@extends('layouts.dashboard')
@section('title', 'Commande ' . $order->order_number)

@section('content')
<x-page-head :title="'Commande '.$order->order_number" :subtitle="'Passée le '.$order->created_at->format('d/m/Y à H:i')">
    <x-slot:actions>
        <a href="{{ route('orders.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Mes commandes</a>
        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="btn btn-ghost"><i class="bi bi-file-earmark-text me-1"></i>Facture</a>
        @if(in_array($order->status, ['delivered', 'cancelled']))
            <form action="{{ route('orders.reorder', $order) }}" method="POST">@csrf<button class="btn btn-soft"><i class="bi bi-arrow-repeat me-1"></i>Racheter</button></form>
        @endif
        @if($canDispute)<a href="{{ route('disputes.create', $order) }}" class="btn btn-outline-danger"><i class="bi bi-exclamation-octagon me-1"></i>Signaler un problème</a>@endif
        @if($order->status === 'pending')
            <form action="{{ route('orders.cancel', $order) }}" method="POST" data-confirm="Annuler cette commande ?">@csrf
                <button class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Annuler</button></form>
        @endif
    </x-slot:actions>
</x-page-head>

<div class="ui-card mb-4 rise"><div class="ui-card-body">
    <div class="d-flex justify-content-between align-items-center mb-3"><x-status-badge :status="$order->status" />
        @if($order->delivered_at)<small class="text-muted">Livrée le {{ $order->delivered_at->format('d/m/Y à H:i') }}</small>@endif</div>
    <x-order-steps :order="$order" />
</div></div>

@if($order->status === 'delivering' && $order->delivery_code)
    <div class="ui-card mb-4 rise text-center p-4" style="border:2px dashed var(--accent)">
        <div class="text-muted small"><i class="bi bi-shield-lock me-1"></i>Votre code de livraison</div>
        <div style="font-size:3.2rem;font-weight:800;letter-spacing:.7rem;color:var(--accent);line-height:1.1;padding-left:.7rem">{{ $order->delivery_code }}</div>
        <div class="small text-muted">Donnez ce code au livreur <b>uniquement à la réception</b> de votre colis.</div>
    </div>
@endif

@if($order->status === 'delivering')
    <div class="ui-card mb-4 rise">
        <div class="ui-card-head"><span><i class="bi bi-geo-alt-fill text-accent me-2"></i>Suivi en direct</span><small id="track-status">Chargement…</small></div>
        <div id="track-map" style="height:340px;border-radius:0 0 16px 16px"></div>
    </div>
@endif

@if($order->status === 'delivered' && $order->proof_path)
    <div class="ui-card mb-4 rise"><div class="ui-card-body d-flex gap-3 align-items-center">
        <a href="{{ route('orders.proof', $order) }}" target="_blank"><img src="{{ route('orders.proof', $order) }}" alt="Preuve de livraison" style="width:90px;height:90px;object-fit:cover;border-radius:14px"></a>
        <div><b><i class="bi bi-camera-fill text-accent me-1"></i>Photo de livraison</b><div class="small text-muted">Prise par le livreur au moment de la remise du colis.</div></div>
    </div></div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ui-card rise">
            <div class="ui-card-head">Articles commandés</div>
            @foreach($order->products as $p)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <img class="thumb" style="width:60px;height:60px" src="{{ $p->image_url }}" alt="">
                    <div class="flex-grow-1"><a class="fw-bold text-reset" href="{{ route('shop.show', $p) }}">{{ $p->name }}</a><div class="small text-muted">@if($p->pivot->size)<span class="var-chip">{{ $p->pivot->size }}</span>@endif @if($p->pivot->color)<span class="var-chip">{{ $p->pivot->color }}</span>@endif {{ $p->pivot->quantity }} × {{ number_format($p->pivot->price, 0, ',', ' ') }} FCFA</div></div>
                    <b>{{ number_format($p->pivot->price * $p->pivot->quantity, 0, ',', ' ') }} FCFA</b>
                </div>
            @endforeach
            <div class="d-flex justify-content-between p-3 fs-5"><b>Total</b><b class="text-accent">{{ $order->formatted_total }}</b></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ui-card mb-3 rise"><div class="ui-card-body">
            <h6 class="fw-bold"><i class="bi bi-geo-alt text-accent me-1"></i>Livraison</h6>
            <p class="mb-1">{{ $order->customer_name }}</p>
            <p class="text-muted small mb-1">{{ $order->shipping_address }}</p>
            <p class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i>{{ $order->customer_phone }}</p>
            @if($order->delivery_note)<p class="text-muted small mb-1"><i class="bi bi-sticky me-1"></i>{{ $order->delivery_note }}</p>@endif
            @if($order->has_exact_location)<a href="{{ $order->map_url }}" target="_blank" class="small fw-bold"><i class="bi bi-geo-alt-fill me-1"></i>Voir ma position exacte</a>@endif
        </div></div>
        <div class="ui-card mb-3 rise"><div class="ui-card-body">
            <h6 class="fw-bold"><i class="bi bi-wallet2 text-accent me-1"></i>Paiement</h6>
            <p class="mb-1">{{ $order->payment_label }}</p>
            <span class="pill {{ $order->payment_status === 'paid' ? 'pill-ok' : 'pill-warn' }}">{{ $order->payment_status === 'paid' ? 'Payé' : 'À régler à la livraison' }}</span>
        </div></div>
        <div class="ui-card mb-3 rise"><div class="ui-card-body">
            <h6 class="fw-bold"><i class="bi bi-shop text-accent me-1"></i>Vendeur</h6>
            <div class="d-flex align-items-center gap-2 mb-2">@if($order->seller)<x-avatar :user="$order->seller" size="sm" />@endif<span>{{ $order->seller_name }}</span></div>
            @if($order->seller && auth()->user()->canMessage($order->seller))<a href="{{ route('messages.conversation', $order->seller) }}" class="btn btn-soft btn-sm"><i class="bi bi-chat-dots me-1"></i>Contacter</a>@endif
        </div></div>
        @if($order->livreur)
            <div class="ui-card rise"><div class="ui-card-body">
                <h6 class="fw-bold"><i class="bi bi-truck text-accent me-1"></i>Votre livreur</h6>
                <div class="d-flex align-items-center gap-2 mb-2"><x-avatar :user="$order->livreur" size="sm" /><span>{{ $order->livreur->name }}</span></div>
                <div class="d-flex gap-2">
                    @if($order->livreur->phone)<a href="tel:{{ $order->livreur->phone }}" class="btn btn-soft btn-sm"><i class="bi bi-telephone me-1"></i>Appeler</a>@endif
                    @if(auth()->user()->canMessage($order->livreur))<a href="{{ route('messages.conversation', $order->livreur) }}" class="btn btn-soft btn-sm"><i class="bi bi-chat-dots me-1"></i>Message</a>@endif
                </div>
            </div></div>
        @endif
    </div>
