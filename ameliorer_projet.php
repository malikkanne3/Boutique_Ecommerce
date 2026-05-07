<?php

$base = __DIR__ . '/views';

// Créer les dossiers nécessaires
$dirs = [
    "$base/layouts",
    "$base/home",
    "$base/shop",
    "$base/cart",
    "$base/checkout",
    "$base/orders",
    "$base/admin/dashboard",
    "$base/admin/products",
    "$base/admin/categories",
    "$base/admin/orders",
    "$base/admin/users",
    "$base/errors",
    "$base/partials",
];
foreach ($dirs as $d) { @mkdir($d, 0755, true); }

// ============================================================
// LAYOUT APP (Bootstrap 5 + design premium)
// ============================================================
file_put_contents("$base/layouts/app.blade.php", <<<'BLADE'
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Shop Sénégal') — Boutique en ligne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --primary-light: #ede9fe;
            --accent: #f59e0b;
            --accent-dark: #d97706;
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f8fafc;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: var(--dark);
        }

        /* ── NAVBAR ── */
        .navbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: .75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 12px rgba(0,0,0,.05);
        }
        .navbar-brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary) !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .navbar-brand .brand-icon {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            color: #fff;
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .nav-link {
            font-weight: 500;
            color: var(--gray) !important;
            transition: color .2s;
            padding: .4rem .75rem !important;
            border-radius: 8px;
        }
        .nav-link:hover, .nav-link.active-link {
            color: var(--primary) !important;
            background: var(--primary-light);
        }
        .nav-link.active-link { font-weight: 600; }

        /* Cart badge */
        .cart-btn {
            position: relative;
            background: var(--light-gray);
            border: 1px solid var(--border);
            color: var(--dark);
            border-radius: 12px;
            padding: .45rem .85rem;
            font-weight: 600;
            font-size: .88rem;
            transition: all .2s;
            text-decoration: none;
            display: flex; align-items: center; gap: 6px;
        }
        .cart-btn:hover { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }
        .cart-badge {
            background: var(--primary);
            color: #fff;
            font-size: .65rem;
            font-weight: 700;
            border-radius: 20px;
            padding: 2px 6px;
            min-width: 18px;
            text-align: center;
        }

        /* ── BUTTONS ── */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            border: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all .25s;
            box-shadow: 0 2px 10px rgba(124,58,237,.25);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(124,58,237,.35);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }
        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-outline-primary:hover { background: var(--primary); color: #fff; }
        .btn-accent {
            background: linear-gradient(135deg, var(--accent), #f97316);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(245,158,11,.3);
            transition: all .25s;
        }
        .btn-accent:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245,158,11,.4); color: #fff; }

        /* ── CARDS ── */
        .card {
            border-radius: 14px;
            border: 1px solid var(--border);
            transition: box-shadow .25s, transform .25s;
        }
        .card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.08); }

        /* ── PRODUCT CARD ── */
        .product-card { overflow: hidden; }
        .product-card .product-img-wrap {
            overflow: hidden;
            position: relative;
        }
        .product-card .product-img {
            height: 220px;
            width: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }
        .product-card:hover .product-img { transform: scale(1.06); }
        .product-card .card-body { padding: 1rem; }
        .product-category {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--primary);
            background: var(--primary-light);
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 6px;
        }
        .product-name { font-weight: 700; font-size: .95rem; color: var(--dark); margin-bottom: 4px; }
        .product-price { font-size: 1.05rem; font-weight: 800; color: var(--primary); }
        .product-old-price { font-size: .82rem; color: var(--gray); text-decoration: line-through; }
        .product-actions { display: flex; gap: 8px; margin-top: 10px; }
        .btn-cart {
            flex: 1;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: .85rem;
            padding: .45rem;
            transition: all .2s;
            cursor: pointer;
        }
        .btn-cart:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-view {
            background: var(--light-gray);
            color: var(--dark);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: .85rem;
            padding: .45rem .75rem;
            text-decoration: none;
            transition: all .2s;
            white-space: nowrap;
        }
        .btn-view:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); }

        /* Stock badges */
        .badge-stock-ok    { background: #dcfce7; color: #166534; font-size: .7rem; }
        .badge-stock-low   { background: #fef3c7; color: #92400e; font-size: .7rem; }
        .badge-stock-out   { background: #fee2e2; color: #991b1b; font-size: .7rem; }

        /* ── STATUS BADGES ── */
        .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; border-radius: 20px; font-size: .78rem; font-weight: 600; }
        .badge-status-pending   { background: #fef3c7; color: #92400e; }
        .badge-status-paid      { background: #d1fae5; color: #065f46; }
        .badge-status-shipped   { background: #dbeafe; color: #1e40af; }
        .badge-status-cancelled { background: #fee2e2; color: #991b1b; }

        /* ── ALERTS ── */
        .alert { border-radius: 12px; border: none; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger { background: #fee2e2; color: #991b1b; }

        /* ── SECTION TITLES ── */
        .section-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.5px;
        }
        .section-subtitle { color: var(--gray); font-size: .95rem; }

        /* ── FOOTER ── */
        footer {
            background: var(--dark);
            color: #94a3b8;
            padding: 3rem 0 1.5rem;
            margin-top: 5rem;
        }
        footer h5 { color: #fff; font-weight: 700; }
        footer a { color: #94a3b8; text-decoration: none; transition: color .2s; }
        footer a:hover { color: #fff; }
        footer .footer-brand { font-size: 1.3rem; font-weight: 800; color: #fff; }
        footer .footer-bottom { border-top: 1px solid #1e293b; margin-top: 2rem; padding-top: 1.5rem; }

        /* ── PAGINATION ── */
        .pagination .page-link {
            border-radius: 8px !important;
            margin: 0 2px;
            font-weight: 600;
            color: var(--primary);
            border-color: var(--border);
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* ── FORM ── */
        .form-control, .form-select {
            border-radius: 10px;
            border-color: var(--border);
            font-size: .9rem;
            padding: .6rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(124,58,237,.12);
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .5s ease both; }
        .fade-up-1 { animation-delay: .1s; }
        .fade-up-2 { animation-delay: .2s; }
        .fade-up-3 { animation-delay: .3s; }

        /* Responsive */
        @media (max-width: 576px) {
            .section-title { font-size: 1.3rem; }
            .product-card .product-img { height: 170px; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
            E-Shop<span style="color:#a855f7">SN</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto gap-1 ms-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active-link' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.*') ? 'active-link' : '' }}" href="{{ route('shop.index') }}">
                        <i class="bi bi-grid me-1"></i>Boutique
                    </a>
                </li>
                @auth
                    @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}" style="color:#dc2626!important;">
                            <i class="bi bi-shield-fill me-1"></i>Admin
                        </a>
                    </li>
                    @endif
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-2">
                {{-- Panier --}}
                <a href="{{ route('cart.index') }}" class="cart-btn">
                    <i class="bi bi-cart3"></i>
                    Panier
                    @php $cartCount = array_sum(array_column(session('cart', []), 'quantity')) @endphp
                    @if($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <span style="background:var(--primary);color:#fff;width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;">
                                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                            </span>
                            {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow" style="border-radius:12px;padding:.5rem;">
                            <li><a class="dropdown-item rounded-2" href="{{ route('orders.index') }}"><i class="bi bi-box-seam me-2 text-primary"></i>Mes commandes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item rounded-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Connexion</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">S'inscrire</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ALERTS --}}
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 fade-up">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 fade-up">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- CONTENT --}}
<main class="py-4">
    @yield('content')
</main>

{{-- FOOTER --}}
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand mb-3"><i class="bi bi-bag-heart-fill me-2" style="color:#a855f7"></i>E-ShopSN</div>
                <p style="font-size:.9rem;line-height:1.7;">Votre boutique en ligne de confiance au Sénégal. Livraison rapide dans tout le pays.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#"><i class="bi bi-twitter-x fs-5"></i></a>
                    <a href="#"><i class="bi bi-whatsapp fs-5"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h5 class="mb-3" style="font-size:1rem">Boutique</h5>
                <ul class="list-unstyled" style="font-size:.9rem">
                    <li class="mb-2"><a href="{{ route('shop.index') }}">Tous les produits</a></li>
                    <li class="mb-2"><a href="#">Nouveautés</a></li>
                    <li class="mb-2"><a href="#">Promotions</a></li>
                    <li class="mb-2"><a href="#">Meilleures ventes</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h5 class="mb-3" style="font-size:1rem">Compte</h5>
                <ul class="list-unstyled" style="font-size:.9rem">
                    <li class="mb-2"><a href="{{ route('login') }}">Connexion</a></li>
                    <li class="mb-2"><a href="{{ route('register') }}">Inscription</a></li>
                    <li class="mb-2"><a href="{{ route('orders.index') }}">Mes commandes</a></li>
                    <li class="mb-2"><a href="{{ route('cart.index') }}">Mon panier</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h5 class="mb-3" style="font-size:1rem">Contact</h5>
                <ul class="list-unstyled" style="font-size:.9rem">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2" style="color:#a855f7"></i>Dakar, Sénégal</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2" style="color:#a855f7"></i>+221 77 000 00 00</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2" style="color:#a855f7"></i>contact@eshopsn.com</li>
                    <li class="mb-2"><i class="bi bi-clock me-2" style="color:#a855f7"></i>Lun–Sam: 8h–20h</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <small>&copy; {{ date('Y') }} E-ShopSN — Tous droits réservés | Fait avec ❤️ au Sénégal</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
BLADE);

echo "✅ Layout app créé\n";

// ============================================================
// HOME PAGE
// ============================================================
file_put_contents("$base/home.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Accueil')

@push('styles')
<style>
/* HERO */
.hero-section {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #4c1d95 100%);
    color: #fff;
    padding: 6rem 0 5rem;
    position: relative;
    overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute; inset: 0;
    background: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1400&q=80') center/cover;
    opacity: .07;
}
.hero-section .container { position: relative; }
.hero-badge {
    background: rgba(167,139,250,.2);
    border: 1px solid rgba(167,139,250,.4);
    color: #c4b5fd;
    padding: 5px 16px;
    border-radius: 20px;
    font-size: .82rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1.2rem;
}
.hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: -1.5px;
    margin-bottom: 1.2rem;
}
.hero-title .gradient-text {
    background: linear-gradient(135deg, #c4b5fd, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hero-subtitle { font-size: 1.1rem; color: #94a3b8; max-width: 520px; margin-bottom: 2rem; line-height: 1.7; }
.hero-stats {
    display: flex; gap: 2rem; margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,.1);
}
.hero-stat .number { font-size: 1.6rem; font-weight: 900; color: #fff; }
.hero-stat .label { font-size: .78rem; color: #94a3b8; }

/* Features strip */
.features-strip {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    margin-top: -2rem;
    position: relative;
    z-index: 10;
}
.feature-item { display: flex; align-items: center; gap: 12px; }
.feature-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

/* Category cards */
.cat-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--border);
    padding: 1.5rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: all .25s;
    text-decoration: none;
    color: inherit;
    display: block;
}
.cat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(124,58,237,.12);
    border-color: var(--primary);
    color: var(--primary);
}
.cat-card .cat-icon {
    font-size: 2.2rem;
    margin-bottom: .75rem;
    display: block;
}
.cat-card .cat-name { font-weight: 700; font-size: .95rem; }
.cat-card .cat-count { font-size: .78rem; color: var(--gray); }

/* Promo banner */
.promo-banner {
    background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
    border-radius: 20px;
    padding: 2.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.promo-banner::after {
    content: '🎁';
    position: absolute;
    right: 2rem; top: 50%;
    transform: translateY(-50%);
    font-size: 5rem;
    opacity: .3;
}
.promo-banner h3 { font-weight: 900; font-size: 1.6rem; letter-spacing: -0.5px; }
</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-up">
                <div class="hero-badge"><i class="bi bi-lightning-charge-fill me-1"></i>Livraison express disponible</div>
                <h1 class="hero-title">
                    Shopping en ligne <br>
                    <span class="gradient-text">simple & rapide</span>
                </h1>
                <p class="hero-subtitle">
                    Des milliers de produits de qualité livrés directement chez vous au Sénégal. Électronique, mode, alimentation et bien plus.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('shop.index') }}" class="btn btn-accent btn-lg px-5">
                        <i class="bi bi-grid me-2"></i>Explorer la boutique
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-lg px-5" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:10px;">
                        <i class="bi bi-person-plus me-2"></i>Créer un compte
                    </a>
                    @endguest
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="number">2K+</div>
                        <div class="label">Produits</div>
                    </div>
                    <div class="hero-stat">
                        <div class="number">500+</div>
                        <div class="label">Clients</div>
                    </div>
                    <div class="hero-stat">
                        <div class="number">48h</div>
                        <div class="label">Livraison</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-flex justify-content-end fade-up fade-up-2">
                <div style="position:relative;">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=600&q=80"
                         style="width:420px;height:420px;object-fit:cover;border-radius:24px;box-shadow:0 30px 60px rgba(0,0,0,.4);"
                         alt="Shopping">
                    <div style="position:absolute;bottom:-20px;left:-20px;background:#fff;border-radius:14px;padding:12px 18px;box-shadow:0 8px 25px rgba(0,0,0,.15);">
                        <div style="font-size:.75rem;color:var(--gray);font-weight:600;">Commande livrée 🎉</div>
                        <div style="font-weight:800;color:var(--dark);font-size:.9rem;">Dakar, Plateau</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">

{{-- FEATURES STRIP --}}
<div class="features-strip mb-5 fade-up fade-up-1">
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div class="feature-item">
                <div class="feature-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-truck"></i></div>
                <div>
                    <div style="font-weight:700;font-size:.9rem">Livraison rapide</div>
                    <div style="font-size:.78rem;color:var(--gray)">48h dans Dakar</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-item">
                <div class="feature-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div style="font-weight:700;font-size:.9rem">Paiement sécurisé</div>
                    <div style="font-size:.78rem;color:var(--gray)">Wave, Orange Money</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-item">
                <div class="feature-icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-arrow-counterclockwise"></i></div>
                <div>
                    <div style="font-weight:700;font-size:.9rem">Retour facile</div>
                    <div style="font-size:.78rem;color:var(--gray)">7 jours pour changer</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-item">
                <div class="feature-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-headset"></i></div>
                <div>
                    <div style="font-weight:700;font-size:.9rem">Support 7j/7</div>
                    <div style="font-size:.78rem;color:var(--gray)">8h – 20h</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CATÉGORIES --}}
@if($categories->count())
<section class="mb-5 fade-up fade-up-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">Nos catégories</h2>
            <p class="section-subtitle mb-0">Trouvez ce que vous cherchez rapidement</p>
        </div>
    </div>
    <div class="row g-3">
        @php
        $catIcons = ['📱','👕','🥗','🏠','💄','⚽','📚','🎮','🛒','🎵','💻','🎨'];
        $catColors = [
            ['background:#f3e8ff;color:#7c3aed'],
            ['background:#dbeafe;color:#1d4ed8'],
            ['background:#d1fae5;color:#059669'],
            ['background:#fef3c7;color:#d97706'],
            ['background:#fce7f3;color:#be185d'],
            ['background:#e0f2fe;color:#0284c7'],
        ];
        @endphp
        @foreach($categories as $i => $cat)
        <div class="col-4 col-md-3 col-lg-2">
            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="cat-card">
                <span class="cat-icon">{{ $catIcons[$i % count($catIcons)] }}</span>
                <div class="cat-name">{{ $cat->name }}</div>
                <div class="cat-count">{{ $cat->products_count }} produits</div>
            </a>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- PRODUITS VEDETTES --}}
@if($featured->count())
<section class="mb-5 fade-up fade-up-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">Produits vedettes</h2>
            <p class="section-subtitle mb-0">Sélection de nos meilleures offres</p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">
            Voir tout <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="row g-4">
        @foreach($featured as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card border-0 shadow-sm h-100">
                <div class="product-img-wrap">
                    <img src="{{ $product->image_url }}" class="product-img" alt="{{ $product->name }}">
                    @if($product->stock <= 3 && $product->stock > 0)
                        <span class="position-absolute top-0 end-0 m-2 badge badge-stock-low">Stock limité</span>
                    @endif
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="product-category">{{ $product->category->name ?? '' }}</div>
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-price mt-auto">{{ $product->formatted_price }}</div>
                    <div class="product-actions">
                        <a href="{{ route('shop.show', $product->slug) }}" class="btn-view">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" style="flex:1">
                            @csrf
                            <button class="btn-cart w-100">
                                <i class="bi bi-cart-plus me-1"></i>Ajouter
                            </button>
                        </form>
                        @else
                        <button class="btn-cart w-100" disabled style="background:#94a3b8;flex:1">Indisponible</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- PROMO BANNER --}}
<section class="mb-5 fade-up">
    <div class="promo-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div style="font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;opacity:.8;margin-bottom:.5rem;">Offre limitée</div>
                <h3>Livraison GRATUITE dès 25 000 FCFA d'achat !</h3>
                <p style="opacity:.9;margin-bottom:1.5rem;">Profitez de cette offre sur toute la ville de Dakar. Valable jusqu'à fin du mois.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-lg" style="background:#fff;color:#d97706;font-weight:700;border-radius:10px;padding:.6rem 2rem;">
                    En profiter <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- NOUVEAUX PRODUITS --}}
@if($newProducts->count())
<section class="mb-5 fade-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">Nouveautés</h2>
            <p class="section-subtitle mb-0">Les derniers arrivages</p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">
            Tout voir <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="row g-4">
        @foreach($newProducts as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card border-0 shadow-sm h-100">
                <div class="product-img-wrap">
                    <img src="{{ $product->image_url }}" class="product-img" alt="{{ $product->name }}">
                    <span class="position-absolute top-0 start-0 m-2 badge" style="background:var(--primary);color:#fff;border-radius:8px;font-size:.7rem;">Nouveau</span>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="product-category">{{ $product->category->name ?? '' }}</div>
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-price mt-auto">{{ $product->formatted_price }}</div>
                    <div class="product-actions">
                        <a href="{{ route('shop.show', $product->slug) }}" class="btn-view"><i class="bi bi-eye"></i></a>
                        @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" style="flex:1">
                            @csrf
                            <button class="btn-cart w-100"><i class="bi bi-cart-plus me-1"></i>Ajouter</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

</div>{{-- /container --}}
@endsection
BLADE);

echo "✅ Home créée\n";

// ============================================================
// SHOP INDEX
// ============================================================
file_put_contents("$base/shop/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Boutique')

@push('styles')
<style>
.shop-header {
    background: linear-gradient(135deg, #0f172a, #1e1b4b);
    color: #fff;
    padding: 3rem 0;
    margin-bottom: 2rem;
}
.shop-header h1 { font-size: 2rem; font-weight: 900; letter-spacing: -1px; }
.filter-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid var(--border);
    position: sticky;
    top: 80px;
}
.filter-title { font-weight: 700; font-size: .85rem; text-transform: uppercase; letter-spacing: .7px; color: var(--gray); margin-bottom: .75rem; }
.cat-filter-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: .45rem .75rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all .15s;
    font-size: .9rem;
    text-decoration: none;
    color: var(--dark);
}
.cat-filter-item:hover { background: var(--primary-light); color: var(--primary); }
.cat-filter-item.active { background: var(--primary); color: #fff; font-weight: 600; }
.cat-filter-item .count-pill {
    font-size: .7rem;
    padding: 1px 7px;
    border-radius: 20px;
    background: rgba(0,0,0,.07);
}
.cat-filter-item.active .count-pill { background: rgba(255,255,255,.25); }
.sort-select { border-radius: 10px; border-color: var(--border); font-size: .88rem; }
.product-count { font-weight: 700; color: var(--dark); }
</style>
@endpush

@section('content')

<div class="shop-header">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:#94a3b8">Accueil</a></li>
                <li class="breadcrumb-item active" style="color:#c4b5fd">Boutique</li>
            </ol>
        </nav>
        <h1>Notre boutique</h1>
        <p style="color:#94a3b8;margin:0">{{ $products->total() }} produit(s) disponible(s)</p>
    </div>
</div>

<div class="container">
    <div class="row g-4">

        {{-- SIDEBAR FILTRES --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="filter-card">

                {{-- Recherche --}}
                <div class="filter-title">Recherche</div>
                <form method="GET" id="searchForm">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <div class="input-group mb-4">
                        <input type="text" name="search" class="form-control border-end-0"
                               placeholder="Rechercher..." value="{{ request('search') }}"
                               style="border-radius:10px 0 0 10px">
                        <button class="btn btn-primary border-0" style="border-radius:0 10px 10px 0">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                {{-- Catégories --}}
                <div class="filter-title">Catégories</div>
                <a href="{{ route('shop.index', array_merge(request()->except('category'), ['search' => request('search'), 'sort' => request('sort')])) }}"
                   class="cat-filter-item mb-1 {{ !request('category') ? 'active' : '' }}">
                    <span>Toutes</span>
                    <span class="count-pill">{{ $allCount }}</span>
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('shop.index', array_merge(request()->except('category'), ['category' => $cat->slug, 'search' => request('search'), 'sort' => request('sort')])) }}"
                   class="cat-filter-item mb-1 {{ request('category') == $cat->slug ? 'active' : '' }}">
                    <span>{{ $cat->name }}</span>
                    <span class="count-pill">{{ $cat->products_count }}</span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- PRODUITS --}}
        <div class="col-lg-9">

            {{-- Barre supérieure --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    @if(request('search') || request('category'))
                        <span class="badge" style="background:var(--primary-light);color:var(--primary);border-radius:8px;padding:6px 12px;font-size:.82rem;">
                            {{ $products->total() }} résultat(s)
                            @if(request('search')) pour "{{ request('search') }}"@endif
                        </span>
                        <a href="{{ route('shop.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="bi bi-x"></i> Réinitialiser
                        </a>
                    @else
                        <span class="product-count">{{ $products->total() }} produits</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    {{-- Filtre mobile --}}
                    <button class="btn btn-outline-secondary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#filterCanvas">
                        <i class="bi bi-funnel me-1"></i>Filtres
                    </button>
                    <form method="GET">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="sort" class="form-select sort-select" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Plus récents</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom A-Z</option>
                        </select>
                    </form>
                </div>
            </div>

            @if($products->count())
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-6 col-md-4">
                    <div class="card product-card border-0 shadow-sm h-100">
                        <div class="product-img-wrap">
                            <img src="{{ $product->image_url }}" class="product-img" alt="{{ $product->name }}">
                            @if($product->stock == 0)
                                <div style="position:absolute;inset:0;background:rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;border-radius:14px 14px 0 0;">
                                    <span style="background:#fff;color:#991b1b;font-weight:700;font-size:.8rem;padding:4px 12px;border-radius:20px;">Rupture</span>
                                </div>
                            @elseif($product->stock <= 3)
                                <span class="position-absolute top-0 end-0 m-2 badge" style="background:#fef3c7;color:#92400e;font-size:.7rem;border-radius:8px;">{{ $product->stock }} restant(s)</span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <div class="product-category">{{ $product->category->name ?? '' }}</div>
                            <div class="product-name">{{ Str::limit($product->name, 40) }}</div>
                            <div class="product-price mt-auto">{{ $product->formatted_price }}</div>
                            <div class="product-actions">
                                <a href="{{ route('shop.show', $product->slug) }}" class="btn-view">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" style="flex:1">
                                    @csrf
                                    <button class="btn-cart w-100">
                                        <i class="bi bi-cart-plus me-1"></i>Ajouter
                                    </button>
                                </form>
                                @else
                                <button class="btn-cart w-100" disabled style="background:#94a3b8;flex:1">Indisponible</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->withQueryString()->links() }}
            </div>

            @else
            <div class="text-center py-5">
                <div style="font-size:4rem;margin-bottom:1rem">🔍</div>
                <h5 class="fw-bold mb-2">Aucun produit trouvé</h5>
                <p class="text-muted mb-4">Essayez d'autres mots-clés ou catégories.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary px-5">Voir tous les produits</a>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- OFFCANVAS FILTRES MOBILE --}}
<div class="offcanvas offcanvas-start" id="filterCanvas" style="width:280px">
    <div class="offcanvas-header border-bottom">
        <h6 class="offcanvas-title fw-bold">Filtres</h6>
        <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET">
            <div class="mb-4">
                <div class="filter-title">Recherche</div>
                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
            </div>
            <div class="mb-4">
                <div class="filter-title">Catégorie</div>
                <select name="category" class="form-select">
                    <option value="">Toutes</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary w-100">Appliquer</button>
        </form>
    </div>
</div>
@endsection
BLADE);

