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
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f8fafc;
            --border: #e2e8f0;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: var(--dark); }

        /* NAVBAR */
        .navbar { background: #fff; border-bottom: 1px solid var(--border); padding: .75rem 0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 1px 12px rgba(0,0,0,.05); }
        .navbar-brand { font-size: 1.4rem; font-weight: 800; color: var(--primary) !important; letter-spacing: -0.5px; display: flex; align-items: center; gap: 8px; }
        .brand-icon { background: linear-gradient(135deg, var(--primary), #a855f7); color: #fff; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        .nav-link { font-weight: 500; color: var(--gray) !important; transition: color .2s; padding: .4rem .75rem !important; border-radius: 8px; }
        .nav-link:hover, .nav-link.active-link { color: var(--primary) !important; background: var(--primary-light); }
        .nav-link.active-link { font-weight: 600; }

        /* CART BTN */
        .cart-btn { position: relative; background: var(--light-gray); border: 1px solid var(--border); color: var(--dark); border-radius: 12px; padding: .45rem .85rem; font-weight: 600; font-size: .88rem; transition: all .2s; text-decoration: none; display: flex; align-items: center; gap: 6px; }
        .cart-btn:hover { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }
        .cart-badge { background: var(--primary); color: #fff; font-size: .65rem; font-weight: 700; border-radius: 20px; padding: 2px 6px; min-width: 18px; text-align: center; }

        /* BUTTONS */
        .btn-primary { background: linear-gradient(135deg, var(--primary), #a855f7); border: none; font-weight: 600; border-radius: 10px; transition: all .25s; box-shadow: 0 2px 10px rgba(124,58,237,.25); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(124,58,237,.35); background: linear-gradient(135deg, var(--primary-dark), var(--primary)); }
        .btn-outline-primary { border-color: var(--primary); color: var(--primary); font-weight: 600; border-radius: 10px; }
        .btn-outline-primary:hover { background: var(--primary); color: #fff; }
        .btn-accent { background: linear-gradient(135deg, var(--accent), #f97316); border: none; color: #fff; font-weight: 700; border-radius: 10px; box-shadow: 0 2px 10px rgba(245,158,11,.3); transition: all .25s; }
        .btn-accent:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245,158,11,.4); color: #fff; }

        /* CARDS */
        .card { border-radius: 14px; border: 1px solid var(--border); transition: box-shadow .25s, transform .25s; }
        .card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.08); }

        /* PRODUCT CARD */
        .product-card { overflow: hidden; }
        .product-img-wrap { overflow: hidden; position: relative; }
        .product-img { height: 220px; width: 100%; object-fit: cover; transition: transform .4s ease; }
        .product-card:hover .product-img { transform: scale(1.06); }
        .product-category { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: var(--primary); background: var(--primary-light); padding: 2px 8px; border-radius: 20px; display: inline-block; margin-bottom: 6px; }
        .product-name { font-weight: 700; font-size: .95rem; color: var(--dark); margin-bottom: 4px; }
        .product-price { font-size: 1.05rem; font-weight: 800; color: var(--primary); }
        .product-actions { display: flex; gap: 8px; margin-top: 10px; }
        .btn-cart { flex: 1; background: var(--primary); color: #fff; border: none; border-radius: 10px; font-weight: 600; font-size: .85rem; padding: .45rem; transition: all .2s; cursor: pointer; }
        .btn-cart:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-cart:disabled { background: #94a3b8; cursor: not-allowed; transform: none; }
        .btn-view { background: var(--light-gray); color: var(--dark); border: 1px solid var(--border); border-radius: 10px; font-size: .85rem; padding: .45rem .75rem; text-decoration: none; transition: all .2s; white-space: nowrap; display: flex; align-items: center; justify-content: center; }
        .btn-view:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); }

        /* STATUS BADGES */
        .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; border-radius: 20px; font-size: .78rem; font-weight: 600; }
        .badge-status-pending   { background: #fef3c7; color: #92400e; }
        .badge-status-paid      { background: #d1fae5; color: #065f46; }
        .badge-status-shipped   { background: #dbeafe; color: #1e40af; }
        .badge-status-cancelled { background: #fee2e2; color: #991b1b; }

        /* ALERTS */
        .alert { border-radius: 12px; border: none; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }

        /* SECTION TITLES */
        .section-title { font-size: 1.7rem; font-weight: 800; color: var(--dark); letter-spacing: -0.5px; }
        .section-subtitle { color: var(--gray); font-size: .95rem; }

        /* FOOTER */
        footer { background: var(--dark); color: #94a3b8; padding: 3rem 0 1.5rem; margin-top: 5rem; }
        footer h5 { color: #fff; font-weight: 700; }
        footer a { color: #94a3b8; text-decoration: none; transition: color .2s; }
        footer a:hover { color: #fff; }
        footer .footer-brand { font-size: 1.3rem; font-weight: 800; color: #fff; }
        footer .footer-bottom { border-top: 1px solid #1e293b; margin-top: 2rem; padding-top: 1.5rem; }

        /* FORMS */
        .form-control, .form-select { border-radius: 10px; border-color: var(--border); font-size: .9rem; padding: .6rem 1rem; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(124,58,237,.12); }

        /* PAGINATION */
        .pagination .page-link { border-radius: 8px !important; margin: 0 2px; font-weight: 600; color: var(--primary); border-color: var(--border); }
        .pagination .page-item.active .page-link { background: var(--primary); border-color: var(--primary); }

        /* ANIMATIONS */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up   { animation: fadeUp .5s ease both; }
        .fade-up-1 { animation-delay: .1s; }
        .fade-up-2 { animation-delay: .2s; }
        .fade-up-3 { animation-delay: .3s; }

        @media (max-width: 576px) {
            .section-title { font-size: 1.3rem; }
            .product-img { height: 170px; }
        }
    </style>
    @stack('styles')
</head>
<body>

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

{{-- CLOCHE NOTIFICATIONS --}}
@auth
<div class="dropdown">
    <button class="btn position-relative" style="background:var(--light-gray);border:1px solid var(--border);border-radius:12px;padding:.45rem .75rem" data-bs-toggle="dropdown">
        <i class="bi bi-bell fs-5" style="color:var(--dark)"></i>
        @php $unread = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
        @if($unread > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
            {{ $unread }}
        </span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end border-0 shadow p-0" style="width:320px;border-radius:14px;overflow:hidden">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <span class="fw-bold" style="font-size:.9rem">Notifications</span>
            <a href="{{ route('notifications.index') }}" class="text-primary" style="font-size:.78rem">Voir tout</a>
        </div>
        @forelse(auth()->user()->notifications()->where('is_read',false)->latest()->limit(5)->get() as $notif)
        <div class="d-flex align-items-start gap-2 px-3 py-2 border-bottom" style="background:#f8f4ff">
            <i class="bi bi-{{ $notif->icon }} text-{{ $notif->color }} mt-1"></i>
            <div>
                <div class="fw-bold" style="font-size:.8rem">{{ $notif->title }}</div>
                <div class="text-muted" style="font-size:.75rem">{{ Str::limit($notif->body, 50) }}</div>
                <div class="text-muted" style="font-size:.7rem">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-3" style="font-size:.82rem">Aucune nouvelle notification</div>
        @endforelse
        <div class="p-2">
            <a href="{{ route('messages.index') }}" class="btn btn-outline-primary btn-sm w-100">
                <i class="bi bi-chat-dots me-1"></i>Messagerie
            </a>
        </div>
    </div>
</div>
@endauth
                <a href="{{ route('cart.index') }}" class="cart-btn">
                    <i class="bi bi-cart3"></i> Panier
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

<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 fade-up">
            <i class="bi bi-check-circle-fill fs-5"></i><span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 fade-up">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i><span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<main class="py-4">@yield('content')</main>

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
