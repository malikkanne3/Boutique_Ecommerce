@extends('layouts.app')
@section('title', 'Livraison & paiement')

@section('content')
@php
    $u = auth()->user();
    $lat = old('delivery_lat', $u->address_lat);
    $lng = old('delivery_lng', $u->address_lng);
@endphp
<div class="container" style="max-width:1140px">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold mb-0" style="letter-spacing:-.03em">Livraison &amp; paiement</h1>
        <div class="step-dots"><span class="on"><span class="n"><i class="bi bi-check"></i></span> Panier</span><i class="bi bi-chevron-right"></i><span class="on"><span class="n">2</span> Livraison &amp; paiement</span><i class="bi bi-chevron-right"></i><span><span class="n">3</span> Confirmation</span></div>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" class="row g-4">
        @csrf
        <div class="col-lg-7">

            {{-- 1. Contact --}}
            <div class="card-ui p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill text-accent me-2"></i>1. Vos informations <small class="text-muted fw-normal">(vérifiez-les)</small></h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nom complet</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $u->name) }}" class="form-control @error('customer_name') is-invalid @enderror" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Téléphone <small class="text-muted">(le livreur vous appellera)</small></label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone', $u->phone) }}" placeholder="+221 77 000 00 00" class="form-control @error('customer_phone') is-invalid @enderror" required>
                        @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-12"><label class="form-label">Email <small class="text-muted">(confirmation de commande)</small></label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $u->email) }}" class="form-control @error('customer_email') is-invalid @enderror" required>
                        @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
            </div>

            {{-- 2. Adresse / position --}}
            <div class="card-ui p-4 mb-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-geo-alt text-accent me-2"></i>2. Où livrer ?</h5>
                <p class="text-muted small">Saisissez votre adresse, partagez votre position exacte, ou les deux (recommandé : le livreur vous trouvera plus facilement).</p>

                <label class="form-label">Adresse de livraison</label>
                <textarea name="shipping_address" rows="2" placeholder="Quartier, rue, repère (ex : près de la pharmacie…), ville" class="form-control mb-3 @error('shipping_address') is-invalid @enderror">{{ old('shipping_address', $u->address) }}</textarea>
                @error('shipping_address')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                <input type="hidden" name="delivery_lat" id="lat" value="{{ $lat }}">
                <input type="hidden" name="delivery_lng" id="lng" value="{{ $lng }}">
                <div class="loc-box {{ $lat && $lng ? 'ok' : '' }}" id="loc-box">
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                        <div><b><i class="bi bi-crosshair2 me-1"></i>Ma position exacte</b>
                            <div class="small text-muted" id="loc-status">{{ $lat && $lng ? 'Position enregistrée : ' . round((float) $lat, 5) . ', ' . round((float) $lng, 5) : 'Non partagée' }}</div></div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-soft btn-sm" id="loc-btn" data-no-loading><i class="bi bi-geo-alt-fill me-1"></i>Utiliser ma position</button>
                            <button type="button" class="btn btn-ghost btn-sm {{ $lat && $lng ? '' : 'd-none' }}" id="loc-clear" data-no-loading>Effacer</button>
                        </div>
                    </div>
                    <iframe id="map" class="map-frame mt-3 {{ $lat && $lng ? '' : 'd-none' }}" loading="lazy" title="Votre position"></iframe>
                </div>

                <label class="form-label mt-3">Indications pour le livreur <small class="text-muted">(facultatif)</small></label>
                <input type="text" name="delivery_note" value="{{ old('delivery_note') }}" maxlength="255" class="form-control" placeholder="Ex : 2e étage, portail bleu, sonner 2 fois…">
            </div>

            {{-- 3. Paiement --}}
            <div class="card-ui p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-wallet2 text-accent me-2"></i>3. Mode de paiement</h5>
                <div class="d-grid gap-2">
                    @foreach($methods as $key => $m)
                        <div class="pay-opt position-relative">
                            <input type="radio" name="payment_method" id="pay-{{ $key }}" value="{{ $key }}" @checked(old('payment_method', 'cash') === $key)>
                            <label for="pay-{{ $key }}"><i class="bi bi-{{ $m['icon'] }}"></i><span><b>{{ $m['label'] }}</b><small>{{ $m['hint'] }}</small></span></label>
                        </div>
                    @endforeach
                </div>
                @error('payment_method')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                <div class="small text-muted mt-3"><i class="bi bi-info-circle me-1"></i>Aucun paiement n'est prélevé maintenant : vous payez à la réception de votre commande.</div>
            </div>

            <button class="btn btn-primary btn-lg w-100"><i class="bi bi-bag-check me-2"></i>Confirmer ma commande · {{ number_format($total, 0, ',', ' ') }} FCFA</button>
            <a href="{{ route('cart.index') }}" class="btn btn-link w-100 mt-1">← Modifier mon panier</a>
        </div>

        {{-- Récapitulatif --}}
        <div class="col-lg-5">
            <div class="card-ui p-4 sum-card">
                <h5 class="fw-bold mb-3">Votre commande</h5>
                @foreach($groups as $sellerKey => $lines)
                    <div class="small fw-bold mb-2" style="color:var(--accent)"><i class="bi bi-shop me-1"></i>{{ $lines->first()->product->seller_name }}</div>
                    @foreach($lines as $line)
                        <div class="d-flex gap-3 align-items-center mb-3">
                            <img src="{{ $line->product->image_url }}" style="width:56px;height:56px;border-radius:12px;object-fit:cover" alt="">
                            <div class="flex-grow-1 small">
                                <div class="fw-bold">{{ $line->product->name }}</div>
                                @if($line->variant)<div>@if($line->variant->size)<span class="var-chip">{{ $line->variant->size }}</span>@endif @if($line->variant->color)<span class="var-chip">{{ $line->variant->color }}</span>@endif</div>@endif
                                <span class="text-muted">{{ $line->quantity }} × {{ number_format($line->unit_price, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <b class="small">{{ number_format($line->subtotal, 0, ',', ' ') }}</b>
                        </div>
                    @endforeach
                    @if($groups->count() > 1)<div class="d-flex justify-content-between small text-muted mb-3 pb-2 border-bottom"><span>Sous-total vendeur</span><b>{{ number_format($lines->sum('subtotal'), 0, ',', ' ') }} FCFA</b></div>@endif
                @endforeach
                <hr>
                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Articles</span><b>{{ $items->sum('quantity') }}</b></div>
                @if($savings > 0)<div class="d-flex justify-content-between small mb-1"><span class="text-muted">Économies</span><b class="text-success">- {{ number_format($savings, 0, ',', ' ') }} FCFA</b></div>@endif
                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Livraison</span><b>À convenir avec le vendeur</b></div>
                <div class="d-flex justify-content-between align-items-center"><span class="fw-bold">Total à payer</span><span class="fs-4 fw-bold text-accent">{{ number_format($total, 0, ',', ' ') }} FCFA</span></div>
                @if($groups->count() > 1)<div class="alert alert-info small py-2 mt-3 mb-0">Commande répartie en {{ $groups->count() }} envois (un par vendeur).</div>@endif
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var lat = document.getElementById('lat'), lng = document.getElementById('lng'), box = document.getElementById('loc-box');
    var status = document.getElementById('loc-status'), map = document.getElementById('map'), btn = document.getElementById('loc-btn'), clear = document.getElementById('loc-clear');

    function showMap() {
        if (!lat.value || !lng.value) { map.classList.add('d-none'); clear.classList.add('d-none'); box.classList.remove('ok'); return; }
        var a = parseFloat(lat.value), o = parseFloat(lng.value);
        map.src = 'https://www.openstreetmap.org/export/embed.html?bbox=' + (o - 0.004) + ',' + (a - 0.0025) + ',' + (o + 0.004) + ',' + (a + 0.0025) + '&layer=mapnik&marker=' + a + ',' + o;
        map.classList.remove('d-none'); clear.classList.remove('d-none'); box.classList.add('ok');
    }
    showMap();

    btn.addEventListener('click', function () {
        if (!navigator.geolocation) { status.textContent = 'La géolocalisation n\'est pas disponible sur cet appareil.'; return; }
        status.textContent = 'Recherche de votre position…';
        navigator.geolocation.getCurrentPosition(function (pos) {
            lat.value = pos.coords.latitude.toFixed(7); lng.value = pos.coords.longitude.toFixed(7);
            status.textContent = 'Position partagée (précision ≈ ' + Math.round(pos.coords.accuracy) + ' m)';
            showMap();
        }, function (err) {
            status.textContent = err.code === 1 ? 'Accès refusé : autorisez la localisation dans votre navigateur.' : 'Position introuvable. La localisation exige une connexion sécurisée (https) : utilisez le lien https, ou saisissez votre adresse.';
        }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
    });
    clear.addEventListener('click', function () { lat.value = ''; lng.value = ''; status.textContent = 'Non partagée'; showMap(); });
})();
</script>
@endpush