echo "✅ Shop index créée\n";

// ============================================================
// SHOP SHOW
// ============================================================
file_put_contents("$base/shop/show.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', $product->name)

@push('styles')
<style>
.product-detail-img {
    width: 100%;
    height: 420px;
    object-fit: cover;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,.12);
}
.price-box {
    background: linear-gradient(135deg, var(--primary-light), #f0fdf4);
    border-radius: 14px;
    padding: 1.25rem;
    margin: 1rem 0;
}
.price-main { font-size: 2.2rem; font-weight: 900; color: var(--primary); }
.guarantee-item { display: flex; align-items: center; gap: 10px; padding: .6rem 0; border-bottom: 1px solid var(--border); }
.guarantee-item:last-child { border: none; }
.guarantee-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
.related-img { height: 160px; object-fit: cover; border-radius: 12px 12px 0 0; }
</style>
@endpush

@section('content')
<div class="container">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size:.85rem">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Boutique</a></li>
            @if($product->category)
            <li class="breadcrumb-item">
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
            </li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5 mb-5">

        {{-- IMAGE --}}
        <div class="col-md-5 fade-up">
            <img src="{{ $product->image_url }}" class="product-detail-img" alt="{{ $product->name }}">
        </div>

        {{-- INFOS --}}
        <div class="col-md-7 fade-up fade-up-1">
            @if($product->category)
            <div class="product-category mb-2">{{ $product->category->name }}</div>
            @endif
            <h1 style="font-size:1.9rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:.5rem">{{ $product->name }}</h1>

            {{-- Stock --}}
            @if($product->stock > 5)
                <span class="badge badge-stock-ok mb-3 px-3 py-2">
                    <i class="bi bi-check-circle-fill me-1"></i>En stock ({{ $product->stock }} disponibles)
                </span>
            @elseif($product->stock > 0)
                <span class="badge badge-stock-low mb-3 px-3 py-2">
                    <i class="bi bi-exclamation-circle-fill me-1"></i>Stock limité ({{ $product->stock }} restants)
                </span>
            @else
                <span class="badge badge-stock-out mb-3 px-3 py-2">
                    <i class="bi bi-x-circle-fill me-1"></i>Rupture de stock
                </span>
            @endif

            {{-- Prix --}}
            <div class="price-box">
                <div class="price-main">{{ $product->formatted_price }}</div>
                <div style="font-size:.82rem;color:var(--gray);margin-top:.25rem">Prix TTC — Livraison calculée au panier</div>
            </div>

            {{-- Description --}}
            @if($product->description)
            <div class="mb-4">
                <h6 style="font-weight:700;margin-bottom:.6rem">À propos de ce produit</h6>
                <p style="color:var(--gray);line-height:1.8;font-size:.95rem">{{ $product->description }}</p>
            </div>
            @endif

            {{-- CTA --}}
            @if($product->stock > 0)
            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-4">
                @csrf
                <button class="btn btn-primary btn-lg px-5 w-100">
                    <i class="bi bi-cart-plus me-2"></i>Ajouter au panier
                </button>
            </form>
            @else
            <button class="btn btn-secondary btn-lg w-100 mb-4" disabled>
                <i class="bi bi-x-circle me-2"></i>Indisponible
            </button>
            @endif

            {{-- Garanties --}}
            <div class="guarantee-item">
                <div class="guarantee-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div style="font-weight:600;font-size:.9rem">Paiement sécurisé</div>
                    <div style="font-size:.78rem;color:var(--gray)">Wave, Orange Money, Carte bancaire</div>
                </div>
            </div>
            <div class="guarantee-item">
                <div class="guarantee-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-truck"></i></div>
                <div>
                    <div style="font-weight:600;font-size:.9rem">Livraison rapide</div>
                    <div style="font-size:.78rem;color:var(--gray)">48h dans Dakar, 5 jours en région</div>
                </div>
            </div>
            <div class="guarantee-item">
                <div class="guarantee-icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-arrow-counterclockwise"></i></div>
                <div>
                    <div style="font-weight:600;font-size:.9rem">Retour sous 7 jours</div>
                    <div style="font-size:.78rem;color:var(--gray)">Remboursement complet garanti</div>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUITS SIMILAIRES --}}
    @if($related->count())
    <section class="mb-5">
        <h3 class="section-title mb-4">Produits similaires</h3>
        <div class="row g-3">
            @foreach($related as $rel)
            <div class="col-6 col-md-3">
                <div class="card product-card border-0 shadow-sm h-100">
                    <div class="product-img-wrap">
                        <img src="{{ $rel->image_url }}" class="product-img" alt="{{ $rel->name }}">
                    </div>
                    <div class="card-body p-3">
                        <div class="product-category">{{ $rel->category->name ?? '' }}</div>
                        <div class="product-name">{{ Str::limit($rel->name, 35) }}</div>
                        <div class="product-price">{{ $rel->formatted_price }}</div>
                        <div class="product-actions">
                            <a href="{{ route('shop.show', $rel->slug) }}" class="btn-view"><i class="bi bi-eye"></i></a>
                            @if($rel->stock > 0)
                            <form action="{{ route('cart.add', $rel->id) }}" method="POST" style="flex:1">
                                @csrf
                                <button class="btn-cart w-100"><i class="bi bi-cart-plus"></i></button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
