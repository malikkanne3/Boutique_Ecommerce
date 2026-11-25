<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Support\Portal;

/** Toutes les transitions de statut d'une commande (un seul endroit, mêmes notifications partout). */
class OrderWorkflow
{
    public static function confirm(Order $order): bool
    {
        if ($order->status !== 'pending') {
            return false;
        }
        $order->update(['status' => 'confirmed']);

        Notifier::notify($order->user_id, '✅ Commande confirmée', "Votre commande {$order->order_number} a été confirmée par {$order->seller_name}.", 'check-circle', 'success', '/my-orders/' . $order->id, $order->seller_id);
        Notifier::mail($order->customer_email, $order->customer_name, '✅ Commande confirmée - ' . $order->order_number,
            '<h2>Votre commande est confirmée !</h2><p>Commande <strong>' . e($order->order_number) . '</strong> — total <strong>' . $order->formatted_total . '</strong>.</p><p>Adresse : ' . e($order->shipping_address) . '</p>');
        return true;
    }

    /** Assigne (ou réassigne après un incident) un livreur disponible. */
    public static function assign(Order $order, User $livreur): bool
    {
        if (! in_array($order->status, ['confirmed', 'assigned', 'problem'], true) || ! $livreur->isLivreur()) {
            return false;
        }
        $order->update([
            'livreur_id' => $livreur->id, 'status' => 'assigned',
            'incident_type' => null, 'incident_note' => null, 'incident_at' => null,
            'delivery_code' => null, 'courier_lat' => null, 'courier_lng' => null, 'courier_updated_at' => null,
        ]);

        Notifier::notify($livreur->id, '🚀 Nouvelle livraison', "La commande {$order->order_number} vous a été assignée.", 'truck', 'warning', Portal::path('livreur', 'dashboard'), auth()->id());
        Notifier::mail($livreur->email, $livreur->name, '🚀 Nouvelle livraison - ' . $order->order_number,
            '<h2>Nouvelle livraison assignée</h2><p>Commande <strong>' . e($order->order_number) . '</strong></p><p>Client : ' . e($order->customer_name) . '<br>Adresse : ' . e($order->shipping_address) . '<br>Téléphone : ' . e((string) $order->customer_phone) . '</p>');
        return true;
    }

    /** $by : 'client' | 'seller' */
    public static function cancel(Order $order, string $by = 'seller'): bool
    {
        if (! in_array($order->status, ['pending', 'confirmed', 'assigned', 'problem'], true)) {
            return false;
        }

        $livreurId = $order->livreur_id;
        $order->loadMissing('products');
        foreach ($order->products as $product) {
            $qty = (int) $product->pivot->quantity;
            Product::withTrashed()->where('id', $product->id)->increment('stock', $qty);
            if ($product->pivot->variant_id) {
                ProductVariant::where('id', $product->pivot->variant_id)->increment('stock', $qty);
            }
        }
        $order->update(['status' => 'cancelled', 'livreur_id' => null, 'delivery_code' => null]);

        if ($by === 'client') {
            Notifier::toSellerSide($order, '❌ Commande annulée', "Le client a annulé la commande {$order->order_number}.", 'x-circle', 'danger', 'order', $order->user_id);
        } else {
            Notifier::notify($order->user_id, '❌ Commande annulée', "Votre commande {$order->order_number} a été annulée.", 'x-circle', 'danger', '/my-orders/' . $order->id, $order->seller_id);
        }
        Notifier::notify($livreurId, '❌ Livraison annulée', "La commande {$order->order_number} a été annulée.", 'x-circle', 'danger', Portal::path('livreur', 'dashboard'));
        return true;
    }

