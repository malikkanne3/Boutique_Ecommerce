<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Livreur — E-Shop SN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #ede9fe;
            --dark: #0f172a;
            --border: #e2e8f0;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; }

        /* NAVBAR */
        .topnav { background: var(--dark); padding: .875rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .topnav .brand { color: #fff; font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; }
        .topnav .brand-icon { background: linear-gradient(135deg, #7c3aed, #a855f7); width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .topnav .user-info { color: #94a3b8; font-size: .85rem; display: flex; align-items: center; gap: 12px; }
        .topnav .user-info strong { color: #e2e8f0; }

        /* STAT CARDS */
        .stat-card { border-radius: 16px; border: none; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--dark); line-height: 1; }
        .stat-label { font-size: .8rem; color: #64748b; font-weight: 500; margin-top: 2px; }

        /* TABS */
        .nav-tabs { border: none; gap: 8px; }
        .nav-tabs .nav-link { border: 1px solid var(--border); border-radius: 10px !important; color: #64748b; font-weight: 600; font-size: .85rem; padding: .5rem 1rem; background: #fff; }
        .nav-tabs .nav-link.active { background: var(--primary); border-color: var(--primary); color: #fff; }

        /* COMMANDE CARD */
        .commande-card { border-radius: 16px; border: 1px solid var(--border); background: #fff; margin-bottom: 1rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.04); transition: box-shadow .2s; }
        .commande-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); }
        .commande-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
        .commande-body { padding: 1.25rem; }
        .commande-footer { padding: .875rem 1.25rem; border-top: 1px solid var(--border); background: #fafafa; display: flex; gap: .75rem; flex-wrap: wrap; }

        /* BADGES */
        .badge-delivering { background: #dbeafe; color: #1e40af; padding: 5px 12px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
        .badge-confirmed  { background: #fef3c7; color: #92400e; padding: 5px 12px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
        .badge-delivered  { background: #d1fae5; color: #065f46; padding: 5px 12px; border-radius: 20px; font-size: .75rem; font-weight: 600; }

        /* INFO ITEMS */
        .info-item { display: flex; align-items: center; gap: 8px; font-size: .85rem; color: #475569; margin-bottom: .4rem; }
        .info-item i { width: 18px; color: var(--primary); }

        /* ARTICLES */
        .article-item { display: flex; align-items: center; gap: 10px; padding: .5rem 0; border-bottom: 1px dashed var(--border); }
        .article-item:last-child { border: none; }
        .article-qty { background: var(--primary-light); color: var(--primary); font-weight: 700; font-size: .75rem; padding: 2px 8px; border-radius: 20px; }

        /* EMPTY */
        .empty-state { text-align: center; padding: 3rem; color: #94a3b8; }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; opacity: .4; }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<div class="topnav">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-bag-heart-fill text-white"></i></div>
        E-ShopSN — Espace Livreur
    </div>
    <div class="user-info">
        <span>Bonjour, <strong>{{ auth()->user()->name }}</strong></span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-outline-light" style="border-radius:8px;font-size:.8rem">
                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </button>
        </form>
    </div>
</div>

<div class="container py-4">

    {{-- ALERTES --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" style="border-radius:12px;border:none">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0">👋 Bonjour, {{ auth()->user()->name }}</h4>
            <p class="text-muted mb-0" style="font-size:.85rem">{{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- STATS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card bg-white">
                <div class="stat-icon" style="background:#ede9fe">
                    <i class="bi bi-box-seam" style="color:#7c3aed"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card bg-white">
                <div class="stat-icon" style="background:#fef3c7">
                    <i class="bi bi-hourglass-split" style="color:#d97706"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['en_attente'] }}</div>
                    <div class="stat-label">À accepter</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card bg-white">
                <div class="stat-icon" style="background:#dbeafe">
                    <i class="bi bi-truck" style="color:#2563eb"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['en_cours'] }}</div>
                    <div class="stat-label">En cours</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card bg-white">
                <div class="stat-icon" style="background:#d1fae5">
                    <i class="bi bi-check-circle" style="color:#059669"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['livrees'] }}</div>
                    <div class="stat-label">Livrées</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs mb-4" id="livraisonTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-all">
                <i class="bi bi-grid me-1"></i>Toutes <span class="badge bg-secondary ms-1" style="font-size:.65rem">{{ $stats['total'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-attente">
                <i class="bi bi-hourglass me-1"></i>À accepter
                @if($stats['en_attente'] > 0)
                <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem">{{ $stats['en_attente'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-cours">
                <i class="bi bi-truck me-1"></i>En cours
                @if($stats['en_cours'] > 0)
                <span class="badge bg-primary ms-1" style="font-size:.65rem">{{ $stats['en_cours'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-livrees">
                <i class="bi bi-check-circle me-1"></i>Livrées
            </a>
        </li>
    </ul>

    {{-- CONTENU TABS --}}
    <div class="tab-content">

        {{-- TOUTES --}}
        <div class="tab-pane fade show active" id="tab-all">
            @forelse($commandes as $cmd)
                @include('livreur.partials.commande-card', ['cmd' => $cmd])
            @empty
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p class="fw-semibold">Aucune commande assignée</p>
                </div>
            @endforelse
        </div>

        {{-- À ACCEPTER --}}
        <div class="tab-pane fade" id="tab-attente">
            @forelse($commandes->where('status', 'confirmed') as $cmd)
                @include('livreur.partials.commande-card', ['cmd' => $cmd])
            @empty
                <div class="empty-state">
                    <i class="bi bi-check2-circle"></i>
                    <p class="fw-semibold">Aucune commande en attente</p>
                </div>
            @endforelse
        </div>

        {{-- EN COURS --}}
        <div class="tab-pane fade" id="tab-cours">
            @forelse($commandes->where('status', 'delivering') as $cmd)
                @include('livreur.partials.commande-card', ['cmd' => $cmd])
            @empty
                <div class="empty-state">
                    <i class="bi bi-truck"></i>
                    <p class="fw-semibold">Aucune livraison en cours</p>
                </div>
            @endforelse
        </div>

        {{-- LIVRÉES --}}
        <div class="tab-pane fade" id="tab-livrees">
            @forelse($commandes->where('status', 'delivered') as $cmd)
                @include('livreur.partials.commande-card', ['cmd' => $cmd])
            @empty
                <div class="empty-state">
                    <i class="bi bi-bag-check"></i>
                    <p class="fw-semibold">Aucune commande livrée</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>