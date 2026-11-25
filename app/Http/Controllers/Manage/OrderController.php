<?php
namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderWorkflow;

/** Gestion des commandes : le vendeur gère les siennes, l'admin supervise tout. */
class OrderController extends Controller
{
    private function prefix(): string
    {
        return auth()->user()->isAdmin() ? 'admin' : 'vendeur';
    }

    private function scoped()
    {
        $q = Order::query();
        if (! auth()->user()->isAdmin()) {
            $q->where('seller_id', auth()->id());
        }
        return $q;
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless(auth()->user()->isAdmin() || (int) $order->seller_id === (int) auth()->id(), 403);
    }

    private function livreurs()
    {
        return User::where('role', 'livreur')
            ->withCount(['deliveries as active_count' => fn ($q) => $q->whereIn('status', ['assigned', 'delivering'])])
            ->orderBy('name')->get();
    }

    public function index()
    {
        $query = $this->scoped()->with(['user', 'livreur', 'seller'])->latest();

        if ($s = request('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', '%' . $s . '%')
                  ->orWhere('customer_name', 'like', '%' . $s . '%');
            });
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }

        $orders   = $query->paginate(12)->withQueryString();
        $counts   = $this->scoped()->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $statuses = Order::STATUSES;
        $livreurs = $this->livreurs();

        return view('manage.orders.index', ['prefix' => $this->prefix()] + compact('orders', 'counts', 'statuses', 'livreurs'));
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['products.category', 'livreur', 'seller', 'user']);
        $livreurs = $this->livreurs();

        // Autres vendeurs concernés par le même panier du client (pour discuter avec eux)
        $siblings = ($order->checkout_ref && ! auth()->user()->isAdmin())
            ? $order->siblings()->with(['products', 'seller'])->whereNotNull('seller_id')->where('seller_id', '!=', auth()->id())->get()
            : collect();

        return view('manage.orders.show', ['prefix' => $this->prefix()] + compact('order', 'livreurs', 'siblings'));
    }

    /** Note interne : visible uniquement par le vendeur. */
    public function note(Order $order)
    {
        abort_if(auth()->user()->isAdmin(), 403);
        $this->authorizeOrder($order);

        $data = request()->validate(['seller_note' => 'nullable|string|max:1000']);
        $order->update(['seller_note' => $data['seller_note'] ?? null]);

        return back()->with('success', 'Note enregistrée.');
    }

    public function update(Order $order)
    {
        $this->authorizeOrder($order);
        $action = request('action');

        if ($action === 'confirm') {
            $ok  = OrderWorkflow::confirm($order);
            $msg = 'Commande confirmée, le client a été prévenu.';
        } elseif ($action === 'assign') {
            $livreur = User::where('role', 'livreur')->find(request('livreur_id'));
            if (! $livreur) {
                return back()->with('error', 'Choisissez un livreur.');
            }
            if (! $livreur->is_available) {
                return back()->with('error', "{$livreur->name} est actuellement indisponible. Choisissez un autre livreur.");
            }
            $ok  = OrderWorkflow::assign($order, $livreur);
            $msg = "Commande assignée à {$livreur->name}.";
        } elseif ($action === 'cancel') {
            $ok  = OrderWorkflow::cancel($order, 'seller');
            $msg = 'Commande annulée, le stock a été remis.';
        } else {
            abort(422);
        }

        return back()->with($ok ? 'success' : 'error', $ok ? $msg : 'Cette action n\'est plus possible pour le statut actuel de la commande.');
    }
}
