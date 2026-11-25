@extends('layouts.dashboard')
@section('title', 'Missions')

@section('content')
<div class="row g-2 mb-4">
    @foreach([['hourglass-split', 'À accepter', $stats['to_accept'], 'warn'], ['truck', 'En cours', $stats['ongoing'], 'info'], ['calendar-check', "Aujourd'hui", $stats['today'], 'ok'], ['trophy', 'Total livrées', $stats['total'], '']] as [$ic, $lb, $val, $tone])
        <div class="col-6 col-md-3">
            <div class="ui-card p-3 text-center rise h-100"><div class="stat-ico {{ $tone }} mx-auto mb-2" style="width:42px;height:42px;font-size:1.2rem"><i class="bi bi-{{ $ic }}"></i></div>
                <div class="stat-value" style="font-size:1.45rem">{{ $val }}</div><div class="stat-label" style="font-size:.66rem">{{ $lb }}</div></div>
        </div>
    @endforeach
</div>

@php $me = auth()->user(); @endphp

{{-- Disponibilité --}}
<div class="ui-card p-3 mb-3 d-flex align-items-center gap-3 rise">
    <span class="stat-ico {{ $me->is_available ? 'ok' : 'bad' }}" style="width:46px;height:46px"><i class="bi bi-{{ $me->is_available ? 'check-circle-fill' : 'pause-circle-fill' }}"></i></span>
    <div class="flex-grow-1"><b>{{ $me->is_available ? 'Vous êtes disponible' : 'Vous êtes indisponible' }}</b>
        <div class="small text-muted">{{ $me->is_available ? 'Les vendeurs peuvent vous assigner des livraisons.' : 'Aucune nouvelle livraison ne vous sera assignée.' }}</div></div>
    <form action="{{ route('livreur.availability') }}" method="POST" data-no-loading>@csrf
        <button class="btn {{ $me->is_available ? 'btn-outline-danger' : 'btn-primary' }}">{{ $me->is_available ? 'Me mettre indisponible' : 'Me mettre disponible' }}</button></form>
</div>

{{-- Partage de position --}}
<div class="ui-card p-3 mb-3 rise">
    <div class="d-flex align-items-center gap-3">
        <span class="stat-ico info" style="width:46px;height:46px"><i class="bi bi-broadcast"></i></span>
        <div class="flex-grow-1"><b>Partage de position en direct</b><div class="small text-muted" id="share-status">Désactivé — vos clients ne voient pas où vous êtes.</div></div>
        <div class="form-check form-switch fs-3 m-0"><input class="form-check-input" type="checkbox" role="switch" id="share-toggle" aria-label="Partager ma position"></div>
    </div>
    <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Gardez cette page ouverte pendant vos livraisons. Nécessite une connexion https et votre autorisation.</div>
</div>

@include('partials.quick-actions')

@if(count($tour) >= 2)
    @php
        $gps = collect($tour)->filter(fn ($t) => $t['order']->has_exact_location)->map(fn ($t) => $t['order']->delivery_lat . ',' . $t['order']->delivery_lng)->values();
        $dirUrl = $gps->count() ? 'https://www.google.com/maps/dir/?api=1&travelmode=driving&destination=' . $gps->last() . ($gps->count() > 1 ? '&waypoints=' . urlencode($gps->slice(0, -1)->implode('|')) : '') : null;
    @endphp
    <div class="ui-card mb-4 rise">
        <div class="ui-card-head"><span><i class="bi bi-signpost-split text-accent me-2"></i>Tournée conseillée</span><small>de la plus proche à la plus lointaine</small></div>
        @foreach($tour as $i => $t)
            @php $o = $t['order']; @endphp
            <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                <span class="avatar" style="width:32px;height:32px;font-size:.85rem">{{ $i + 1 }}</span>
                <div class="flex-grow-1 min-w-0"><b class="small">{{ $o->customer_name }}</b><div class="small text-muted text-truncate">{{ $o->shipping_address }}</div></div>
                <div class="text-end small">@if($t['leg'] !== null)<b>≈ {{ $t['leg'] }} km</b>@elseif(! $o->has_exact_location)<span class="text-muted">sans GPS</span>@else<span class="text-muted">départ</span>@endif</div>
                <a href="{{ $o->map_url }}" target="_blank" class="btn btn-soft btn-sm"><i class="bi bi-map"></i></a>
            </div>
        @endforeach
        @if($dirUrl)<div class="p-3"><a href="{{ $dirUrl }}" target="_blank" class="btn btn-primary w-100"><i class="bi bi-signpost me-1"></i>Ouvrir l'itinéraire complet</a></div>@endif
    </div>
@endif

<h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split text-accent me-2"></i>Missions à accepter <span class="pill pill-warn ms-1">{{ $toAccept->count() }}</span></h5>
@forelse($toAccept as $order)
    @include('livreur.partials.mission', ['order' => $order, 'mode' => 'accept'])
@empty
    <div class="ui-card mb-4"><x-empty-state icon="inbox" title="Aucune nouvelle mission" text="Vous serez notifié dès qu'un vendeur vous en assigne une." /></div>
@endforelse

<h5 class="fw-bold mb-3 mt-4"><i class="bi bi-truck text-accent me-2"></i>Livraisons en cours <span class="pill pill-delivering ms-1">{{ $ongoing->count() }}</span></h5>
@forelse($ongoing as $order)
    @include('livreur.partials.mission', ['order' => $order, 'mode' => 'ongoing'])
@empty
    <div class="ui-card"><x-empty-state icon="truck" title="Rien en cours" text="Acceptez une mission pour démarrer votre tournée." /></div>
@endforelse

@include('partials.dash-extras')
@endsection

@push('scripts')
<script>
(function () {
    var toggle = document.getElementById('share-toggle'), status = document.getElementById('share-status');
    var token = document.querySelector('meta[name=csrf-token]').content, url = @json(route('livreur.position'));
    var watchId = null, lastSent = 0;

    function send(p) {
        var now = Date.now(); if (now - lastSent < 10000) { return; } lastSent = now;
        fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ lat: p.coords.latitude, lng: p.coords.longitude }) })
            .then(function (r) { status.textContent = r.ok ? 'Position partagée à ' + new Date().toLocaleTimeString('fr-FR') : 'Envoi refusé (reconnectez-vous).'; })
            .catch(function () { status.textContent = 'Envoi impossible (réseau)…'; });
    }
    function start() {
        if (!navigator.geolocation) { status.textContent = 'Géolocalisation indisponible sur cet appareil.'; toggle.checked = false; return; }
        status.textContent = 'Recherche de votre position…';
        watchId = navigator.geolocation.watchPosition(send, function (e) {
            status.textContent = e.code === 1 ? 'Accès à la position refusé : autorisez-le dans le navigateur.' : 'Position indisponible (la connexion https est requise).';
            toggle.checked = false; localStorage.removeItem('share_position');
        }, { enableHighAccuracy: true, maximumAge: 5000, timeout: 20000 });
        localStorage.setItem('share_position', '1');
    }
    function stop() {
        if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        localStorage.removeItem('share_position'); status.textContent = 'Désactivé — vos clients ne voient pas où vous êtes.';
    }
    toggle.addEventListener('change', function () { this.checked ? start() : stop(); });
    if (localStorage.getItem('share_position') === '1') { toggle.checked = true; start(); }
})();
</script>
@endpush
