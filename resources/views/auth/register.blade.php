@extends('layouts.auth')
@section('title', 'Inscription')

@section('content')
    <h1 class="fw-bold mb-1" style="letter-spacing:-.03em">Créer mon compte</h1>
    <p class="text-muted mb-4">Rejoignez E-Shop SN en quelques secondes.</p>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        @php $type = old('account_type', request('type', 'client')); @endphp
        <label class="form-label">Je souhaite…</label>
        <div class="row g-2 mb-3">
            <div class="col-6 type-card"><input type="radio" name="account_type" id="t-client" value="client" @checked($type === 'client')><label for="t-client"><i class="bi bi-bag-heart"></i>Acheter<small>Compte client</small></label></div>
            <div class="col-6 type-card"><input type="radio" name="account_type" id="t-vendeur" value="vendeur" @checked($type === 'vendeur')><label for="t-vendeur"><i class="bi bi-shop"></i>Vendre<small>Compte vendeur</small></label></div>
        </div>

        <div id="shop-field" class="mb-3 {{ $type === 'vendeur' ? '' : 'd-none' }}">
            <label class="form-label">Nom de votre boutique</label>
            <input type="text" name="shop_name" value="{{ old('shop_name') }}" class="form-control @error('shop_name') is-invalid @enderror" placeholder="Ex : Boutique Teranga">
            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Votre boutique sera examinée par l'administration. Vous recevrez votre lien de connexion par e-mail une fois validée.</div>
            @error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3"><label class="form-label"><span id="photo-label">Photo de profil</span> <small class="text-muted">(facultatif)</small></label>
            <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
            @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

        <div class="mb-3"><label class="form-label">Nom complet</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="row g-3 mb-3">
            <div class="col-md-7"><label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-5"><label class="form-label">Téléphone</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="77 000 00 00">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Confirmation</label>
                <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password"></div>
        </div>
        <button class="btn btn-primary btn-lg w-100">Créer mon compte</button>
    </form>
    <p class="text-center text-muted mt-4 mb-0">Déjà inscrit ? <a href="{{ route('login') }}" class="fw-bold">Se connecter</a></p>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('input[name=account_type]').forEach(function (r) {
        r.addEventListener('change', function () {
            document.getElementById('shop-field').classList.toggle('d-none', this.value !== 'vendeur');
            document.getElementById('photo-label').textContent = this.value === 'vendeur' ? 'Photo / logo de la boutique' : 'Photo de profil';
        });
    });
</script>
@endpush
