<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Helpers\BrevoMail;
use App\Helpers\NotifHelper;

class AdminOrderController extends Controller
{
    public function index()
    {
        $query = Order::with(['user', 'livreur'])->latest();

        if (request('search')) {
            $query->where(function($q) {
                $q->where('order_number', 'like', '%'.request('search').'%')
                  ->orWhere('customer_name', 'like', '%'.request('search').'%');
            });
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }

        $orders   = $query->paginate(20);
        $statuses = Order::STATUSES;
        $livreurs = User::where('role', 'livreur')->get();

        return view('admin.orders.index', compact('orders', 'statuses', 'livreurs'));
    }

    public function show(Order $order)
    {
        $order->load('products.category', 'livreur');
        $statuses = Order::STATUSES;
        $livreurs = User::where('role', 'livreur')->get();
        return view('admin.orders.show', compact('order', 'statuses', 'livreurs'));
    }

    public function update(Order $order)
    {
        $data = ['status' => request('status')];

        if (request('livreur_id')) {
            $data['livreur_id'] = request('livreur_id');
            $data['status']     = 'delivering';
            $order->update($data);
            $order->load('livreur');

            // Notification interne au livreur
            NotifHelper::send(
                $order->livreur->id,
                '🚀 Nouvelle livraison !',
                'Commande ' . $order->order_number . ' vous a été assignée.',
                'truck',
                'warning',
                '/livreur'
            );

            // Email au livreur
            BrevoMail::send(
    $order->livreur->email,
    $order->livreur->name,
    '🚀 Nouvelle livraison - ' . $order->order_number,
    '<h2>Nouvelle livraison assignée !</h2>
    <p>Bonjour <strong>' . $order->livreur->name . '</strong>,</p>
    <p>La commande <strong>' . $order->order_number . '</strong> vous a été assignée.</p>
    <p>Client : ' . $order->customer_name . '</p>
    <p>Adresse : ' . $order->shipping_address . '</p>
    <p>Téléphone : ' . $order->customer_phone . '</p>'
);

        } else {
            $order->update($data);

            if (request('status') === 'confirmed') {
                // Notification interne au client
                if ($order->user_id) {
                    NotifHelper::send(
                        $order->user_id,
                        '✅ Commande confirmée !',
                        'Votre commande ' . $order->order_number . ' a été confirmée.',
                        'check-circle',
                        'success',
                        '/my-orders/' . $order->id
                    );
                }

                // Email au client
                BrevoMail::send(
    $order->customer_email,
    $order->customer_name,
    '✅ Commande confirmée - ' . $order->order_number,
    '<h2>Votre commande est confirmée !</h2>
    <p>Bonjour <strong>' . $order->customer_name . '</strong>,</p>
    <p>Votre commande <strong>' . $order->order_number . '</strong> a été confirmée.</p>
    <p>Total : <strong>' . $order->formatted_total . '</strong></p>
    <p>Adresse de livraison : ' . $order->shipping_address . '</p>'
);
            }
        }

        return redirect()->back()->with('success', 'Commande mise à jour !');
    }
}