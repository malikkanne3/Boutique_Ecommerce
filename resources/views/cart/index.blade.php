@extends('layouts.app')
@section('title', 'Mon panier')

@push('styles')
<style>
.cart-item-img { width: 85px; height: 85px; object-fit: cover; border-radius: 12px; flex-shrink: 0; }
.qty-btn { width: 30px; height: 30px; border-radius: 8px; border: 1px solid var(--border); background: #fff; font-weight: 700; cursor: pointer; transition: all .15s; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
.qty-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.summary-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 1.5rem; position: sticky; top: 85px; }
</style>
@endpush

@section('content')
<div class="container">
    <h1 style="font-size:1.8rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:.3rem">Mon panier</h1>
    <p class="text-muted mb-4">{{ count($cart) }} article(s)</p>

    @if(count($cart) > 0)
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @foreach($cart as $productId => $item)
                    <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <img src="{{ $item['image'] ? (filter_var($item['image'], FILTER_VALIDATE_URL) ? $item['image'] : asset('storage/'.$item['image'])) : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=200&q=80' }}"
                             class="cart-item-img" alt="{{ $item['name'] }}">
                        <div class="flex-grow-1 min-w-0">
                            <div style="font-weight:700;font-size:.95rem">{{ $item['name'] }}</div>
                            <div style="color:var(--gray);font-size:.82rem">{{ number_format($item['price'], 0, ',', ' ') }} FCFA / unité</div>
                        </div>
                        <form action="{{ route('cart.update', $productId) }}" method="POST" class="d-flex align-items-center gap-1">
                            @csrf
                            <button type="submit" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}" class="qty-btn">−</button>
                            <span style="width:36px;text-align:center;font-weight:700;font-size:.9rem">{{ $item['quantity'] }}</span>
                            <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="qty-btn">+</button>
                        </form>
                        <div style="font-weight:800;color:var(--primary);font-size:.95rem;min-width:90px;text-align:right" class="d-none d-sm-block">
                            {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA
                        </div>
                        <form action="{{ route('cart.remove', $productId) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger border-0" style="border-radius:8px"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center" style="border-radius:0 0 14px 14px">
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Continuer les achats
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash3 me-1"></i>Vider le panier</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="summary-card">
                <h5 style="font-weight:800;margin-bottom:1.25rem">Résumé</h5>
                @foreach($cart as $item)
                <div class="d-flex justify-content-between text-muted mb-2" style="font-size:.85rem">
                    <span>{{ Str::limit($item['name'], 22) }} × {{ $item['quantity'] }}</span>
                    <span>{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}</span>
                </div>
                @endforeach
                <div class="d-flex justify-content-between text-muted mb-2" style="font-size:.85rem">
                    <span>Livraison</span>
                    <span>{{ $total >= 25000 ? 'Gratuite 🎉' : 'À calculer' }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold" style="font-size:1.15rem">
                    <span>Total</span>
                    <span style="color:var(--primary)">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>

                @if($total < 25000)
                <div style="background:#fef3c7;border-radius:10px;padding:.75rem;margin:1rem 0;font-size:.8rem;color:#92400e">
                    <i class="bi bi-truck me-1"></i>
                    Plus que {{ number_format(25000 - $total, 0, ',', ' ') }} FCFA pour la livraison gratuite !
                </div>
                @endif

                @auth
                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 btn-lg mt-3">
                    <i class="bi bi-credit-card me-2"></i>Commander
                </a>
                @else
                <a href="{{ route('login') }}" class="btn btn-primary w-100 btn-lg mt-3">
                    <i class="bi bi-lock me-2"></i>Se connecter pour commander
                </a>
                <div class="text-center small text-muted mt-2">Pas de compte ? <a href="{{ route('register') }}">Créer un compte</a></div>
                @endauth

                <div class="text-center mt-3">
                    <div style="font-size:.72rem;color:var(--gray);margin-bottom:.5rem">Paiements acceptés</div>
                    <div style="display:flex;justify-content:center;gap:6px;flex-wrap:wrap">
                        @foreach(['Wave', 'Orange Money', 'Visa', 'Cash'] as $pay)
                        <span style="background:#f1f5f9;border-radius:6px;padding:3px 8px;font-size:.7rem;font-weight:600">{{ $pay }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <div style="font-size:5rem;margin-bottom:1.5rem">🛒</div>
        <h4 style="font-weight:800;margin-bottom:.5rem">Votre panier est vide</h4>
        <p class="text-muted mb-4">Découvrez nos produits et ajoutez vos favoris !</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-grid me-2"></i>Découvrir la boutique
        </a>
    </div>
    @endif
</div>
@endsection
