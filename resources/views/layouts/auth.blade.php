<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — E-Shop SN</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/storefront.css') }}?v={{ @filemtime(public_path('css/storefront.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}?v={{ @filemtime(public_path('css/auth.css')) }}" rel="stylesheet">
</head>
<body class="store">
@include('partials.flash')
<div class="auth-wrap">
    <aside class="auth-side">
        <a href="{{ route('home') }}" aria-label="E-Shop SN"><img src="{{ asset('images/logo-white.svg') }}" alt="E-Shop SN" style="height:46px;width:auto"></a>
        <div class="position-relative" style="z-index:1">
            <h2 class="mb-4">Une plateforme,<br>quatre espaces.</h2>
            <div class="role-pill"><i class="bi bi-bag-heart"></i> Client — achetez et suivez vos commandes</div>
            <div class="role-pill"><i class="bi bi-shop"></i> Vendeur — gérez votre boutique et vos stocks</div>
            <div class="role-pill"><i class="bi bi-truck"></i> Livreur — recevez et livrez vos missions</div>
            <div class="role-pill"><i class="bi bi-shield-lock"></i> Admin — supervisez toute la plateforme</div>
        </div>
        <small style="opacity:.75">&copy; {{ date('Y') }} E-ShopSN · Dakar, Sénégal</small>
    </aside>
    <main class="auth-main"><div class="auth-box rise">@yield('content')</div></main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/ui.js') }}?v={{ @filemtime(public_path('js/ui.js')) }}"></script>
@stack('scripts')
</body>
</html>
