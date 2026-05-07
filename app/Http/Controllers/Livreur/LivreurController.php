<?php
namespace App\Http\Controllers\Livreur;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Helpers\BrevoMail;
use App\Helpers\NotifHelper;

class LivreurController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $commandes = Order::with(['products'])
            ->where('livreur_id', $userId)
            ->latest()
            ->get();
        $stats = [
            'total'      => $commandes->count(),
            'en_attente' => $commandes->where('status', 'confirmed')->count(),
            'en_cours'   => $commandes->where('status', 'delivering')->count(),
            'livrees'    => $commandes->where('status', 'delivered')->count(),
        ];
        return view('livreur.dashboard', compact('commandes', 'stats'));
    }

    public function accepter(Order $order)
    {
        if ($order->livreur_id !== auth()->id()) abort(403);
        $order->update(['status' => 'delivering']);
        return redirect()->back()->with('success', 'Commande acceptée ! Bonne livraison 🚚');
    }

    public function refuser(Order $order)
    {
        if ($order->livreur_id !== auth()->id()) abort(403);
        $order->update(['status' => 'confirmed', 'livreur_id' => null]);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            NotifHelper::send(
                $admin->id,
                '❌ Livraison refusée',
                'Le livreur ' . auth()->user()->name . ' a refusé la commande ' . $order->order_number,
                'x-circle',
                'danger',
                '/admin/orders/' . $order->id
            );
        }
        return redirect()->back()->with('success', 'Commande refusée, elle retourne en attente.');
    }

    public function livree(Order $order)
    {
        if ($order->livreur_id !== auth()->id()) abort(403);

        $order->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        // Notification interne à l'admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            NotifHelper::send(
                $admin->id,
                '📦 Commande livrée !',
                'La commande ' . $order->order_number . ' a été livrée par ' . auth()->user()->name,
                'check2-all',
                'info',
                '/admin/orders/' . $order->id
            );
        }

        // Notification interne au client
        if ($order->user_id) {
            NotifHelper::send(
                $order->user_id,
                '🎉 Commande livrée !',
                'Votre commande ' . $order->order_number . ' a été livrée avec succès.',
                'bag-check',
                'success',
                '/my-orders/' . $order->id
            );
        }

        // Email à l'admin
        BrevoMail::send(
            env('ADMIN_EMAIL'),
            'Admin',
            '📦 Commande livrée - ' . $order->order_number,
            '<h2>Commande livrée !</h2>
            <p>La commande <strong>' . $order->order_number . '</strong> a été livrée par <strong>' . auth()->user()->name . '</strong>.</p>
            <p>Client : ' . $order->customer_name . '</p>'
        );

        // Email au client
        BrevoMail::send(
            $order->customer_email,
            $order->customer_name,
            '🎉 Votre commande a été livrée - ' . $order->order_number,
            '<h2>Commande livrée !</h2>
            <p>Bonjour <strong>' . $order->customer_name . '</strong>,</p>
            <p>Votre commande <strong>' . $order->order_number . '</strong> a été livrée avec succès.</p>
            <p>Merci pour votre confiance !</p>'
        );

        return redirect()->back()->with('success', 'Commande marquée comme livrée ✅');
    }
}