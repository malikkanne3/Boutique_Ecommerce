<?php
namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderWorkflow;
use App\Support\Geo;
use Illuminate\Http\Request;

class LivreurController extends Controller
{
    public function index()
    {
        $me = auth()->user();
        $id = $me->id;

        $toAccept = Order::with(['products', 'seller', 'user'])->where('livreur_id', $id)->where('status', 'assigned')->oldest()->get();
        $ongoing  = Order::with(['products', 'seller', 'user'])->where('livreur_id', $id)->where('status', 'delivering')->oldest()->get();

        $stats = [
            'to_accept' => $toAccept->count(),
            'ongoing'   => $ongoing->count(),
            'today'     => Order::where('livreur_id', $id)->where('status', 'delivered')->whereDate('delivered_at', today())->count(),
            'total'     => Order::where('livreur_id', $id)->where('status', 'delivered')->count(),
            'earned_today' => (int) Order::where('livreur_id', $id)->where('status', 'delivered')->whereDate('delivered_at', today())->sum('courier_fee'),
        ];

        // Tournée conseillée : au plus proche voisin, depuis la dernière position connue
        $fresh = $me->last_lat && $me->last_seen_at && $me->last_seen_at->gt(now()->subHours(12));
        $tour = Geo::tour($ongoing->merge($toAccept), $fresh ? (float) $me->last_lat : null, $fresh ? (float) $me->last_lng : null);

        return view('livreur.dashboard', compact('toAccept', 'ongoing', 'stats', 'tour'));
    }

    /** Historique + gains (aujourd'hui, semaine, mois, total) + récapitulatif hebdomadaire. */
    public function history()
    {
        $id = auth()->id();
        $base = fn () => Order::where('livreur_id', $id)->where('status', 'delivered');

        $earn = [
            'today' => (int) $base()->whereDate('delivered_at', today())->sum('courier_fee'),
            'week'  => (int) $base()->where('delivered_at', '>=', now()->startOfWeek())->sum('courier_fee'),
            'month' => (int) $base()->where('delivered_at', '>=', now()->startOfMonth())->sum('courier_fee'),
            'total' => (int) $base()->sum('courier_fee'),
            'count' => $base()->count(),
        ];

        $since = now()->startOfWeek()->subWeeks(5);
        $byWeek = $base()->where('delivered_at', '>=', $since)->get(['delivered_at', 'courier_fee'])
            ->groupBy(fn ($o) => $o->delivered_at->copy()->startOfWeek()->format('Y-m-d'));

        $weeks = [];
        for ($i = 0; $i < 6; $i++) {
            $start = now()->startOfWeek()->subWeeks($i);
            $rows = $byWeek->get($start->format('Y-m-d'), collect());
            $weeks[] = ['label' => 'Sem. du ' . $start->format('d/m'), 'count' => $rows->count(), 'sum' => (int) $rows->sum('courier_fee')];
        }

        $orders = Order::with(['products', 'seller', 'user'])
            ->where('livreur_id', $id)->where('status', 'delivered')
            ->latest('delivered_at')->paginate(10);

        return view('livreur.history', compact('orders', 'earn', 'weeks'));
    }

    private function mine(Order $order): void
    {
        abort_unless((int) $order->livreur_id === (int) auth()->id(), 403);
    }

    public function accepter(Order $order)
    {
        $this->mine($order);
        $ok = OrderWorkflow::accept($order);
        return back()->with($ok ? 'success' : 'error', $ok ? 'Mission acceptée, bonne route ! 🚚 Activez le partage de position pour que le client vous suive.' : 'Cette mission n\'est plus à accepter.');
    }

    public function refuser(Order $order)
    {
        $this->mine($order);
        $ok = OrderWorkflow::refuse($order);
        return back()->with($ok ? 'success' : 'error', $ok ? 'Mission refusée, le vendeur a été prévenu.' : 'Action impossible.');
    }

    /** Livraison validée par le code à 4 chiffres du client OU par une photo du colis remis. */
    public function livree(Request $request, Order $order)
    {
        $this->mine($order);
        $request->validate([
            'code'  => 'nullable|digits:4',
            'proof' => 'nullable|image|max:5120',
        ]);

        $codeOk   = $request->filled('code') && $order->delivery_code !== null && hash_equals((string) $order->delivery_code, (string) $request->input('code'));
        $hasProof = $request->hasFile('proof');

        if ($order->delivery_code !== null && ! $codeOk && ! $hasProof) {
            return back()->with('error', $request->filled('code')
                ? 'Code incorrect. Demandez à nouveau le code au client, ou prenez une photo du colis remis.'
                : 'Saisissez le code à 4 chiffres donné par le client, ou prenez une photo du colis remis.');
        }

        $path = $hasProof ? $request->file('proof')->store('proofs', 'local') : null;
        $ok = OrderWorkflow::deliver($order, $path);

        return back()->with($ok ? 'success' : 'error', $ok ? 'Livraison confirmée ✅ Merci ! Votre course est comptabilisée.' : 'Action impossible.');
    }

    public function incident(Request $request, Order $order)
    {
        $this->mine($order);
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Order::INCIDENTS)),
            'note' => 'nullable|string|max:500',
        ]);

        $ok = OrderWorkflow::reportIncident($order, $data['type'], $data['note'] ?? null);
        return back()->with($ok ? 'success' : 'error', $ok ? 'Incident signalé : le vendeur et le client ont été prévenus.' : 'Action impossible.');
    }

    /** Position en direct (envoyée toutes les ~10 s par le navigateur du livreur). */
    public function position(Request $request)
    {
        $data = $request->validate(['lat' => 'required|numeric|between:-90,90', 'lng' => 'required|numeric|between:-180,180']);
        $me = auth()->user();

        $me->forceFill(['last_lat' => $data['lat'], 'last_lng' => $data['lng'], 'last_seen_at' => now()])->save();

        Order::where('livreur_id', $me->id)->where('status', 'delivering')
            ->update(['courier_lat' => $data['lat'], 'courier_lng' => $data['lng'], 'courier_updated_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function availability()
    {
        $me = auth()->user();
        $me->update(['is_available' => ! $me->is_available]);

        return back()->with('success', $me->is_available
            ? 'Vous êtes maintenant DISPONIBLE : les vendeurs peuvent vous assigner des livraisons.'
            : 'Vous êtes INDISPONIBLE : plus aucune livraison ne vous sera assignée.');
    }
}
