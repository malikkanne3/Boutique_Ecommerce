<!DOCTYPE html>
<html lang="fr" @if($role === 'admin') data-bs-theme="dark" @endif>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta['title'] }} — E-Shop SN</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}?v={{ @filemtime(public_path('css/dashboard.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}?v={{ @filemtime(public_path('css/auth.css')) }}" rel="stylesheet">
</head>
<body class="dash theme-{{ $role }}">
@include('partials.flash')

<div class="auth-wrap">
    <aside class="auth-side">
        <a href="{{ route('home') }}" aria-label="E-Shop SN"><img src="{{ asset('images/logo-white.svg') }}" alt="E-Shop SN" style="height:46px;width:auto"></a>
        <div class="position-relative" style="z-index:1">
            <h2 class="mb-2">{{ $meta['title'] }}</h2>
            <p class="mb-4" style="opacity:.9">{{ $meta['tag'] }}</p>
            @foreach($meta['points'] as $point)
                <div class="role-pill"><i class="bi bi-check-circle-fill"></i> {{ $point }}</div>
            @endforeach
        </div>
        <small style="opacity:.75">&copy; {{ date('Y') }} E-ShopSN · Dakar, Sénégal</small>
    </aside>

    <main class="auth-main">
        <div class="auth-box rise">
            <div class="portal-icon"><i class="bi bi-{{ $meta['icon'] }}"></i></div>
            <h1 class="fw-bold mb-1" style="letter-spacing:-.03em">Connexion {{ strtolower($label) }}</h1>
            <p class="text-muted mb-4">Accès réservé aux comptes <b>{{ strtolower($label) }}</b>.</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="portal" value="{{ $role }}">

                <div class="mb-3">
                    <label class="form-label" for="email">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus autocomplete="username">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Mot de passe</label>
                        @if(Route::has('password.request'))<a href="{{ route('password.request') }}" class="small fw-bold">Oublié ?</a>@endif
                    </div>
                    <div class="input-group">
                        <input id="password" type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required autocomplete="current-password">
                        <button type="button" class="btn btn-soft" data-no-loading onclick="var i=document.getElementById('password');i.type=i.type==='password'?'text':'password'"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label class="form-check-label small" for="remember">Rester connecté</label></div>
                <button class="btn btn-primary btn-lg w-100">Se connecter <i class="bi bi-arrow-right ms-1"></i></button>
            </form>

            @if($role === 'vendeur')
                <p class="text-center text-muted mt-4 mb-0 small">Votre boutique doit être validée par l'administration avant votre première connexion.</p>
            @elseif($role === 'livreur')
                <p class="text-center text-muted mt-4 mb-0 small">Les comptes livreurs sont créés par l'administration.</p>
            @endif

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/ui.js') }}?v={{ @filemtime(public_path('js/ui.js')) }}"></script>
</body>
</html>