BLADE);

echo "✅ Shop show créée\n";

// ============================================================
// CART
// ============================================================
file_put_contents("$base/cart/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Mon panier')

@push('styles')
<style>
.cart-page-title { font-size: 1.8rem; font-weight: 900; letter-spacing: -0.5px; }
.cart-item-img { width: 90px; height: 90px; object-fit: cover; border-radius: 12px; flex-shrink: 0; }
.cart-item-name { font-weight: 700; font-size: .95rem; }
.cart-item-cat { font-size: .78rem; color: var(--gray); }
.cart-item-price { color: var(--gray); font-size: .85rem; }
.qty-control { display: flex; align-items: center; gap: 4px; }
.qty-btn { width: 30px; height: 30px; border-radius: 8px; border: 1px solid var(--border); background: #fff; font-weight: 700; cursor: pointer; transition: all .15s; display: flex; align-items: center; justify-content: center; }
.qty-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.qty-val { width: 40px; text-align: center; font-weight: 700; font-size: .9rem; }
.cart-subtotal { font-weight: 800; color: var(--primary); font-size: .95rem; white-space: nowrap; }
.summary-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 1.5rem; position: sticky; top: 85px; }
.summary-row { display: flex; justify-content: space-between; font-size: .9rem; margin-bottom: .6rem; }
.summary-total { display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 900; border-top: 2px solid var(--border); padding-top: 1rem; margin-top: .5rem; }
.empty-cart { text-align: center; padding: 5rem 0; }
.empty-cart .empty-icon { font-size: 5rem; margin-bottom: 1.5rem; }
</style>
@endpush

@section('content')
<div class="container">
    <h1 class="cart-page-title mb-1">Mon panier</h1>
    <p class="text-muted mb-4">{{ count($cart) }} article(s)</p>

    @if(count($cart) > 0)
    <div class="row g-4">

        {{-- ARTICLES --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @foreach($cart as $productId => $item)
                    <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=200&q=80' }}"
                             class="cart-item-img" alt="{{ $item['name'] }}">

                        <div class="flex-grow-1 min-w-0">
                            <div class="cart-item-name">{{ $item['name'] }}</div>
                            <div class="cart-item-price">{{ number_format($item['price'], 0, ',', ' ') }} FCFA / unité</div>
                        </div>

                        <form action="{{ route('cart.update', $productId) }}" method="POST" class="qty-control">
                            @csrf
                            <button type="submit" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}" class="qty-btn">−</button>
                            <span class="qty-val">{{ $item['quantity'] }}</span>
                            <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="qty-btn">+</button>
                        </form>

                        <div class="cart-subtotal d-none d-sm-block">
                            {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA
                        </div>

                        <form action="{{ route('cart.remove', $productId) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger border-0" style="border-radius:8px" title="Supprimer">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center" style="border-radius:0 0 14px 14px">
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Continuer les achats
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash3 me-1"></i>Vider le panier
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RÉSUMÉ --}}
        <div class="col-lg-4">
            <div class="summary-card">
                <h5 style="font-weight:800;margin-bottom:1.25rem">Résumé</h5>

                @foreach($cart as $item)
                <div class="summary-row text-muted">
                    <span>{{ Str::limit($item['name'], 25) }} × {{ $item['quantity'] }}</span>
                    <span>{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}</span>
                </div>
                @endforeach

                <div class="summary-row text-muted">
                    <span>Livraison</span>
                    <span>{{ $total >= 25000 ? 'Gratuite 🎉' : 'Calculée au checkout' }}</span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span style="color:var(--primary)">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>

                @if($total < 25000)
                <div style="background:#fef3c7;border-radius:10px;padding:.75rem;margin:1rem 0;font-size:.82rem;color:#92400e">
                    <i class="bi bi-truck me-1"></i>
                    Ajoutez encore {{ number_format(25000 - $total, 0, ',', ' ') }} FCFA pour la livraison gratuite !
                </div>
                @endif

                @auth
                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 btn-lg mt-3">
                    <i class="bi bi-credit-card me-2"></i>Passer la commande
                </a>
                @else
                <a href="{{ route('login') }}" class="btn btn-primary w-100 btn-lg mt-3">
                    <i class="bi bi-lock me-2"></i>Se connecter pour commander
                </a>
                <div class="text-center small text-muted mt-2">
                    Pas de compte ? <a href="{{ route('register') }}">Créer un compte</a>
                </div>
                @endauth

                {{-- Moyens de paiement --}}
                <div class="text-center mt-3">
                    <div style="font-size:.75rem;color:var(--gray);margin-bottom:.5rem">Paiements acceptés</div>
                    <div style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap">
                        <span style="background:#f1f5f9;border-radius:6px;padding:3px 10px;font-size:.75rem;font-weight:600;">Wave</span>
                        <span style="background:#f1f5f9;border-radius:6px;padding:3px 10px;font-size:.75rem;font-weight:600;">Orange Money</span>
                        <span style="background:#f1f5f9;border-radius:6px;padding:3px 10px;font-size:.75rem;font-weight:600;">Visa</span>
                        <span style="background:#f1f5f9;border-radius:6px;padding:3px 10px;font-size:.75rem;font-weight:600;">Cash</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @else
    <div class="empty-cart">
        <div class="empty-icon">🛒</div>
        <h4 style="font-weight:800;margin-bottom:.5rem">Votre panier est vide</h4>
        <p class="text-muted mb-4">Découvrez nos produits et ajoutez vos favoris !</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-grid me-2"></i>Découvrir la boutique
        </a>
    </div>
    @endif
</div>
@endsection
BLADE);

