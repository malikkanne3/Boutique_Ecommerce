@extends('layouts.app')
@section('title', 'Boutique')

@push('styles')
<style>
.shop-header { background: linear-gradient(135deg, #0f172a, #1e1b4b); color: #fff; padding: 3rem 0; margin-bottom: 2rem; }
.shop-header h1 { font-size: 2rem; font-weight: 900; letter-spacing: -1px; }
.filter-card { background: #fff; border-radius: 16px; padding: 1.25rem; border: 1px solid var(--border); position: sticky; top: 80px; }
.filter-title { font-weight: 700; font-size: .78rem; text-transform: uppercase; letter-spacing: .7px; color: var(--gray); margin-bottom: .75rem; }
.cat-filter-item { display: flex; align-items: center; justify-content: space-between; padding: .45rem .75rem; border-radius: 8px; cursor: pointer; transition: all .15s; font-size: .88rem; text-decoration: none; color: var(--dark); margin-bottom: 2px; }
.cat-filter-item:hover { background: var(--primary-light); color: var(--primary); }
.cat-filter-item.active { background: var(--primary); color: #fff; font-weight: 600; }
.count-pill { font-size: .68rem; padding: 1px 7px; border-radius: 20px; background: rgba(0,0,0,.07); }
.cat-filter-item.active .count-pill { background: rgba(255,255,255,.25); }
</style>
@endpush

@section('content')
<div class="shop-header">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:.82rem">
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

        {{-- SIDEBAR --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="filter-card">
                <div class="filter-title">Recherche</div>
                <form method="GET" class="mb-4">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-end-0"
                               placeholder="Rechercher..." value="{{ request('search') }}"
                               style="border-radius:10px 0 0 10px">
                        <button class="btn btn-primary" style="border-radius:0 10px 10px 0"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <div class="filter-title">Catégories</div>
                <a href="{{ route('shop.index', ['search' => request('search'), 'sort' => request('sort')]) }}"
                   class="cat-filter-item {{ !request('category') ? 'active' : '' }}">
                    <span>Toutes les catégories</span>
                    <span class="count-pill">{{ $allCount }}</span>
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('shop.index', ['category' => $cat->slug, 'search' => request('search'), 'sort' => request('sort')]) }}"
                   class="cat-filter-item {{ request('category') == $cat->slug ? 'active' : '' }}">
                    <span>{{ $cat->name }}</span>
                    <span class="count-pill">{{ $cat->products_count }}</span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- PRODUITS --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    @if(request('search') || request('category'))
                        <span class="badge" style="background:var(--primary-light);color:var(--primary);border-radius:8px;padding:6px 12px;font-size:.82rem">
                            {{ $products->total() }} résultat(s)
                            @if(request('search')) pour "{{ request('search') }}" @endif
                        </span>
                        <a href="{{ route('shop.index') }}" class="btn btn-sm btn-outline-secondary ms-2"><i class="bi bi-x"></i> Réinitialiser</a>
                    @else
                        <span style="font-weight:700;color:var(--dark)">{{ $products->total() }} produits</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#filterCanvas">
                        <i class="bi bi-funnel me-1"></i>Filtres
                    </button>
                    <form method="GET">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="sort" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort','latest') == 'latest' ? 'selected' : '' }}>Plus récents</option>
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
                                <div style="position:absolute;inset:0;background:rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;">
                                    <span style="background:#fff;color:#991b1b;font-weight:700;font-size:.78rem;padding:4px 12px;border-radius:20px;">Rupture</span>
                                </div>
                            @elseif($product->stock <= 3)
                                <span class="position-absolute top-0 end-0 m-2 badge" style="background:#fef3c7;color:#92400e;font-size:.68rem;border-radius:8px;">{{ $product->stock }} restant(s)</span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <div class="product-category">{{ $product->category->name ?? '' }}</div>
                            <div class="product-name">{{ Str::limit($product->name, 40) }}</div>
                            <div class="product-price mt-auto">{{ $product->formatted_price }}</div>
                            <div class="product-actions">
                                <a href="{{ route('shop.show', $product->slug) }}" class="btn-view"><i class="bi bi-eye"></i></a>
                                @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" style="flex:1">
                                    @csrf
                                    <button class="btn-cart w-100"><i class="bi bi-cart-plus me-1"></i>Ajouter</button>
                                </form>
                                @else
                                <button class="btn-cart w-100" disabled style="flex:1">Indisponible</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-5 d-flex justify-content-center">{{ $products->withQueryString()->links() }}</div>
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

{{-- OFFCANVAS MOBILE --}}
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
