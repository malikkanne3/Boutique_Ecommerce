<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Notifier;
use App\Support\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /** Étape 2 : vérification des informations, localisation et mode de paiement. */
    public function index()
    {
        [$items, $total] = Cart::items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $groups   = $items->groupBy(fn ($i) => $i->product->seller_id ?? 0);
        $savings  = $items->sum('saved');
        $methods  = config('shop.payment_methods');

        return view('checkout.index', compact('items', 'total', 'groups', 'savings', 'methods'));
    }

    public function store()
    {
        [$items] = Cart::items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $data = request()->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_phone'   => 'required|string|max:30',
            'shipping_address' => 'nullable|string|max:500',
            'delivery_lat'     => 'nullable|numeric|between:-90,90',
            'delivery_lng'     => 'nullable|numeric|between:-180,180',
            'delivery_note'    => 'nullable|string|max:255',
            'payment_method'   => 'required|in:cash,wave,orange_money',
        ]);

        $hasLocation = $data['delivery_lat'] !== null && $data['delivery_lng'] !== null;
        if (empty($data['shipping_address']) && ! $hasLocation) {
            return back()->withInput()->withErrors(['shipping_address' => 'Indiquez votre adresse de livraison ou partagez votre position exacte.']);
        }
        if (empty($data['shipping_address'])) {
            $data['shipping_address'] = 'Position GPS : ' . $data['delivery_lat'] . ', ' . $data['delivery_lng'];
        }

        // Une commande par vendeur : chacun gère et expédie ses propres produits
        $groups = $items->groupBy(fn ($i) => $i->product->seller_id ?? 0);
        $orders = [];
        $checkoutRef = Str::random(20); // relie les commandes d'un même panier (utile à la messagerie entre vendeurs)

        try {
            DB::transaction(function () use ($groups, $data, &$orders, $checkoutRef) {
                foreach ($groups as $sellerKey => $lines) {
                    $order = Order::create([
                        'user_id'          => auth()->id(),
                        'seller_id'        => $sellerKey ?: null,
                        'order_number'     => 'CMD-' . strtoupper(Str::random(8)),
                        'total_amount'     => $lines->sum('subtotal'),
                        'status'           => 'pending',
                        'customer_name'    => $data['customer_name'],
                        'customer_email'   => $data['customer_email'],
                        'customer_phone'   => $data['customer_phone'],
                        'shipping_address' => $data['shipping_address'],
                        'delivery_lat'     => $data['delivery_lat'] ?? null,
                        'delivery_lng'     => $data['delivery_lng'] ?? null,
                        'delivery_note'    => $data['delivery_note'] ?? null,
                        'payment_method'   => $data['payment_method'],
                        'payment_status'   => 'pending',
                        'checkout_ref'     => $checkoutRef,
                    ]);

                    foreach ($lines as $line) {
                        $qty = (int) $line->quantity;

                        if ($line->variant) {
                            $done = ProductVariant::where('id', $line->variant->id)
                                ->where('stock', '>=', $qty)->decrement('stock', $qty);
                            if ($done < 1) {
                                throw new \RuntimeException($line->product->name . ' (' . $line->variant->label . ')');
                            }
                            Product::where('id', $line->product->id)->decrement('stock', $qty);
                        } else {
                            $done = Product::where('id', $line->product->id)
                                ->where('stock', '>=', $qty)->decrement('stock', $qty);
                            if ($done < 1) {
                                throw new \RuntimeException($line->product->name);
                            }
                        }

                        $order->products()->attach($line->product->id, [
                            'quantity'   => $qty,
                            'price'      => $line->unit_price,
                            'variant_id' => $line->variant ? $line->variant->id : null,
                            'size'       => $line->variant ? $line->variant->size : null,
                            'color'      => $line->variant ? $line->variant->color : null,
                        ]);
                    }
                    $orders[] = $order;
                }
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Stock insuffisant pour « ' . $e->getMessage() . ' ». Vérifiez votre panier.');
        }

        session()->forget('cart');
        session()->put('last_orders', collect($orders)->pluck('id')->all());

        $payLabel = config('shop.payment_methods.' . $data['payment_method'] . '.label');

        foreach ($orders as $order) {
            $order->load('seller');
            Notifier::toSellerSide($order, '🛒 Nouvelle commande !', "Commande {$order->order_number} de {$order->customer_name} ({$order->formatted_total}) — {$payLabel}.", 'cart', 'primary', 'order', $order->user_id);
            [$email, $name] = Notifier::sellerEmail($order);
            Notifier::mail($email, $name, '🛒 Nouvelle commande - ' . $order->order_number,
                '<h2>Nouvelle commande reçue</h2><p>Commande <strong>' . e($order->order_number) . '</strong> de <strong>' . e($order->customer_name) . '</strong></p>'
                . '<p>Total : <strong>' . $order->formatted_total . '</strong> — ' . e($payLabel) . '<br>Adresse : ' . e($order->shipping_address) . '</p>');

            Notifier::notify($order->user_id, '🧾 Commande enregistrée', "Votre commande {$order->order_number} est en attente de confirmation.", 'receipt', 'primary', '/my-orders/' . $order->id);
            Notifier::mail($order->customer_email, $order->customer_name, 'Commande reçue - ' . $order->order_number,
                '<h2>Merci pour votre commande !</h2><p>Commande <strong>' . e($order->order_number) . '</strong> — ' . $order->formatted_total . ' (' . e($payLabel) . ').</p><p>Le vendeur va la confirmer très bientôt.</p>');
        }

        return redirect()->route('checkout.success');
    }

    /** Étape 3 : confirmation. */
    public function success()
    {
        $ids = session('last_orders', []);
        if (empty($ids)) {
            return redirect()->route('orders.index');
        }

        $orders = Order::with(['products', 'seller'])->whereIn('id', $ids)->where('user_id', auth()->id())->get();
        if ($orders->isEmpty()) {
            return redirect()->route('orders.index');
        }

        return view('checkout.success', [
            'orders'  => $orders,
            'methods' => config('shop.payment_methods'),
            'wave'    => config('shop.wave_number'),
            'om'      => config('shop.orange_money_number'),
        ]);
    }
}