    /** Le livreur accepte : un code de livraison à 4 chiffres est envoyé au client. */
    public static function accept(Order $order): bool
    {
        if ($order->status !== 'assigned') {
            return false;
        }
        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $order->update(['status' => 'delivering', 'delivery_code' => $code]);

        Notifier::notify($order->user_id, '🚚 Votre commande est en route', "Commande {$order->order_number} en livraison. Votre code de livraison : {$code} — donnez-le au livreur à la réception.", 'truck', 'info', '/my-orders/' . $order->id, $order->livreur_id);
        Notifier::mail($order->customer_email, $order->customer_name, '🚚 Votre commande est en route - ' . $order->order_number,
            '<h2>Votre commande arrive !</h2><p>Votre code de livraison : <strong style="font-size:24px;letter-spacing:4px">' . $code . '</strong></p><p>Donnez ce code au livreur à la réception de votre colis. Vous pouvez aussi suivre sa position sur la carte depuis votre espace.</p>');
        Notifier::toSellerSide($order, '🚚 Livraison acceptée', "{$order->livreur?->name} a pris en charge {$order->order_number}.", 'truck', 'info', 'order', $order->livreur_id);
        return true;
    }

    public static function refuse(Order $order): bool
    {
        if ($order->status !== 'assigned') {
            return false;
        }
        $name = $order->livreur?->name ?? 'Le livreur';
        $actor = $order->livreur_id;
        $order->update(['status' => 'confirmed', 'livreur_id' => null]);
        Notifier::toSellerSide($order, '⚠️ Livraison refusée', "{$name} a refusé {$order->order_number}. Assignez un autre livreur.", 'x-circle', 'danger', 'order', $actor);
        return true;
    }

    /** Preuve de livraison validée (code ou photo) : la commande est livrée et la course rémunérée. */
    public static function deliver(Order $order, ?string $proofPath = null): bool
    {
        if (! in_array($order->status, ['assigned', 'delivering'], true)) {
            return false;
        }
        $data = [
            'status' => 'delivered', 'delivered_at' => now(),
            'courier_fee' => (int) config('shop.courier_fee', 1000),
            'courier_lat' => null, 'courier_lng' => null, 'courier_updated_at' => null,
        ];
        if ($proofPath) {
            $data['proof_path'] = $proofPath;
        }
        $order->update($data);

        Notifier::notify($order->user_id, '🎉 Commande livrée', "Votre commande {$order->order_number} a été livrée. Merci ! Pensez à noter votre achat.", 'bag-check', 'success', '/my-orders/' . $order->id, $order->livreur_id);
        Notifier::toSellerSide($order, '📦 Commande livrée', "La commande {$order->order_number} a été livrée.", 'check2-all', 'success', 'order', $order->livreur_id);
        Notifier::mail($order->customer_email, $order->customer_name, '🎉 Commande livrée - ' . $order->order_number,
            '<h2>Commande livrée !</h2><p>Bonjour <strong>' . e($order->customer_name) . '</strong>, votre commande <strong>' . e($order->order_number) . '</strong> a bien été livrée. Merci pour votre confiance !</p>');
        return true;
    }

    /** Le livreur signale un incident : la commande repasse au vendeur, qui peut réassigner ou annuler. */
    public static function reportIncident(Order $order, string $type, ?string $note): bool
    {
        if (! in_array($order->status, ['assigned', 'delivering'], true)) {
            return false;
        }
        $actor = $order->livreur_id;
        $label = Order::INCIDENTS[$type] ?? 'Incident';
        $order->update([
            'status' => 'problem', 'incident_type' => $type, 'incident_note' => $note, 'incident_at' => now(),
            'courier_lat' => null, 'courier_lng' => null, 'courier_updated_at' => null,
        ]);

        Notifier::toSellerSide($order, '⚠️ Problème de livraison', "{$order->order_number} : {$label}. Réassignez un livreur ou contactez le client.", 'exclamation-triangle', 'danger', 'order', $actor);
        Notifier::notify($order->user_id, '⚠️ Souci de livraison', "Le livreur n'a pas pu vous livrer la commande {$order->order_number} ({$label}). Le vendeur va vous recontacter.", 'exclamation-triangle', 'warning', '/my-orders/' . $order->id, $actor);
        return true;
    }
}
