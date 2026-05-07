@extends('layouts.app')
@section('title', $product->name)

@push('styles')
<style>
.product-detail-img { width: 100%; height: 420px; object-fit: cover; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,.12); }
.price-box { background: linear-gradient(135deg, var(--primary-light), #f0fdf4); border-radius: 14px; padding: 1.25rem; margin: 1rem 0; }
.price-main { font-size: 2.2rem; font-weight: 900; color: var(--primary); }
.guarantee-item { display: flex; align-items: center; gap: 10px; padding: .6rem 0; border-bottom: 1px solid var(--border); }
.guarantee-item:last-child { border: none; }
.guarantee-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size:.85rem">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Boutique</a></li>
            @if($product->category)
            <li class="breadcrumb-item"><a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ Str::limit($product->name, 30) }}</li>
        </ol>
    </nav>

    <div class="row g-5 mb-5">
        <div class="col-md-5 fade-up">
            <img src="{{ $product->image_url }}" class="product-detail-img" alt="{{ $product->name }}">
        </div>

        <div class="col-md-7 fade-up fade-up-1">
            @if($product->category)
            <div class="product-category mb-2">{{ $product->category->name }}</div>
            @endif
            <h1 style="font-size:1.9rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:.5rem">{{ $product->name }}</h1>

            @if($product->stock > 5)
                <span class="badge mb-3 px-3 py-2" style="background:#d1fae5;color:#065f46;font-size:.82rem;border-radius:20px">
                    <i class="bi bi-check-circle-fill me-1"></i>En stock ({{ $product->stock }} disponibles)
                </span>
            @elseif($product->stock > 0)
                <span class="badge mb-3 px-3 py-2" style="background:#fef3c7;color:#92400e;font-size:.82rem;border-radius:20px">
                    <i class="bi bi-exclamation-circle-fill me-1"></i>Stock limité ({{ $product->stock }} restants)
                </span>
            @else
                <span class="badge mb-3 px-3 py-2" style="background:#fee2e2;color:#991b1b;font-size:.82rem;border-radius:20px">
                    <i class="bi bi-x-circle-fill me-1"></i>Rupture de stock
                </span>
            @endif

            <div class="price-box">
                <div class="price-main">{{ $product->formatted_price }}</div>
                <div style="font-size:.82rem;color:var(--gray);margin-top:.25rem">Prix TTC — Livraison calculée au panier</div>
            </div>

            @if($product->description)
            <div class="mb-4">
                <h6 style="font-weight:700;margin-bottom:.6rem">À propos de ce produit</h6>
                <p style="color:var(--gray);line-height:1.8;font-size:.95rem">{{ $product->description }}</p>
            </div>
            @endif

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

            <div class="guarantee-item">
                <div class="guarantee-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-shield-check"></i></div>
                <div><div style="font-weight:600;font-size:.9rem">Paiement sécurisé</div><div style="font-size:.78rem;color:var(--gray)">Wave, Orange Money, Carte bancaire</div></div>
            </div>
            <div class="guarantee-item">
                <div class="guarantee-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-truck"></i></div>
                <div><div style="font-weight:600;font-size:.9rem">Livraison rapide</div><div style="font-size:.78rem;color:var(--gray)">48h dans Dakar, 5 jours en région</div></div>
            </div>
            <div class="guarantee-item">
                <div class="guarantee-icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-arrow-counterclockwise"></i></div>
                <div><div style="font-weight:600;font-size:.9rem">Retour sous 7 jours</div><div style="font-size:.78rem;color:var(--gray)">Remboursement complet garanti</div></div>
            </div>
        </div>
    </div>

    @if(isset($related) && $related->count())
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
