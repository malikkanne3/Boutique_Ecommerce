@extends('layouts.app')
@section('title', 'Boutique')

@push('styles')
<style>
.filter-bar { background: #fff; border-radius: 12px; padding: 1rem 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.product-img { height: 210px; object-fit: cover; border-radius: 12px 12px 0 0; transition: transform .3s; }
.card:hover .product-img { transform: scale(1.03); }
.card { overflow: hidden; }
.category-pill { border-radius: 20px; font-size: .82rem; padding: 5px 14px; transition: all .2s; }
.category-pill.active { background: #6366f1; color: #fff; border-color: #6366f1; }
.badge-stock { font-size: .72rem; }
</style>
@endpush

@section('content')
<div class="container">

    {{-- ENTÊTE --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-0">Notre boutique</h1>
            <span class="text-muted small">{{ $products->total() }} produit(s) trouvé(s)</span>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="filter-bar mb-4">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Rechercher un produit..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-5">
                <select name="category" class="form-select">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <button class="btn btn-primary w-100"><i class="bi bi-funnel"></i></button>
            </div>
            <div class="col-6 col-md-1">
                <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary w-100" title="Réinitialiser">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- GRILLE PRODUITS --}}
    @if($products->count())
        <div class="row g-4">
            @foreach($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div style="overflow:hidden">
                        <img src="{{ $product->image_url }}"
                             class="product-img w-100"
                             alt="{{ $product->name }}">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="text-muted small mb-1">{{ $product->category->name }}</div>
                        <h6 class="card-title fw-bold mb-1">{{ $product->name }}</h6>

                        {{-- Stock --}}
                        @if($product->stock <= 3 && $product->stock > 0)
                            <span class="badge bg-warning text-dark badge-stock mb-2">Plus que {{ $product->stock }} en stock</span>
                        @elseif($product->stock == 0)
                            <span class="badge bg-danger badge-stock mb-2">Rupture de stock</span>
                        @endif

                        <div class="fw-bold text-primary fs-6 mt-auto mb-3">{{ $product->formatted_price }}</div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('shop.show', $product->slug) }}"
                               class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="bi bi-eye"></i> Voir
                            </a>
                            @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <button class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-cart-plus"></i> Ajouter
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-sm flex-fill" disabled>Indisponible</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div class="mt-5 d-flex justify-content-center">
            {{ $products->withQueryString()->links() }}
        </div>

    @else
        <div class="text-center py-5">
            <i class="bi bi-search" style="font-size:3rem;color:#cbd5e1"></i>
            <p class="mt-3 text-muted">Aucun produit trouvé.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">Voir tous les produits</a>
        </div>
    @endif

</div>
@endsection