echo "✅ Cart créée\n";

// ============================================================
// CHECKOUT
// ============================================================
file_put_contents("$base/checkout/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Finaliser la commande')

@push('styles')
<style>
.checkout-step { display: flex; align-items: center; gap: 12px; margin-bottom: 2rem; }
.step-num {
    width: 36px; height: 36px; border-radius: 50%;
    background: var(--primary); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .9rem; flex-shrink: 0;
}
.step-line { flex: 1; height: 2px; background: var(--border); }
.form-section { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 1.75rem; margin-bottom: 1.5rem; }
.form-section-title { font-weight: 800; font-size: 1rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px; }
.form-section-icon { width: 32px; height: 32px; background: var(--primary-light); color: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.order-summary { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 1.5rem; position: sticky; top: 85px; }
.order-item { display: flex; align-items: center; gap: 12px; margin-bottom: 1rem; }
.order-item-img { width: 52px; height: 52px; object-fit: cover; border-radius: 10px; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="container" style="max-width:1000px">
    <h1 style="font-size:1.7rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:2rem">
        <i class="bi bi-bag-check me-2" style="color:var(--primary)"></i>Finaliser la commande
    </h1>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="row g-4">

            {{-- FORMULAIRE --}}
            <div class="col-lg-7">
                <div class="form-section">
                    <div class="form-section-title">
                        <div class="form-section-icon"><i class="bi bi-person"></i></div>
                        Informations de livraison
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:.85rem">Nom complet *</label>
                            <input type="text" name="customer_name"
                                   class="form-control @error('customer_name') is-invalid @enderror"
                                   value="{{ old('customer_name', auth()->user()->name) }}" required
                                   placeholder="Ex: Moussa Diallo">
                            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem">Email *</label>
                            <input type="email" name="customer_email"
                                   class="form-control @error('customer_email') is-invalid @enderror"
                                   value="{{ old('customer_email', auth()->user()->email) }}" required>
                            @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem">Téléphone</label>
                            <input type="text" name="customer_phone"
                                   class="form-control @error('customer_phone') is-invalid @enderror"
                                   value="{{ old('customer_phone') }}" placeholder="+221 77 000 00 00">
                            @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:.85rem">Adresse de livraison *</label>
                            <textarea name="shipping_address"
                                      class="form-control @error('shipping_address') is-invalid @enderror"
                                      rows="3" required
                                      placeholder="Quartier, rue, numéro de maison, ville...">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <div class="form-section-icon"><i class="bi bi-credit-card"></i></div>
                        Moyen de paiement
                    </div>
                    <div class="row g-2">
                        @foreach([
                            ['wave', 'Wave', '📱', '#1e40af'],
                            ['orange_money', 'Orange Money', '🟠', '#ea580c'],
                            ['cash', 'Paiement à la livraison', '💵', '#059669'],
                        ] as [$val, $label, $icon, $color])
                        <div class="col-12">
                            <label style="display:flex;align-items:center;gap:12px;background:#f8fafc;border-radius:10px;padding:.75rem 1rem;border:2px solid transparent;cursor:pointer;transition:all .2s"
                                   onclick="this.style.borderColor='{{ $color }}'">
                                <input type="radio" name="payment_method" value="{{ $val }}" style="display:none" checked>
                                <span style="font-size:1.4rem">{{ $icon }}</span>
                                <span style="font-weight:600;font-size:.9rem">{{ $label }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RÉSUMÉ --}}
            <div class="col-lg-5">
                <div class="order-summary">
                    <h6 style="font-weight:800;margin-bottom:1.25rem">
                        <i class="bi bi-receipt me-2 text-primary"></i>Votre commande
                    </h6>

                    @php $total = 0; @endphp
                    @foreach($cart as $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp
                    <div class="order-item">
                        <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=100&q=80' }}"
                             class="order-item-img" alt="{{ $item['name'] }}">
                        <div class="flex-grow-1">
                            <div style="font-weight:600;font-size:.88rem">{{ Str::limit($item['name'], 30) }}</div>
                            <div style="font-size:.78rem;color:var(--gray)">× {{ $item['quantity'] }}</div>
                        </div>
                        <div style="font-weight:700;font-size:.88rem">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    @endforeach

                    <hr>
                    <div class="summary-row d-flex justify-content-between text-muted mb-2" style="font-size:.88rem">
                        <span>Sous-total</span>
                        <span>{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="summary-row d-flex justify-content-between text-muted mb-3" style="font-size:.88rem">
                        <span>Livraison</span>
                        <span>{{ $total >= 25000 ? 'Gratuite 🎉' : 'À déterminer' }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold" style="font-size:1.15rem">
                        <span>Total</span>
                        <span style="color:var(--primary)">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg mt-4">
                        <i class="bi bi-check2-circle me-2"></i>Confirmer la commande
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left me-1"></i>Retour au panier
                    </a>
                    <div class="text-center mt-3" style="font-size:.78rem;color:var(--gray)">
                        <i class="bi bi-lock-fill me-1"></i>Transaction 100% sécurisée
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
BLADE);

echo "✅ Checkout créée\n";

// ============================================================
// ORDERS INDEX
// ============================================================
file_put_contents("$base/orders/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Mes commandes')

@section('content')
<div class="container" style="max-width:900px">
    <h1 style="font-size:1.7rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:.5rem">Mes commandes</h1>
    <p class="text-muted mb-4">Suivez l'état de vos commandes</p>

    @if($orders->count())
    <div class="d-flex flex-column gap-3">
        @foreach($orders as $order)
        <div class="card border-0 shadow-sm" style="border-radius:16px">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div style="font-size:.75rem;color:var(--gray);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Commande</div>
                        <div style="font-weight:800;font-size:1.05rem;font-family:monospace">{{ $order->order_number }}</div>
                        <div style="font-size:.82rem;color:var(--gray);margin-top:4px">
                            <i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d/m/Y à H:i') }}
                            · {{ $order->products->count() }} article(s)
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-status badge-status-{{ $order->status }} mb-2">
                            @if($order->status === 'pending') <i class="bi bi-clock me-1"></i>
                            @elseif($order->status === 'paid') <i class="bi bi-check-circle me-1"></i>
                            @elseif($order->status === 'shipped') <i class="bi bi-truck me-1"></i>
                            @else <i class="bi bi-x-circle me-1"></i> @endif
                            {{ $order->status_label }}
                        </span>
                        <div style="font-size:1.2rem;font-weight:900;color:var(--primary)">{{ $order->formatted_total }}</div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        @foreach($order->products->take(3) as $product)
                        <img src="{{ $product->image_url }}" style="width:40px;height:40px;object-fit:cover;border-radius:8px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.1)" alt="">
                        @endforeach
                        @if($order->products->count() > 3)
                        <span style="width:40px;height:40px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:var(--gray)">+{{ $order->products->count() - 3 }}</span>
                        @endif
                    </div>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>Voir le détail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $orders->links() }}</div>
    @else
    <div class="text-center py-5">
        <div style="font-size:4rem;margin-bottom:1rem">📦</div>
        <h5 style="font-weight:800;margin-bottom:.5rem">Aucune commande pour l'instant</h5>
        <p class="text-muted mb-4">Commencez à faire du shopping !</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary px-5">Découvrir la boutique</a>
    </div>
    @endif
</div>
@endsection
BLADE);

// ORDERS SHOW
file_put_contents("$base/orders/show.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Commande ' . $order->order_number)

@section('content')
<div class="container" style="max-width:900px">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left me-1"></i>Mes commandes
            </a>
            <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:.25rem">{{ $order->order_number }}</h1>
            <p class="text-muted mb-0" style="font-size:.85rem">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <span class="badge badge-status badge-status-{{ $order->status }} px-4 py-2 fs-6">
            {{ $order->status_label }}
        </span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Articles --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px">
                <div class="card-header bg-white fw-bold border-0 pt-4 pb-0 px-4">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Articles commandés
                </div>
                <div class="card-body p-0">
                    @foreach($order->products as $product)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <img src="{{ $product->image_url }}" style="width:65px;height:65px;object-fit:cover;border-radius:12px;flex-shrink:0" alt="{{ $product->name }}">
                        <div class="flex-grow-1">
                            <div style="font-weight:700;font-size:.92rem">{{ $product->name }}</div>
                            <div style="font-size:.78rem;color:var(--gray)">{{ $product->category->name ?? '' }}</div>
                            <div style="font-size:.82rem;color:var(--gray)">{{ number_format($product->pivot->price, 0, ',', ' ') }} FCFA × {{ $product->pivot->quantity }}</div>
                        </div>
                        <div style="font-weight:800;color:var(--primary)">{{ number_format($product->pivot->price * $product->pivot->quantity, 0, ',', ' ') }} FCFA</div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white border-0 px-4 py-3 d-flex justify-content-between fw-bold" style="font-size:1.1rem;border-top:2px solid var(--border)!important">
                    <span>Total</span>
                    <span style="color:var(--primary)">{{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Infos client --}}
            <div class="card border-0 shadow-sm" style="border-radius:16px">
                <div class="card-header bg-white fw-bold border-0 pt-4 pb-0 px-4">
                    <i class="bi bi-person me-2 text-primary"></i>Livraison
                </div>
                <div class="card-body px-4" style="font-size:.88rem">
                    <div class="mb-2"><strong>Nom :</strong> {{ $order->customer_name }}</div>
                    <div class="mb-2"><strong>Email :</strong> {{ $order->customer_email }}</div>
                    @if($order->customer_phone)
                    <div class="mb-2"><strong>Tél :</strong> {{ $order->customer_phone }}</div>
                    @endif
                    <div><strong>Adresse :</strong> {{ $order->shipping_address }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE);

echo "✅ Orders créées\n";

// ============================================================
// ADMIN DASHBOARD
// ============================================================
file_put_contents("$base/admin/dashboard/index.blade.php", <<<'BLADE'
@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
{{-- STATS --}}
<div class="row g-3 mb-4">
    @php
    $statCards = [
        ['label'=>'Clients',    'value'=>$stats['users'],    'icon'=>'people-fill',      'bg'=>'#ede9fe', 'color'=>'#7c3aed'],
        ['label'=>'Produits',   'value'=>$stats['products'], 'icon'=>'box-seam-fill',    'bg'=>'#dbeafe', 'color'=>'#1d4ed8'],
        ['label'=>'Catégories', 'value'=>$stats['categories'],'icon'=>'tag-fill',        'bg'=>'#d1fae5', 'color'=>'#059669'],
        ['label'=>'Commandes',  'value'=>$stats['orders'],   'icon'=>'receipt',          'bg'=>'#fef3c7', 'color'=>'#d97706'],
        ['label'=>'En attente', 'value'=>$stats['pending'],  'icon'=>'clock-fill',       'bg'=>'#fee2e2', 'color'=>'#dc2626'],
        ['label'=>'Revenus',    'value'=>number_format($stats['revenue'],0,',',' ').' FCFA', 'icon'=>'currency-dollar','bg'=>'#f0fdf4','color'=>'#16a34a'],
    ];
    @endphp
    @foreach($statCards as $s)
    <div class="col-6 col-lg-4 col-xl-2">
        <div class="card stat-card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:{{ $s['bg'] }}">
                    <i class="bi bi-{{ $s['icon'] }}" style="color:{{ $s['color'] }}"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5 lh-1 mb-1">{{ $s['value'] }}</div>
                    <div class="text-muted" style="font-size:.78rem">{{ $s['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- DERNIÈRES COMMANDES --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Dernières commandes</span>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestOrders as $order)
                        <tr style="cursor:pointer" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                            <td class="small fw-semibold">{{ $order->order_number }}</td>
                            <td class="small">{{ $order->customer_name }}</td>
                            <td class="small fw-bold">{{ $order->formatted_total }}</td>
                            <td><span class="badge badge-status badge-status-{{ $order->status }}">{{ $order->status_label }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Aucune commande</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- STOCK FAIBLE --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-exclamation-triangle text-warning me-1"></i>Stock faible</span>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-warning">Gérer</a>
            </div>
            <div class="card-body p-0">
                @forelse($lowStock as $product)
                <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <img src="{{ $product->image_url }}" style="width:40px;height:40px;object-fit:cover;border-radius:8px;flex-shrink:0" alt="">
                    <div class="flex-grow-1">
                        <div class="small fw-semibold">{{ $product->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $product->category->name ?? '' }}</div>
                    </div>
                    <span class="badge bg-{{ $product->stock == 0 ? 'danger' : 'warning text-dark' }}">
                        {{ $product->stock }} restant(s)
                    </span>
                </div>
                @empty
                <div class="p-4 text-center text-muted small">Aucun produit en stock faible 🎉</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
BLADE);

echo "✅ Admin dashboard créée\n";

// ADMIN PRODUCTS INDEX (gardé tel quel, déjà bien)
// ADMIN CATEGORIES INDEX (gardé tel quel)
// ADMIN ORDERS (gardés)

echo "\n✅ TOUTES LES VUES CRÉÉES AVEC SUCCÈS !\n";