</div>

@if($dispute)
    <a href="{{ route('disputes.show', $dispute) }}" class="d-flex align-items-center gap-3 p-3 mt-4 rounded-4 text-reset rise" style="background:var(--accent-soft)">
        <i class="bi bi-exclamation-octagon fs-3 text-accent"></i>
        <div class="flex-grow-1"><b>Litige : {{ $dispute->reason_label }}</b><div class="small text-muted">Statut : <span class="pill {{ $dispute->pill_class }}">{{ $dispute->status_label }}</span></div></div><i class="bi bi-chevron-right"></i>
    </a>
@endif

@if($order->status === 'delivered')
<div class="ui-card mt-4 rise">
    <div class="ui-card-head"><span><i class="bi bi-star-fill text-warning me-2"></i>Donnez votre avis</span><small>Il aide les autres clients</small></div>
    <div class="ui-card-body">
        @foreach($order->products as $p)
            @php $rv = $productReviews->get($p->id); @endphp
            <form action="{{ route('reviews.product', $p) }}" method="POST" class="row g-2 align-items-center pb-3 mb-3 border-bottom">
                @csrf <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="col-md-3 d-flex align-items-center gap-2"><img class="thumb" src="{{ $p->image_url }}" alt=""><b class="small">{{ $p->name }}</b></div>
                <div class="col-md-3"><x-star-input :id="'rp'.$p->id" :current="$rv ? $rv->rating : 0" /></div>
                <div class="col-md-4"><input type="text" name="comment" value="{{ $rv ? $rv->comment : '' }}" class="form-control form-control-sm" placeholder="Votre commentaire (facultatif)" maxlength="1000"></div>
                <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">{{ $rv ? 'Modifier' : 'Publier' }}</button></div>
            </form>
        @endforeach

        @if($order->seller)
            <form action="{{ route('reviews.seller', $order->seller) }}" method="POST" class="row g-2 align-items-center">
                @csrf <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="col-md-3 d-flex align-items-center gap-2"><x-avatar :user="$order->seller" size="sm" /><b class="small">Vendeur : {{ $order->seller_name }}</b></div>
                <div class="col-md-3"><x-star-input :id="'rs'.$order->id" :current="$sellerReview ? $sellerReview->rating : 0" /></div>
                <div class="col-md-4"><input type="text" name="comment" value="{{ $sellerReview ? $sellerReview->comment : '' }}" class="form-control form-control-sm" placeholder="Comment s'est passé l'achat ? (facultatif)" maxlength="1000"></div>
                <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">{{ $sellerReview ? 'Modifier' : 'Publier' }}</button></div>
            </form>
        @endif
    </div>
</div>
@endif
@endsection

@if($order->status === 'delivering')
@push('styles')<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var map = L.map('track-map').setView([14.7167, -17.4677], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
    var truck = L.divIcon({ html: '<div style="font-size:30px;line-height:1">🛵</div>', className: '', iconSize: [34, 34], iconAnchor: [17, 17] });
    var dest = null, cour = null, fitted = false, status = document.getElementById('track-status'), url = @json(route('orders.position', $order));

    function refresh() {
        fetch(url, { headers: { 'Accept': 'application/json' } }).then(function (r) { return r.json(); }).then(function (d) {
            if (!d.live) { location.reload(); return; }
            if (d.destination && !dest) { dest = L.marker([d.destination.lat, d.destination.lng]).addTo(map).bindPopup('Votre adresse de livraison'); }
            if (d.courier) {
                var ll = [d.courier.lat, d.courier.lng];
                if (!cour) { cour = L.marker(ll, { icon: truck }).addTo(map).bindPopup(d.livreur || 'Votre livreur'); } else { cour.setLatLng(ll); }
                status.textContent = 'Position mise à jour il y a ' + (d.courier.age == null ? '?' : d.courier.age) + ' s';
                if (!fitted) { if (dest) { map.fitBounds(L.latLngBounds([ll, dest.getLatLng()]).pad(0.3)); } else { map.setView(ll, 15); } fitted = true; }
            } else {
                status.textContent = 'Le livreur n\'a pas encore partagé sa position';
                if (dest && !fitted) { map.setView(dest.getLatLng(), 15); fitted = true; }
            }
        }).catch(function () { status.textContent = 'Connexion perdue…'; });
    }
    refresh(); setInterval(refresh, 8000);
})();
</script>
@endpush
@endif
