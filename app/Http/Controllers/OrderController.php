<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Services\OrderWorkflow;
use App\Support\Cart;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::with(['products', 'seller'])->where('user_id', auth()->id())->latest();

        if (request('status') === 'active') {
            $query->whereIn('status', Order::ACTIVE);
        } elseif (request('status')) {
            $query->where('status', request('status'));
        }

        $orders = $query->paginate(8)->withQueryString();
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if((int) $order->user_id !== (int) auth()->id(), 403);
        $order->load(['products.category', 'seller', 'livreur']);

        // Avis déjà donnés par ce client (produits de la commande + vendeur)
        $productReviews = Review::where('user_id', auth()->id())->whereIn('product_id', $order->products->pluck('id'))->get()->keyBy('product_id');
        $sellerReview = $order->seller_id
            ? Review::where('user_id', auth()->id())->where('seller_id', $order->seller_id)->where('order_id', $order->id)->whereNull('product_id')->first()
            : null;
        $dispute = $order->disputes()->latest()->first();
        $canDispute = $order->status === 'delivered'
            && ! $order->disputes()->where('status', '!=', 'resolved')->exists()
            && (($order->delivered_at ?: $order->updated_at) === null || ($order->delivered_at ?: $order->updated_at)->gte(now()->subDays((int) config('shop.dispute_days', 14))));

        return view('orders.show', compact('order', 'productReviews', 'sellerReview', 'dispute', 'canDispute'));
    }

    /** Position du livreur (JSON) pour la carte de suivi du client. */
    public function position(Order $order)
    {
        abort_if((int) $order->user_id !== (int) auth()->id(), 403);

        $live = $order->status === 'delivering';
        return response()->json([
            'status'  => $order->status,
            'live'    => $live,
            'courier' => ($live && $order->courier_lat !== null) ? [
                'lat' => (float) $order->courier_lat, 'lng' => (float) $order->courier_lng,
                'age' => $order->courier_updated_at ? $order->courier_updated_at->diffInSeconds(now()) : null,
            ] : null,
            'destination' => $order->has_exact_location ? ['lat' => (float) $order->delivery_lat, 'lng' => (float) $order->delivery_lng] : null,
            'livreur' => $order->livreur ? $order->livreur->name : null,
        ]);
    }

    /** « Racheter » : remet les mêmes articles dans le panier (dans la limite du stock). */
    public function reorder(Order $order)
    {
        abort_if((int) $order->user_id !== (int) auth()->id(), 403);
        $order->load('products.variants');

        $cart = session()->get('cart', []);
        $added = 0;
        $missing = [];

        foreach ($order->products as $product) {
            if (! $product->is_active || ! $product->is_available || $product->stock < 1) {
                $missing[] = $product->name;
                continue;
            }

            $variantId = 0;
            $stock = (int) $product->stock;
            if ($product->variants->isNotEmpty()) {
                $variant = $product->variants->firstWhere('id', (int) $product->pivot->variant_id)
                    ?: $product->variants->first(fn ($v) => $v->size === $product->pivot->size && $v->color === $product->pivot->color);
                if (! $variant || $variant->stock < 1) {
                    $missing[] = $product->name . ($product->pivot->size || $product->pivot->color ? ' (' . trim($product->pivot->size . ' ' . $product->pivot->color) . ')' : '');
                    continue;
                }
                $variantId = $variant->id;
                $stock = (int) $variant->stock;
            }

            $key = Cart::key($product->id, $variantId);
            $cart[$key] = ['quantity' => min((int) ($cart[$key]['quantity'] ?? 0) + (int) $product->pivot->quantity, $stock)];
            $added++;
        }

        session()->put('cart', $cart);

        $redirect = redirect()->route('cart.index');
        if ($added > 0) {
            $redirect->with('success', $added . ' article(s) remis dans votre panier.');
        }
        if (! empty($missing)) {
            $redirect->with('error', 'Plus disponible : ' . implode(', ', $missing) . '.');
        }
        return $redirect;
    }

    public function cancel(Order $order)
    {
        abort_if((int) $order->user_id !== (int) auth()->id(), 403);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Seule une commande en attente peut être annulée.');
        }
        OrderWorkflow::cancel($order, 'client');
        return back()->with('success', 'Votre commande a été annulée.');
    }
}
