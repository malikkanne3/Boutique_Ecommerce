@extends('layouts.dashboard')
@section('title', auth()->user()->isVendeur() ? 'Ma boutique' : 'Mon profil')

@section('content')
@php $user = auth()->user(); @endphp
<x-page-head :title="$user->isVendeur() ? 'Ma boutique' : 'Mon profil'" subtitle="Vos informations, votre photo et votre sécurité." />

<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="ui-card rise">
            @csrf @method('PATCH')
            <div class="ui-card-head">Informations</div>
            <div class="ui-card-body row g-3">

                {{-- Photos --}}
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <x-avatar :user="$user" size="xl" />
                            <div><label class="form-label mb-1">Photo de profil</label>
                                <input type="file" name="avatar" accept="image/*" class="form-control form-control-sm @error('avatar') is-invalid @enderror">
                                @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if($user->avatar)<div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="rm-av"><label class="form-check-label small" for="rm-av">Supprimer ma photo</label></div>@endif</div>
                        </div>
                        @if($user->isVendeur())
                            <div class="d-flex align-items-center gap-3">
                                @if($user->shop_logo)<img src="{{ asset('storage/'.$user->shop_logo) }}" class="thumb" style="width:88px;height:88px" alt="Logo">@else<span class="thumb d-grid" style="width:88px;height:88px;place-items:center;font-size:2rem;color:var(--accent)"><i class="bi bi-shop"></i></span>@endif
                                <div><label class="form-label mb-1">Photo / logo de la boutique</label>
                                    <input type="file" name="shop_logo" accept="image/*" class="form-control form-control-sm @error('shop_logo') is-invalid @enderror">
                                    @error('shop_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if($user->shop_logo)<div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_shop_logo" value="1" id="rm-sl"><label class="form-check-label small" for="rm-sl">Supprimer le logo</label></div>@endif</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($user->isVendeur())
                    <div class="col-12"><label class="form-label">Nom de la boutique</label>
                        <input type="text" name="shop_name" value="{{ old('shop_name', $user->shop_name) }}" class="form-control @error('shop_name') is-invalid @enderror"></div>
                @endif
                <div class="col-md-6"><label class="form-label">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label">Téléphone</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror"></div>
                <div class="col-12"><label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="form-label">{{ $user->isLivreur() ? 'Zone de livraison' : 'Adresse par défaut' }}</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control" placeholder="Quartier, ville"></div>

                @if($user->isClient())
                    <div class="col-12">
                        <input type="hidden" name="address_lat" id="p-lat" value="{{ old('address_lat', $user->address_lat) }}">
                        <input type="hidden" name="address_lng" id="p-lng" value="{{ old('address_lng', $user->address_lng) }}">
                        <div class="d-flex flex-wrap align-items-center gap-2 p-3 rounded-3" style="background:var(--accent-soft)">
                            <i class="bi bi-crosshair2 fs-4 text-accent"></i>
                            <div class="flex-grow-1"><b>Ma position exacte</b><div class="small text-muted" id="p-status">{{ $user->address_lat ? 'Enregistrée : '.round((float) $user->address_lat, 5).', '.round((float) $user->address_lng, 5) : 'Non enregistrée — elle sera proposée à chaque commande.' }}</div></div>
                            <button type="button" class="btn btn-primary btn-sm" id="p-btn" data-no-loading>Utiliser ma position</button>
                            <button type="button" class="btn btn-ghost btn-sm" id="p-clear" data-no-loading>Effacer</button>
                        </div>
                    </div>
                @endif

                @if($user->isVendeur())
                    <div class="col-12"><hr class="my-1"><h6 class="fw-bold mb-0"><i class="bi bi-shop text-accent me-2"></i>Page de ma boutique</h6></div>

                    <div class="col-12"><label class="form-label">Lien de ma boutique <small class="text-muted">(à partager sur WhatsApp, Facebook…)</small></label>
                        <div class="input-group"><input type="text" id="shop-link" class="form-control" value="{{ $user->shop_url }}" readonly>
                            <button type="button" class="btn btn-soft" data-copy="#shop-link">Copier</button>
                            <a class="btn btn-primary" href="{{ $user->shop_url }}" target="_blank">Voir</a></div>
                        <a href="https://wa.me/?text={{ urlencode('Découvrez ma boutique '.$user->display_name.' sur E-Shop SN : '.$user->shop_url) }}" target="_blank" class="share-btn wa mt-2"><i class="bi bi-whatsapp"></i>Partager sur WhatsApp</a></div>

                    <div class="col-12"><label class="form-label">Bannière de la boutique</label>
                        @if($user->shop_banner_url)<img src="{{ $user->shop_banner_url }}" alt="Bannière" class="d-block mb-2" style="max-height:110px;border-radius:12px;max-width:100%">@endif
                        <input type="file" name="shop_banner" accept="image/*" class="form-control form-control-sm @error('shop_banner') is-invalid @enderror">
                        @error('shop_banner')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($user->shop_banner)<div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_shop_banner" value="1" id="rm-sb"><label class="form-check-label small" for="rm-sb">Supprimer la bannière</label></div>@endif</div>

                    <div class="col-12"><label class="form-label">Description de la boutique</label>
                        <textarea name="shop_description" rows="4" maxlength="1000" class="form-control @error('shop_description') is-invalid @enderror" placeholder="Présentez votre boutique, vos produits, vos engagements…">{{ old('shop_description', $user->shop_description) }}</textarea>
                        @error('shop_description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

                    <div class="col-12">
                        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" role="switch" name="use_hours" value="1" id="use-hours" @checked($user->has_hours)><label class="form-check-label fw-bold" for="use-hours">Afficher mes horaires d'ouverture</label></div>
                        <div id="hours-box" class="p-3 rounded-3" style="background:var(--surface-2)">
                            @foreach(\App\Models\User::DAYS as $d => $label)
                                @php $h = $user->hoursForDay($d); @endphp
                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col-4 col-md-3 small fw-bold">{{ $label }}</div>
                                    <div class="col-8 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="hours[{{ $d }}][closed]" value="1" id="cl{{ $d }}" @checked($h['closed'])><label class="form-check-label small" for="cl{{ $d }}">Fermé</label></div></div>
                                    <div class="col-6 col-md-3"><input type="time" name="hours[{{ $d }}][open]" value="{{ $h['open'] }}" class="form-control form-control-sm"></div>
                                    <div class="col-6 col-md-3"><input type="time" name="hours[{{ $d }}][close]" value="{{ $h['close'] }}" class="form-control form-control-sm"></div>
                                </div>
                            @endforeach
                        </div>
                        @error('hours.*.open')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" role="switch" name="vacation_mode" value="1" id="vac-mode" @checked($user->vacation_mode)><label class="form-check-label fw-bold" for="vac-mode"><i class="bi bi-umbrella me-1"></i>Mode vacances <small class="text-muted fw-normal">(masque vos produits et suspend les commandes)</small></label></div>
                        <div class="row g-2">
                            <div class="col-md-4"><label class="form-label small">Retour prévu le</label><input type="date" name="vacation_until" min="{{ today()->toDateString() }}" value="{{ $user->vacation_until && $user->vacation_until->gte(today()) ? $user->vacation_until->format('Y-m-d') : '' }}" class="form-control form-control-sm @error('vacation_until') is-invalid @enderror"></div>
                            <div class="col-md-8"><label class="form-label small">Message aux clients</label><input type="text" name="vacation_message" maxlength="255" value="{{ old('vacation_message', $user->vacation_message) }}" class="form-control form-control-sm" placeholder="Ex : Boutique fermée pour congés, retour le 5 janvier !"></div>
                        </div>
                        @error('vacation_until')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                @endif

                <div class="col-12"><button class="btn btn-primary px-4">Enregistrer</button>
                    @if(session('status') === 'profile-updated')<span class="text-success fw-bold ms-3"><i class="bi bi-check-circle me-1"></i>Enregistré</span>@endif</div>
            </div>
        </form>
    </div>
    <div class="col-lg-5">
        <form method="POST" action="{{ route('password.update') }}" class="ui-card rise mb-4">
            @csrf @method('PUT')
            <div class="ui-card-head">Changer le mot de passe</div>
            <div class="ui-card-body">
                <div class="mb-3"><label class="form-label">Mot de passe actuel</label><input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required>
                    @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Nouveau mot de passe</label><input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required>
                    @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Confirmation</label><input type="password" name="password_confirmation" class="form-control" required></div>
                <button class="btn btn-primary">Mettre à jour</button>
                @if(session('status') === 'password-updated')<span class="text-success fw-bold ms-2">Modifié ✓</span>@endif
            </div>
        </form>

        <div class="ui-card rise"><div class="ui-card-body small text-muted">
            <i class="bi bi-shield-check text-accent me-1"></i>Pour supprimer ou désactiver votre compte, contactez l'administration.
        </div></div>
    </div>
</div>
@endsection

@if($user->isVendeur())
@push('scripts')
<script>
(function () {
    var sw = document.getElementById('use-hours'), box = document.getElementById('hours-box');
    function sync() { box.style.display = sw.checked ? '' : 'none'; } sw.addEventListener('change', sync); sync();
})();
</script>
@endpush
@endif

@if($user->isClient())
@push('scripts')
<script>
(function () {
    var lat = document.getElementById('p-lat'), lng = document.getElementById('p-lng'), st = document.getElementById('p-status');
    document.getElementById('p-btn').addEventListener('click', function () {
        if (!navigator.geolocation) { st.textContent = 'Géolocalisation indisponible sur cet appareil.'; return; }
        st.textContent = 'Recherche…';
        navigator.geolocation.getCurrentPosition(function (p) {
            lat.value = p.coords.latitude.toFixed(7); lng.value = p.coords.longitude.toFixed(7);
            st.textContent = 'Position capturée — cliquez sur « Enregistrer » pour la sauvegarder.';
        }, function () { st.textContent = 'Position introuvable (la localisation exige une connexion https et votre autorisation).'; }, { enableHighAccuracy: true, timeout: 15000 });
    });
    document.getElementById('p-clear').addEventListener('click', function () { lat.value = ''; lng.value = ''; st.textContent = 'Position effacée — cliquez sur « Enregistrer ».'; });
})();
</script>
@endpush
@endif
