<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

/** Facture et bon de livraison imprimables (impression ou « Enregistrer en PDF » du navigateur). */
class InvoiceController extends Controller
{
    public function invoice(Order $order)
    {
        $me = auth()->user();
        abort_unless(
            $me->isAdmin() || (int) $order->user_id === $me->id || ($order->seller_id && (int) $order->seller_id === $me->id),
            403
        );
        $order->load(['products', 'seller', 'user']);

        return view('orders.invoice', compact('order'));
    }

    /** Photo de preuve de livraison (privée). */
    public function proof(Order $order)
    {
        $me = auth()->user();
        abort_unless(
            $me->isAdmin() || (int) $order->user_id === $me->id
            || ($order->seller_id && (int) $order->seller_id === $me->id)
            || ($order->livreur_id && (int) $order->livreur_id === $me->id),
            403
        );
        abort_unless($order->proof_path && Storage::disk('local')->exists($order->proof_path), 404);

        return response()->file(Storage::disk('local')->path($order->proof_path), ['X-Content-Type-Options' => 'nosniff']);
    }

    public function deliveryNote(Order $order)
    {
        $me = auth()->user();
        abort_unless(
            $me->isAdmin()
            || ($order->seller_id && (int) $order->seller_id === $me->id)
            || ($order->livreur_id && (int) $order->livreur_id === $me->id),
            403
        );
        $order->load(['products', 'seller', 'livreur']);

        return view('orders.delivery-note', compact('order'));
    }
}
