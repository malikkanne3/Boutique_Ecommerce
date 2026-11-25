@php
    $authUser = auth()->user();
    $theme    = $authUser->isAdmin() ? 'admin' : ($authUser->isVendeur() ? 'vendeur' : ($authUser->isLivreur() ? 'livreur' : 'client'));
    $mode     = in_array($theme, ['admin', 'vendeur']) ? 'sidebar' : ($theme === 'livreur' ? 'appnav' : 'topnav');
    $navItems = \App\Support\Nav::items($authUser);
    $unread   = $authUser->unreadNotificationsCount();
    $subtitles = ['admin' => 'Administration', 'vendeur' => 'Espace vendeur', 'livreur' => 'Espace livreur', 'client' => 'Mon espace'];
@endphp
<!DOCTYPE html>
<html lang="fr" @if($theme === 'admin') data-bs-theme="dark" @endif>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') — E-Shop SN</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/components.css') }}?v={{ @filemtime(public_path('css/components.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}?v={{ @filemtime(public_path('css/dashboard.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="dash theme-{{ $theme }} layout-{{ $mode }}">

@include('partials.flash')

@if($mode === 'sidebar')
    {{-- ================= SIDEBAR : admin & vendeur ================= --}}
    <aside class="side">
        <a href="{{ route($authUser->dashboardRoute()) }}" class="side-brand">
            <span class="brand-mark"><i class="bi bi-{{ $theme === 'admin' ? 'shield-lock-fill' : 'shop-window' }}"></i></span>
            <span><b>E-Shop SN</b><small>{{ $subtitles[$theme] }}</small></span>
        </a>
        <nav class="side-nav">
            <div class="side-label">Navigation</div>
            @foreach($navItems as [$label, $icon, $route, $match, $badge])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($match) ? 'active' : '' }}">
                    <i class="bi bi-{{ $icon }}"></i>{{ $label }}
                    @if($badge)<span class="nav-badge">{{ $badge }}</span>@endif
                </a>
            @endforeach
            <div class="side-label">Boutique</div>
            <a href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i>Voir la boutique</a>
            @if($theme === 'vendeur')
                <a href="{{ route('vendeur.products.create') }}"><i class="bi bi-plus-circle"></i>Ajouter un produit</a>
            @endif
        </nav>
        <div class="side-foot">
            <div class="side-user mb-3">
                <x-avatar :user="$authUser" />
                <div class="text-truncate"><div class="fw-bold text-truncate">{{ $authUser->display_name }}</div><small>{{ $authUser->role_label }}</small></div>
            </div>
            <form action="{{ route('logout') }}" method="POST">@csrf
                <button class="btn btn-sm w-100 text-white" style="background:rgba(255,255,255,.12)" data-no-loading><i class="bi bi-box-arrow-left me-1"></i>Déconnexion</button>
            </form>
        </div>
    </aside>
    <div class="side-backdrop"></div>

    <div class="main">
        <header class="topbar">
            <button class="icon-btn menu-toggle" data-sidebar-toggle aria-label="Menu"><i class="bi bi-list"></i></button>
            <div class="me-auto">
                <h1>@yield('title', 'Tableau de bord')</h1>
                <div class="crumb">{{ $subtitles[$theme] }}@hasSection('crumb') · @yield('crumb')@endif</div>
            </div>
            <a href="{{ route('messages.index') }}" class="icon-btn" title="Messages"><i class="bi bi-chat-dots"></i>
                @if($authUser->unreadMessagesCount())<span class="dot">{{ $authUser->unreadMessagesCount() }}</span>@endif</a>
            <a href="{{ route('notifications.index') }}" class="icon-btn" title="Notifications"><i class="bi bi-bell"></i>
                @if($unread)<span class="dot">{{ $unread }}</span>@endif</a>
        </header>
        <main class="page">@yield('content')</main>
    </div>

@elseif($mode === 'appnav')
    {{-- ================= APP MOBILE : livreur ================= --}}
    <header class="appbar">
        <div class="appbar-in">
            <span class="brand-mark"><i class="bi bi-truck"></i></span>
            <div class="me-auto lh-sm">
                <div class="fw-bold">Salut {{ \Illuminate\Support\Str::before($authUser->name, ' ') }} 👋</div>
                <small style="opacity:.85">Espace livreur · @yield('title', 'Missions')</small>
            </div>
            <nav class="tabs">
                @foreach($navItems as [$label, $icon, $route, $match, $badge])
                    <a href="{{ route($route) }}" class="{{ request()->routeIs($match) ? 'active' : '' }}"><i class="bi bi-{{ $icon }}"></i>{{ $label }}@if($badge)<span class="nav-badge">{{ $badge }}</span>@endif</a>
                @endforeach
            </nav>
            <a href="{{ route('notifications.index') }}" class="icon-btn"><i class="bi bi-bell"></i>@if($unread)<span class="dot">{{ $unread }}</span>@endif</a>
            <form action="{{ route('logout') }}" method="POST">@csrf<button class="icon-btn" title="Déconnexion" data-no-loading><i class="bi bi-power"></i></button></form>
        </div>
    </header>
    <main class="page">@yield('content')</main>
    <nav class="bottom-nav">
        @foreach($navItems as [$label, $icon, $route, $match, $badge])
            <a href="{{ route($route) }}" class="{{ request()->routeIs($match) ? 'active' : '' }}"><i class="bi bi-{{ $icon }}"></i>{{ $label }}@if($badge)<span class="nav-badge">{{ $badge }}</span>@endif</a>
        @endforeach
    </nav>

@else
    {{-- ================= TOP NAV : client ================= --}}
    <header class="topnav">
        <div class="topnav-in">
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo.svg') }}" alt="E-Shop SN" class="d-none d-sm-block" style="height:36px;width:auto"><img src="{{ asset('images/logo-icon.svg') }}" alt="E-Shop SN" class="d-sm-none" style="height:38px;width:auto">
            </a>
            <nav class="tabs">
                @foreach($navItems as [$label, $icon, $route, $match, $badge])
                    <a href="{{ route($route) }}" class="{{ request()->routeIs($match) ? 'active' : '' }}"><i class="bi bi-{{ $icon }}"></i>{{ $label }}@if($badge)<span class="nav-badge">{{ $badge }}</span>@endif</a>
                @endforeach
            </nav>
            <a href="{{ route('cart.index') }}" class="icon-btn" title="Panier"><i class="bi bi-cart3"></i>
                @if(\App\Support\Cart::count())<span class="dot">{{ \App\Support\Cart::count() }}</span>@endif</a>
            <div class="dropdown">
                <button class="border-0 p-0 bg-transparent rounded-circle" data-bs-toggle="dropdown" aria-label="Mon compte"><x-avatar :user="$authUser" /></button>
                <ul class="dropdown-menu dropdown-menu-end p-2">
                    <li class="px-3 py-2"><div class="fw-bold">{{ $authUser->name }}</div><small class="text-muted">{{ $authUser->email }}</small></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('shop.index') }}"><i class="bi bi-grid me-2"></i>Continuer mes achats</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person-gear me-2"></i>Mon profil</a></li>
                    <li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item text-danger" data-no-loading><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</button></form></li>
                </ul>
            </div>
        </div>
    </header>
    <main class="page">@yield('content')</main>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/ui.js') }}?v={{ @filemtime(public_path('js/ui.js')) }}"></script>
@stack('scripts')
</body>
</html>
