<?php
namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\Order;
use App\Models\User;
use App\Services\Notifier;
use App\Support\ChatPayload;

/**
 * Retours et litiges : le client signale un problème sur une commande livrée,
 * le vendeur répond, l'admin arbitre si besoin.
 */
class DisputeController extends Controller
{
    private function role(Dispute $d): string
    {
        $me = auth()->user();
        if ($me->isAdmin()) {
            return 'admin';
        }
        if ((int) $d->user_id === $me->id) {
            return 'client';
        }
        if ($d->seller_id && (int) $d->seller_id === $me->id) {
            return 'seller';
        }
        abort(403);
    }

    private function window(): int
    {
        return (int) config('shop.dispute_days', 14);
    }

    /** Prévient l'autre partie ; sans vendeur (boutique officielle), ce sont les admins. */
    private function notifySellerSide(Dispute $d, string $title, string $body): void
    {
        $link = '/litiges/' . $d->id;
        if ($d->seller_id) {
            Notifier::notify($d->seller_id, $title, $body, 'exclamation-octagon', 'warning', $link, auth()->id());
            return;
        }
        foreach (User::where('role', 'admin')->where('status', 'active')->pluck('id') as $adminId) {
            Notifier::notify($adminId, $title, $body, 'exclamation-octagon', 'warning', $link, auth()->id());
        }
    }

    private function notifyAdmins(Dispute $d, string $title, string $body): void
    {
        foreach (User::where('role', 'admin')->where('status', 'active')->pluck('id') as $adminId) {
            Notifier::notify($adminId, $title, $body, 'shield-exclamation', 'danger', '/litiges/' . $d->id, auth()->id());
        }
    }

    public function index()
    {
        $me = auth()->user();
        $query = Dispute::with(['order', 'client', 'seller'])->latest();

        if ($me->isAdmin()) {
            // tous les litiges
        } elseif ($me->isVendeur()) {
            $query->where('seller_id', $me->id);
        } else {
            $query->where('user_id', $me->id);
        }

        $base = clone $query;
        $counts = $base->reorder()->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $disputes = $query->paginate(10)->withQueryString();

        return view('disputes.index', compact('disputes', 'counts'));
    }

