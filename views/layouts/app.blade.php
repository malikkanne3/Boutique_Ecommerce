<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'E-Shop'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
        }
        body { background: #f8fafc; font-family: 'Segoe UI', sans-serif; }
        .navbar { border-bottom: 1px solid #e2e8f0; }
        .navbar-brand { font-size: 1.5rem; color: var(--primary) !important; letter-spacing: -0.5px; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .card { border-radius: 12px; transition: box-shadow .2s; }
        .card:hover { box-shadow: 0 8px 25px rgba(99,102,241,.12) !important; }
        .badge-status-pending   { background: #fef3c7; color: #92400e; }
        .badge-status-paid      { background: #d1fae5; color: #065f46; }
        .badge-status-shipped   { background: #dbeafe; color: #1e40af; }
        .badge-status-cancelled { background: #fee2e2; color: #991b1b; }
        .cart-count { background: var(--primary); color: #fff; border-radius: 50%; padding: 1px 6px; font-size: .7rem; vertical-align: top; }
        footer { border-top: 1px solid #e2e8f0; }
    </style>
    @stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="bi bi-bag-heart-fill"></i> E-Shop
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'fw-semibold' : '' }}" href="{{ route('home') }}">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.*') ? 'fw-semibold' : '' }}" href="{{ route('shop.index') }}">Boutique</a>
                </li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link text-indigo-600" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-gear-fill"></i> Admin
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary position-relative">
                    <i class="bi bi-cart3"></i>
                    @php $cartCount = count(session('cart', [])) @endphp
                    @if($cartCount > 0)
                        <span class="cart-count">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-box-seam me-2"></i>Mes commandes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Connexion</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Inscription</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ALERTS --}}
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- CONTENT --}}
<main class="py-4">
    @yield('content')
</main>

{{-- FOOTER --}}
<footer class="py-4 mt-5 bg-white">
    <div class="container text-center text-muted small">
        &copy; {{ date('Y') }} E-Shop — Tous droits réservés.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
