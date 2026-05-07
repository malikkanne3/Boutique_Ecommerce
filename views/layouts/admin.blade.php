<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar: #1e1b4b; --sidebar-hover: #312e81; --accent: #6366f1; }
        body { background: #f1f5f9; }
        .sidebar {
            width: 240px; min-height: 100vh; background: var(--sidebar);
            position: fixed; top: 0; left: 0; z-index: 100; display: flex; flex-direction: column;
        }
        .sidebar-brand { padding: 1.5rem 1.25rem; color: #fff; font-size: 1.3rem; font-weight: 700; border-bottom: 1px solid #312e81; }
        .sidebar-brand i { color: var(--accent); }
        .sidebar .nav-link { color: #a5b4fc; padding: .65rem 1.25rem; border-radius: 8px; margin: 2px 8px; font-size: .9rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: var(--sidebar-hover); color: #fff; }
        .sidebar .nav-link i { width: 20px; }
        .main-content { margin-left: 240px; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: .75rem 1.5rem; }
        .page-content { padding: 1.5rem; }
        .stat-card { border-radius: 12px; border: none; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .table th { font-size: .8rem; text-transform: uppercase; color: #64748b; letter-spacing: .5px; }
        .badge-status { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: .78rem; font-weight: 600; }
        .card { border-radius: 12px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-240px); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="sidebar-brand"><i class="bi bi-bag-heart-fill me-2"></i>E-Shop Admin</div>
    <nav class="nav flex-column mt-3 flex-grow-1">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
           href="{{ route('admin.products.index') }}">
            <i class="bi bi-box-seam me-2"></i> Produits
        </a>
        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
           href="{{ route('admin.categories.index') }}">
            <i class="bi bi-tag me-2"></i> Catégories
        </a>
        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
           href="{{ route('admin.orders.index') }}">
            <i class="bi bi-receipt me-2"></i> Commandes
        </a>
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
           href="{{ route('admin.users.index') }}">
            <i class="bi bi-people me-2"></i> Clients
        </a>
    </nav>
    <div class="p-3 border-top" style="border-color:#312e81!important;">
        <div class="text-white-50 small mb-1">{{ auth()->user()->name }}</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right me-1"></i>Déconnexion</button>
        </form>
    </div>
</div>

{{-- MAIN --}}
<div class="main-content">
    <div class="topbar d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark">@yield('title', 'Dashboard')</h6>
        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Voir la boutique
        </a>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
