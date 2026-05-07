@extends('layouts.app')
@section('title', 'Mon panier')

@push('styles')
<style>
.cart-img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; }
.qty-input { width: 70px; text-align: center; }
.summary-card { position: sticky; top: 90px; border-radius: 16px; }
</style>
@endpush

@section('content')
<div class="container">
    <h1 class="fw-bold mb-4">Mon panier</h1>

    @if(count($cart) > 0)
    <div class="row g-4">

        {{-- LISTE --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @foreach($cart as $productId => $item)
                    <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://via.placeholder.com/80' }}"
                             class="cart-img" alt="{{ $item['name'] }}">

                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $item['name'] }}</div>
                            <div class="text-muted small">
                                {{ number_format($item['price'], 2, ',', ' ') }} FCFA / unité
                            </div>
                        </div>

                        {{-- Quantité --}}
                        <form action="{{ route('cart.update', $productId) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                   min="1" max="{{ $item['stock'] }}"
                                   class="form-control form-control-sm qty-input"
                                   onchange="this.form.submit()">
                        </form>

                        {{-- Sous-total --}}
                        <div class="fw-bold text-primary text-nowrap" style="min-width:100px;text-align:right">
                            {{ number_format($item['price'] * $item['quantity'], 2, ',', ' ') }} FCFA
                        </div>

                        {{-- Supprimer --}}
                        <form action="{{ route('cart.remove', $productId) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
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
            <div class="card border-0 shadow-sm summary-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Résumé de la commande</h5>

                    @foreach($cart as $item)
                    <div class="d-flex justify-content-between text-muted small mb-2">
                        <span>{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                        <span>{{ number_format($item['price'] * $item['quantity'], 2, ',', ' ') }} FCFA</span>
                    </div>
                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                        <span>Total</span>
                        <span class="text-primary">{{ number_format($total, 2, ',', ' ') }} FCFA</span>
                    </div>

                    @auth
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-credit-card me-2"></i>Passer la commande
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-lock me-2"></i>Se connecter pour commander
                        </a>
                        <div class="text-center text-muted small mt-2">
                            Pas encore de compte ?
                            <a href="{{ route('register') }}">Créer un compte</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

    </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size:4rem;color:#cbd5e1"></i>
            <p class="mt-3 text-muted fs-5">Votre panier est vide.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary px-5">
                <i class="bi bi-arrow-left me-2"></i>Aller à la boutique
            </a>
        </div>
    @endif
</div>
@endsection
