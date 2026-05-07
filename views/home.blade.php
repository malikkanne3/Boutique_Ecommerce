@extends('layouts.app')
@section('title', 'Accueil')

@push('styles')
<style>
.hero {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff;
    padding: 5rem 0;
    border-radius: 0 0 40px 40px;
}
.hero h1 { font-size: 3rem; font-weight: 800; line-height: 1.1; }
.hero p { font-size: 1.2rem; opacity: .9; }
.category-card { border-radius: 14px; cursor: pointer; overflow: hidden; }
.category-card .icon { font-size: 2.5rem; }
.product-card img { height: 220px; object-fit: cover; }
.section-title { font-size: 1.6rem; font-weight: 700; }
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="hero mb-5">
    <div class="container text-center">
        <h1>Bienvenue sur E-Shop 🛍️</h1>
        <p class="mt-3 mb-4">Découvrez nos produits de qualité livrés rapidement.</p>
        <a href="{{ route('shop.index') }}" class="btn btn-light btn-lg px-5 fw-semibold">
            Explorer la boutique <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>
</div>

<div class="container">

    {{-- CATÉGORIES --}}
    @if($categories->count())
    <div class="mb-5">
        <h2 class="section-title mb-4">Catégories</h2>
        <div class="row g-3">
            @foreach($categories as $cat)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="text-decoration-none">
                    <div class="card category-card border-0 shadow-sm text-center p-3 h-100">
                        <div class="icon mb-2">🏷️</div>
                        <div class="fw-semibold small">{{ $cat->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $cat->products_count }} produits</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- PRODUITS VEDETTES --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Produits vedettes</h2>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary btn-sm">Voir tout</a>
        </div>
        <div class="row g-4">
            @foreach($featured as $product)
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <img src="{{ $product->image_url }}"
                         class="card-img-top product-card"
                         alt="{{ $product->name }}"
                         style="height:200px;object-fit:cover;border-radius:12px 12px 0 0;">
                    <div class="card-body d-flex flex-column">
                        <div class="text-muted small mb-1">{{ $product->category->name }}</div>
                        <h6 class="card-title fw-bold">{{ $product->name }}</h6>
                        <div class="fw-bold text-primary mt-auto mb-2">{{ $product->formatted_price }}</div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('shop.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm flex-fill">Voir</a>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-fill">
                                @csrf
                                <button class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