    public function create(Order $order)
    {
        $this->authorizeOrder($order);
        if ($error = $this->cannotOpen($order)) {
            return redirect()->route('orders.show', $order)->with('error', $error);
        }
        return view('disputes.create', ['order' => $order->load('products')]);
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);
    }

    private function cannotOpen(Order $order): ?string
    {
        if ($order->status !== 'delivered') {
            return 'Vous ne pouvez signaler un problème que sur une commande livrée.';
        }
        $since = $order->delivered_at ?: $order->updated_at;
        if ($since && $since->lt(now()->subDays($this->window()))) {
            return 'Le délai de ' . $this->window() . ' jours pour signaler un problème est dépassé.';
        }
        if ($order->disputes()->where('status', '!=', 'resolved')->exists()) {
            return 'Un litige est déjà en cours pour cette commande.';
        }
        return null;
    }

    public function store(Order $order)
    {
        $this->authorizeOrder($order);
        if ($error = $this->cannotOpen($order)) {
            return redirect()->route('orders.show', $order)->with('error', $error);
        }

        $data = request()->validate([
            'reason'      => 'required|in:' . implode(',', array_keys(Dispute::REASONS)),
            'description' => 'required|string|min:10|max:2000',
            'file'        => ChatPayload::rules()['file'],
        ]);

        $dispute = Dispute::create([
            'order_id'    => $order->id,
            'user_id'     => auth()->id(),
            'seller_id'   => $order->seller_id,
            'reason'      => $data['reason'],
            'description' => $data['description'],
            'status'      => 'open',
        ]);

        $message = ['dispute_id' => $dispute->id, 'user_id' => auth()->id(), 'body' => $data['description'], 'type' => 'text'];
        if (request()->hasFile('file')) {
            $payload = ChatPayload::build(request());
            $message = array_merge($message, array_intersect_key($payload, array_flip(['type', 'attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size'])));
        }
        DisputeMessage::create($message);

        $this->notifySellerSide($dispute, '⚠️ Nouveau litige', auth()->user()->name . ' signale un problème sur ' . $order->order_number . ' (' . $dispute->reason_label . ').');
        if ($order->seller) {
            Notifier::mail($order->seller->email, $order->seller->display_name, 'Litige sur la commande ' . $order->order_number,
                '<h2>Un client signale un problème</h2><p>Commande <strong>' . e($order->order_number) . '</strong> — ' . e($dispute->reason_label) . '</p><p>' . nl2br(e($dispute->description)) . '</p><p>Merci de répondre depuis votre espace, rubrique Litiges.</p>');
        }

        return redirect()->route('disputes.show', $dispute)->with('success', 'Votre signalement a été envoyé. Le vendeur va vous répondre.');
    }

    public function show(Dispute $dispute)
    {
        $role = $this->role($dispute);
        $dispute->load(['order.products', 'client', 'seller', 'messages.user']);

        return view('disputes.show', compact('dispute', 'role'));
    }

    public function reply(Dispute $dispute)
    {
        $role = $this->role($dispute);
        if (! $dispute->is_open) {
            return back()->with('error', 'Ce litige est clôturé.');
        }

        request()->validate(['body' => 'nullable|string|max:2000', 'file' => ChatPayload::rules()['file']]);
        $payload = ChatPayload::build(request());
        if (! $payload || ($payload['type'] === 'location')) {
            return back()->with('error', 'Écrivez un message ou joignez un fichier.');
        }

        DisputeMessage::create(['dispute_id' => $dispute->id, 'user_id' => auth()->id()] + array_intersect_key($payload, array_flip(['body', 'type', 'attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size'])));

        if ($role === 'seller' && $dispute->status === 'open') {
            $dispute->update(['status' => 'seller_replied']);
        }

        $link = '/litiges/' . $dispute->id;
        $preview = $payload['body'] !== '' ? mb_strimwidth($payload['body'], 0, 90, '…') : '📎 Pièce jointe';
        if ($role !== 'client') {
            Notifier::notify($dispute->user_id, '💬 Réponse sur votre litige', $preview, 'chat-dots', 'info', $link, auth()->id());
        }
        if ($role === 'client') {
            $this->notifySellerSide($dispute, '💬 Le client a répondu', $preview);
        }
        if ($role === 'admin' && $dispute->seller_id) {
            Notifier::notify($dispute->seller_id, '💬 Message de l\'administration', $preview, 'shield-check', 'info', $link, auth()->id());
        }

        return back();
    }

    /** Le client demande l'arbitrage de l'administration. */
    public function escalate(Dispute $dispute)
    {
        abort_unless($this->role($dispute) === 'client', 403);
        if (! in_array($dispute->status, ['open', 'seller_replied'], true)) {
            return back()->with('error', 'Ce litige ne peut pas être transmis à l\'administration.');
        }
        $dispute->update(['status' => 'escalated']);

        $this->notifyAdmins($dispute, '⚖️ Litige à arbitrer', 'Le client ' . auth()->user()->name . ' demande l\'arbitrage pour la commande ' . $dispute->order->order_number . '.');
        if ($dispute->seller_id) {
            Notifier::notify($dispute->seller_id, '⚖️ Litige transmis à l\'administration', 'Commande ' . $dispute->order->order_number, 'shield-exclamation', 'warning', '/litiges/' . $dispute->id, auth()->id());
        }

        return back()->with('success', 'Votre litige a été transmis à l\'administration.');
    }

    /** Le client indique que le problème est réglé. */
    public function close(Dispute $dispute)
    {
        abort_unless($this->role($dispute) === 'client', 403);
        if (! $dispute->is_open) {
            return back();
        }
        $dispute->update(['status' => 'resolved', 'resolution' => 'agreement', 'resolved_at' => now()]);
        $this->notifySellerSide($dispute, '✅ Litige résolu', 'Le client a indiqué que le problème est réglé (' . $dispute->order->order_number . ').');

        return back()->with('success', 'Litige clôturé. Merci !');
    }

    /** Décision de l'administrateur. */
    public function decide(Dispute $dispute)
    {
        abort_unless($this->role($dispute) === 'admin', 403);
        if (! $dispute->is_open) {
            return back()->with('error', 'Ce litige est déjà clôturé.');
        }

        $data = request()->validate([
            'resolution'     => 'required|in:refund,exchange,no_action',
            'admin_decision' => 'required|string|min:5|max:2000',
        ]);

        $dispute->update($data + ['status' => 'resolved', 'resolved_at' => now()]);

        $label = Dispute::RESOLUTIONS[$data['resolution']];
        $link = '/litiges/' . $dispute->id;
        Notifier::notify($dispute->user_id, '⚖️ Décision sur votre litige', $label, 'shield-check', 'success', $link, auth()->id());
        if ($dispute->seller_id) {
            Notifier::notify($dispute->seller_id, '⚖️ Décision de l\'administration', $label, 'shield-check', 'success', $link, auth()->id());
        }
        Notifier::mail($dispute->client->email, $dispute->client->name, 'Décision sur votre litige — ' . $dispute->order->order_number,
            '<h2>Votre litige a été examiné</h2><p>Décision : <strong>' . e($label) . '</strong></p><p>' . nl2br(e($data['admin_decision'])) . '</p>');

        return back()->with('success', 'Décision enregistrée et notifiée aux deux parties.');
    }
}
