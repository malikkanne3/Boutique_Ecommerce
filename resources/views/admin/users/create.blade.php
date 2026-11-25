@extends('layouts.dashboard')
@section('title', 'Créer un compte')

@section('content')
<x-page-head title="Créer un compte" subtitle="Le mot de passe est provisoire : l'utilisateur devra en choisir un nouveau à sa première connexion.">
    <x-slot:actions><a href="{{ route('admin.users.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Retour</a></x-slot:actions>
</x-page-head>

<div class="row"><div class="col-lg-7">
<form method="POST" action="{{ route('admin.users.store') }}" class="ui-card rise">
    @csrf
    <div class="ui-card-body row g-3">
        <div class="col-12"><label class="form-label">Type de compte</label>
            <div class="chips">
                @foreach(['vendeur' => 'Vendeur', 'livreur' => 'Livreur', 'client' => 'Client', 'admin' => 'Administrateur'] as $k => $l)
                    <label class="chip" style="cursor:pointer"><input type="radio" name="role" value="{{ $k }}" class="form-check-input me-1" @checked(old('role', $preRole) === $k)>{{ $l }}</label>
                @endforeach
            </div>@error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
        <div class="col-12" id="shop-wrap"><label class="form-label">Nom de la boutique <small class="text-muted">(vendeur)</small></label>
            <input type="text" name="shop_name" value="{{ old('shop_name') }}" class="form-control @error('shop_name') is-invalid @enderror">@error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Nom complet</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Téléphone</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control"></div>
        <div class="col-12"><label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

        <div class="col-12"><label class="form-label">Mot de passe provisoire</label>
            <div class="chips mb-2">
                <label class="chip" style="cursor:pointer"><input type="radio" name="password_mode" value="generate" class="form-check-input me-1" @checked(old('password_mode', 'generate') === 'generate')><i class="bi bi-magic"></i> Générer automatiquement</label>
                <label class="chip" style="cursor:pointer"><input type="radio" name="password_mode" value="manual" class="form-check-input me-1" @checked(old('password_mode') === 'manual')><i class="bi bi-pencil"></i> Je le saisis moi-même</label>
            </div>
            <div id="manual-wrap">
                <input type="text" name="password" value="{{ old('password') }}" class="form-control @error('password') is-invalid @enderror" placeholder="Au moins 8 caractères" autocomplete="off">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <small class="text-muted d-block mt-2"><i class="bi bi-shield-lock me-1"></i>Le mot de passe est envoyé par e-mail et affiché une seule fois. Vous ne pourrez plus jamais le consulter ensuite.</small>
        </div>

        <div class="col-12"><button class="btn btn-primary btn-lg px-4"><i class="bi bi-person-check me-1"></i>Créer le compte</button></div>
    </div>
</form>
</div></div>
@endsection

@push('scripts')
<script>
    function sync() {
        var r = document.querySelector('input[name=role]:checked');
        document.getElementById('shop-wrap').style.display = (r && r.value === 'vendeur') ? '' : 'none';
        var m = document.querySelector('input[name=password_mode]:checked');
        document.getElementById('manual-wrap').style.display = (m && m.value === 'manual') ? '' : 'none';
    }
    document.querySelectorAll('input[name=role], input[name=password_mode]').forEach(function (r) { r.addEventListener('change', sync); }); sync();
</script>
@endpush
