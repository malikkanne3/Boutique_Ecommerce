{{-- Carte de mission : $order, $mode = 'accept' | 'ongoing' | 'done' --}}
<div class="mission rise">
    <div class="mission-top">
        <div><div class="mono">{{ $order->order_number }}</div><small class="text-muted"><i class="bi bi-shop me-1"></i>{{ $order->seller_name }}</small></div>
        <div class="text-end"><x-status-badge :status="$order->status" /><div class="fw-bold mt-1">{{ $order->formatted_total }}</div></div>
    </div>
    <div class="mission-body">
        <div class="mission-line"><span class="flex-shrink-0"><x-avatar :user="$order->user" size="md" /></span><div><small>Client</small><b>{{ $order->customer_name }}</b></div></div>
        <div class="mission-line"><i class="bi bi-geo-alt"></i><div><small>Adresse de livraison</small><b>{{ $order->shipping_address }}</b>@if($order->has_exact_location)<div><span class="pill pill-ok"><i class="bi bi-crosshair2"></i> Position GPS exacte</span></div>@endif @if($order->delivery_note)<div class="small text-muted mt-1"><i class="bi bi-sticky me-1"></i>{{ $order->delivery_note }}</div>@endif</div></div>
        <div class="mission-line"><i class="bi bi-box-seam"></i><div><small>Colis ({{ $order->products->sum('pivot.quantity') }} article(s))</small>
            <span class="small">@foreach($order->products as $p){{ $p->pivot->quantity }}× {{ $p->name }}@if($p->pivot->size || $p->pivot->color) ({{ trim($p->pivot->size.' '.$p->pivot->color) }})@endif @if(! $loop->last), @endif @endforeach</span></div></div>
        <div class="mission-line"><i class="bi bi-cash-coin"></i><div><small>À encaisser — {{ $order->payment_label }}</small><b class="text-accent">{{ $order->formatted_total }}</b></div></div>

        @if($mode !== 'done')
            <div class="row g-2 mb-3">
                <div class="col-6"><a href="tel:{{ $order->customer_phone }}" class="btn btn-ghost w-100"><i class="bi bi-telephone me-1"></i>Appeler</a></div>
                <div class="col-6"><a target="_blank" href="{{ $order->map_url }}" class="btn btn-ghost w-100"><i class="bi bi-map me-1"></i>Itinéraire</a></div>
            </div>
        @endif

        @if($mode !== 'done')
            <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                @if($order->user && auth()->user()->canMessage($order->user))<a href="{{ route('messages.conversation', $order->user) }}" class="btn btn-soft btn-sm"><i class="bi bi-chat-dots me-1"></i>Écrire au client</a>@endif
                @if($order->seller && auth()->user()->canMessage($order->seller))<a href="{{ route('messages.conversation', $order->seller) }}" class="btn btn-soft btn-sm"><i class="bi bi-shop me-1"></i>Écrire au vendeur</a>@endif
                <a href="{{ route('orders.delivery_note', $order) }}" target="_blank" class="btn btn-ghost btn-sm"><i class="bi bi-printer me-1"></i>Bon de livraison</a>
            </div>
        @endif

        @if($mode === 'accept')
            <div class="row g-2">
                <div class="col-8"><form action="{{ route('livreur.accepter', $order) }}" method="POST">@csrf<button class="btn btn-primary btn-big w-100"><i class="bi bi-check2-circle me-1"></i>Accepter</button></form></div>
                <div class="col-4"><form action="{{ route('livreur.refuser', $order) }}" method="POST" data-confirm="Refuser cette mission ?">@csrf<button class="btn btn-outline-danger btn-big w-100"><i class="bi bi-x-lg"></i></button></form></div>
            </div>
        @elseif($mode === 'ongoing')
            <form action="{{ route('livreur.livree', $order) }}" method="POST" enctype="multipart/form-data" data-confirm="Confirmer que le colis a bien été remis au client ?">@csrf
                <div class="p-3 rounded-3 mb-3" style="background:var(--accent-soft)">
                    <div class="fw-bold small mb-2"><i class="bi bi-shield-check me-1"></i>Preuve de livraison</div>
                    @if($order->delivery_code)
                        <input type="text" name="code" inputmode="numeric" maxlength="4" pattern="[0-9]{4}" class="form-control form-control-lg text-center mb-2" placeholder="Code à 4 chiffres" style="letter-spacing:.5rem;font-weight:800" autocomplete="off">
                        <div class="small text-muted text-center mb-2">— ou —</div>
                    @endif
                    <input type="file" name="proof" accept="image/*" capture="environment" class="form-control">
                    <div class="form-text">{{ $order->delivery_code ? 'Photo du colis remis (si le client n\'a pas pu donner son code).' : 'Prenez une photo du colis remis.' }}</div>
                </div>
                <button class="btn btn-primary btn-big w-100"><i class="bi bi-bag-check me-1"></i>Marquer comme livrée</button>
            </form>
        @endif

        @if($mode !== 'done')
            <details class="mt-3">
                <summary class="small fw-bold text-danger" style="cursor:pointer"><i class="bi bi-exclamation-triangle me-1"></i>Signaler un incident</summary>
                <form action="{{ route('livreur.incident', $order) }}" method="POST" class="mt-2" data-confirm="Signaler cet incident ? La commande sera renvoyée au vendeur.">@csrf
                    <select name="type" class="form-select mb-2" required>
                        <option value="">Que se passe-t-il ?</option>
                        @foreach(\App\Models\Order::INCIDENTS as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach
                    </select>
                    <input type="text" name="note" maxlength="500" class="form-control mb-2" placeholder="Précisions (facultatif)">
                    <button class="btn btn-outline-danger w-100">Envoyer le signalement</button>
                </form>
            </details>
        @else
            <small class="text-muted"><i class="bi bi-check-circle-fill text-success me-1"></i>Livrée le {{ $order->delivered_at?->format('d/m/Y à H:i') ?? $order->updated_at->format('d/m/Y') }}</small>
        @endif
    </div>
</div>
