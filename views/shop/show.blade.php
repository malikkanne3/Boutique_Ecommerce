@extends('layouts.app')
@section('title', $product->name)

@push('styles')
<style>
.product-main-img {
    width: 100%; max-height: 420px; object-fit: cover;
    border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.1);
}
.price-tag { font-size: 2rem; font-weight: 800; color: #6366f1; }
.stock-badge { font-size: .85rem; padding: 6px 14px; border-radius: 20px; }
.qty-btn { width: 38px; height: 38px; padding: 0; font-size: 1.1rem; }
</style>
@endpush

@section('content')
<div class="container">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Boutique</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">
                    {{ $product->category->name }}
                </a>
            </li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">

        {{-- IMAGE --}}
        <div class="col-md-5">
            <img src="{{ $product->image_url }}" class="product-main-img" alt="{{ $product->name }}">
        </div>

        {{-- INFOS --}}
        <div class="col-md-7">
            <div class="text-muted small mb-1">{{ $product->category->name }}</div>
            <h1 class="fw-bold mb-3" style="font-size:1.8rem">{{ $product->name }}</h1>

            <div class="price-tag mb-3">{{ $product->formatted_price }}</div>

            {{-- STOCK --}}
            @if($product->stock > 5)
                <span class="badge bg-success stock-badge mb-3">
                    <i class="bi bi-check-circle me-1"></i>En stock ({{ $product->stock }} disponibles)
                </span>
            @elseif($product->stock > 0)
                <span class="badge bg-warning text-dark stock-badge mb-3">
                    <i class="bi bi-exclamation-circle me-1"></i>Stock limité ({{ $product->stock }} restants)
                </span>
            @else
                <span class="badge bg-danger stock-badge mb-3">
                    <i class="bi bi-x-circle me-1"></i>Rupture de stock
                </span>
            @endif

            {{-- DESCRIPTION --}}
            @if($product->description)
            <div class="mb-4">
                <h6 class="fw-semibold mb-2">Description</h6>
                <p class="text-muted" style="line-height:1.7">{{ $product->description }}</p>
            </div>
            @endif

            {{-- AJOUTER AU PANIER --}}
            @if($product->stock > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-cart-plus me-2"></i>Ajouter au panier
                    </button>
                </form>
            @else
                <button class="btn btn-secondary btn-lg px-5" disabled>Indisponible</button>
            @endif

            {{-- GARANTIES --}}
            <div class="row g-3 mt-4">
                <div class="col-4 text-center">
                    <i class="bi bi-shield-check text-success fs-4"></i>
                    <div class="small text-muted mt-1">Paiement sécurisé</div>
                </div>
                <div class="col-4 text-center">
                    <i class="bi bi-truck text-primary fs-4"></i>
                    <div class="small text-muted mt-1">Livraison rapide</div>
                </div>
                <div class="col-4 text-center">
                    <i class="bi bi-arrow-repeat text-warning fs-4"></i>
                    <div class="small text-muted mt-1">Retour facile</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
