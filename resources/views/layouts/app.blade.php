<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Accueil') — E-Shop Sénégal</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/components.css') }}?v={{ @filemtime(public_path('css/components.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/storefront.css') }}?v={{ @filemtime(public_path('css/storefront.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="store">
@include('partials.flash')

@php $cartCount = \App\Support\Cart::count(); @endphp
<header class="store-nav">
    <div class="store-nav-in">
        <a class="brand" href="{{ route('home') }}" aria-label="E-Shop SN — accueil"><img src="{{ asset('images/logo.svg') }}" alt="E-Shop SN" style="height:38px;width:auto"></a>

        <form class="nav-search" action="{{ route('shop.index') }}" method="GET" data-no-loading>
            <i class="bi bi-search"></i>
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Rechercher un produit…" aria-label="Rechercher">
        </form>

        <nav class="nav-links">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a>
            <a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.*') ? 'active' : '' }}">Boutique</a>
        </nav>

        <div class="d-flex align-items-center gap-2 ms-auto">
            <a href="{{ route('cart.index') }}" class="icon-btn" title="Panier"><i class="bi bi-cart3"></i>
                @if($cartCount)<span class="dot">{{ $cartCount }}</span>@endif
            </a>
            @auth
                @php $u = auth()->user(); $unreadN = $u->unreadNotificationsCount(); @endphp
                <a href="{{ route('favorites.index') }}" class="icon-btn d-none d-sm-grid" title="Mes favoris"><i class="bi bi-heart"></i></a>
                <a href="{{ route('notifications.index') }}" class="icon-btn" title="Notifications"><i class="bi bi-bell"></i>
                    @if($unreadN)<span class="dot">{{ $unreadN }}</span>@endif
                </a>
                <div class="dropdown">
                    <button class="account-btn" data-bs-toggle="dropdown"><span class="av">{{ $u->initial }}</span><span class="lbl">{{ \Illuminate\Support\Str::before($u->name, ' ') }}</span><i class="bi bi-chevron-down small"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="border-radius:16px;min-width:230px">
                        <li class="px-3 py-2"><div class="fw-bold">{{ $u->name }}</div><small class="text-muted">{{ $u->role_label }}</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded-3" href="{{ $u->dashboardUrl() }}"><i class="bi bi-speedometer2 me-2 text-accent"></i>{{ $u->isClient() ? 'Mon espace' : 'Mon tableau de bord' }}</a></li>
                        <li><a class="dropdown-item rounded-3" href="{{ route('orders.index') }}"><i class="bi bi-box-seam me-2 text-accent"></i>Mes achats</a></li>
                        <li><a class="dropdown-item rounded-3" href="{{ route('messages.index') }}"><i class="bi bi-chat-dots me-2 text-accent"></i>Messages</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item rounded-3 text-danger" data-no-loading><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</button></form></li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3">Connexion</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-3 d-none d-sm-inline-block">S'inscrire</a>
            @endauth
        </div>
    </div>
</header>

<main class="py-4">@yield('content')</main>

<footer class="store-foot">
    <div class="container" style="max-width:1240px">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="mb-3"><img src="{{ asset('images/logo-light.svg') }}" alt="E-Shop SN" style="height:42px;width:auto"></div>
                <p class="small">La marketplace sénégalaise : achetez auprès de vendeurs locaux et faites-vous livrer rapidement.</p>
            </div>
            <div class="col-6 col-lg-2"><h6>Acheter</h6>
                <ul class="list-unstyled small"><li class="mb-2"><a href="{{ route('shop.index') }}">Tous les produits</a></li><li class="mb-2"><a href="{{ route('cart.index') }}">Mon panier</a></li><li class="mb-2"><a href="{{ route('orders.index') }}">Mes commandes</a></li></ul></div>
            <div class="col-6 col-lg-3"><h6>Vendre</h6>
                <ul class="list-unstyled small"><li class="mb-2"><a href="{{ route('register') }}">Ouvrir ma boutique</a></li></ul></div>
            <div class="col-lg-3"><h6>Contact</h6>
                <ul class="list-unstyled small"><li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Dakar, Sénégal</li><li class="mb-2"><i class="bi bi-envelope me-2"></i>contact@eshopsn.com</li><li class="mb-2"><i class="bi bi-clock me-2"></i>Lun–Sam : 8h–20h</li></ul></div>
        </div>
        <div class="text-center small pt-4 mt-3" style="border-top:1px solid rgba(255,255,255,.1)">&copy; {{ date('Y') }} E-ShopSN — Fait avec ❤️ au Sénégal</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/ui.js') }}?v={{ @filemtime(public_path('js/ui.js')) }}"></script>
@stack('scripts')
</body>
</html>
