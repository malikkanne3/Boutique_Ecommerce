<?php
namespace App\Http\Controllers;
use App\Helpers\BrevoMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Helpers\NotifHelper;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');
        return view('checkout.index', compact('cart'));
    }

    public function store()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');

        request()->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email',
            'customer_phone'   => 'nullable|string|max:20',
            'shipping_address' => 'required|string',
        ]);

        $order = null;

        DB::transaction(function () use ($cart, &$order) {
            $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
            $order = Order::create([
                'user_id'          => auth()->id(),
                'order_number'     => 'CMD-' . strtoupper(uniqid()),
                'total_amount'     => $total,
                'status'           => 'pending',
                'customer_name'    => request('customer_name'),
                'customer_email'   => request('customer_email'),
                'customer_phone'   => request('customer_phone'),
                'shipping_address' => request('shipping_address'),
            ]);

            foreach ($cart as $id => $item) {
                $order->products()->attach($id, [
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                ]);
                Product::find($id)?->decrement('stock', $item['quantity']);
            }

            session()->forget('cart');
        });

        // Notification interne à l'admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            NotifHelper::send(
                $admin->id,
                '🛒 Nouvelle commande !',
                'Commande ' . $order->order_number . ' de ' . $order->customer_name,
                'cart',
                'primary',
                '/admin/orders/' . $order->id
            );
        }

        // Email à l'admin
        BrevoMail::send(
    env('ADMIN_EMAIL'),
    'Admin',
    '🛒 Nouvelle commande - ' . $order->order_number,
    '<h2>Nouvelle commande reçue !</h2>
    <p>Commande <strong>' . $order->order_number . '</strong> de <strong>' . $order->customer_name . '</strong></p>
    <p>Total : <strong>' . $order->formatted_total . '</strong></p>
    <p>Adresse : ' . $order->shipping_address . '</p>'
);

        return redirect()->route('orders.index')
            ->with('success', '🎉 Commande passée avec succès ! Nous vous contacterons rapidement.');
    }
}