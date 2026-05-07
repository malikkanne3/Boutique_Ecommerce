<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — @yield('title', 'Dashboard') | E-ShopSN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #7c3aed;
            --accent: #7c3aed;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; margin: 0; }

        /* SIDEBAR */
        .sidebar {
            width: 250px; min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed; top: 0; left: 0; z-index: 100;
            display: flex; flex-direction: column;
            transition: transform .3s;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid #1e293b;
            text-decoration: none;
        }
        .sidebar-brand .brand-icon {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: #fff; width: 36px; height: 36px;
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .sidebar-brand .brand-text { color: #fff; font-weight: 800; font-size: 1.1rem; }
        .sidebar-brand .brand-sub { color: #64748b; font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }

        .sidebar-section { padding: .75rem 1rem .25rem; font-size: .68rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .8px; }

        .sidebar .nav-link {
            color: #94a3b8; padding: .6rem 1rem; border-radius: 10px;
            margin: 2px .75rem; font-size: .875rem; font-weight: 500;
            display: flex; align-items: center; gap: 10px; transition: all .2s;
        }
        .sidebar .nav-link i { width: 18px; text-align: center; font-size: 1rem; }
        .sidebar .nav-link:hover { background: var(--sidebar-hover); color: #e2e8f0; }
        .sidebar .nav-link.active { background: var(--sidebar-active); color: #fff; font-weight: 600; box-shadow: 0 4px 12px rgba(124,58,237,.4); }
        .sidebar .nav-link .badge { margin-left: auto; font-size: .65rem; }

        .sidebar-footer { margin-top: auto; padding: 1rem; border-top: 1px solid #1e293b; }
        .sidebar-user { display: flex; align-items: center; gap: 10px; }
        .sidebar-avatar { width: 34px; height: 34px; background: linear-gradient(135deg, #7c3aed, #a855f7); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .85rem; flex-shrink: 0; }
        .sidebar-user-info .name { color: #e2e8f0; font-size: .85rem; font-weight: 700; }
        .sidebar-user-info .role { color: #64748b; font-size: .72rem; }

        /* MAIN */
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }

        /* TOPBAR */
        .topbar {
            background: #fff; border-bottom: 1px solid var(--border);
            padding: .875rem 1.75rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-weight: 800; font-size: 1.1rem; color: #0f172a; }
        .topbar-breadcrumb { font-size: .8rem; color: #94a3b8; }

        /* PAGE CONTENT */
        .page-content { padding: 1.75rem; flex: 1; }

        /* STAT CARDS */
        .stat-card { border-radius: 14px; border: none; transition: transform .2s, box-shadow .2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,.1) !important; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }

        /* TABLES */
        .table { font-size: .875rem; }
        .table th { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 700; border-bottom: 2px solid var(--border); }
        .table td { vertical-align: middle; border-color: #f1f5f9; }
        .table tbody tr { transition: background .15s; }

        /* BADGE STATUS */
        .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
        .badge-status-pending   { background: #fef3c7; color: #92400e; }
        .badge-status-paid      { background: #d1fae5; color: #065f46; }
        .badge-status-shipped   { background: #dbeafe; color: #1e40af; }
        .badge-status-cancelled { background: #fee2e2; color: #991b1b; }

        /* CARDS */
        .card { border-radius: 14px; border-color: var(--border); }
        .card-header { border-radius: 14px 14px 0 0 !important; padding: 1rem 1.25rem; font-weight: 700; }

        /* FORMS */
        .form-control, .form-select { border-radius: 10px; border-color: var(--border); font-size: .875rem; }
        .form-control:focus, .form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
        .form-label { font-weight: 600; font-size: .82rem; color: #374151; }

        /* BUTTONS */
        .btn { border-radius: 10px; font-weight: 600; font-size: .875rem; }
        .btn-primary { background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; box-shadow: 0 2px 8px rgba(124,58,237,.3); }
        .btn-primary:hover { background: linear-gradient(135deg, #6d28d9, #9333ea); transform: translateY(-1px); }

        /* ALERTS */
        .alert { border-radius: 12px; border: none; font-weight: 500; font-size: .875rem; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-250px); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<div class="sidebar" id="adminSidebar">
    <a href="{{ route('home') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
        <div>
            <div class="brand-text">E-ShopSN</div>
            <div class="brand-sub">Administration</div>
        </div>
    </a>

    <nav class="nav flex-column pt-2 flex-grow-1">
        <div class="sidebar-section">Principal</div>
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="sidebar-section mt-2">Catalogue</div>
        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
            <i class="bi bi-box-seam"></i> Produits
        </a>
        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
            <i class="bi bi-tag"></i> Catégories
        </a>

        <div class="sidebar-section mt-2">Ventes</div>
        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
            <i class="bi bi-receipt"></i> Commandes
        </a>
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
            <i class="bi bi-people"></i> Clients
        </a>

        <div class="sidebar-section mt-2">Accès rapide</div>
        <a class="nav-link" href="{{ route('home') }}" target="_blank">
            <i class="bi bi-arrow-up-right-square"></i> Voir la boutique
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="role">Administrateur</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                @csrf
                <button class="btn btn-sm p-1" style="background:transparent;color:#64748b;border:none" title="Déconnexion">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- MAIN --}}
<div class="main-content">
    <div class="topbar">
        <div>
            <div class="topbar-title">@yield('title', 'Dashboard')</div>
            <div class="topbar-breadcrumb">Administration · @yield('title', 'Dashboard')</div>
        </div>
        <div class="d-flex align-items-center gap-2">
    {{-- CLOCHE ADMIN --}}
    <div class="dropdown">
        <button class="btn position-relative btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            @php $unread = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
            @if($unread > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">{{ $unread }}</span>
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
    <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
        <i class="bi bi-list"></i>
    </button>
</div>    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
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
