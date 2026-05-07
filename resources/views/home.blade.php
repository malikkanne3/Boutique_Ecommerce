@extends('layouts.app')
@section('title', 'Accueil')

@push('styles')
<style>
.hero-section {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #4c1d95 100%);
    color: #fff; padding: 6rem 0 5rem; position: relative; overflow: hidden;
}
.hero-section::before {
    content: ''; position: absolute; inset: 0;
    background: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1400&q=80') center/cover;
    opacity: .07;
}
.hero-section .container { position: relative; }
.hero-badge { background: rgba(167,139,250,.2); border: 1px solid rgba(167,139,250,.4); color: #c4b5fd; padding: 5px 16px; border-radius: 20px; font-size: .82rem; font-weight: 600; display: inline-block; margin-bottom: 1.2rem; }
.hero-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 1.2rem; }
.gradient-text { background: linear-gradient(135deg, #c4b5fd, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.hero-subtitle { font-size: 1.1rem; color: #94a3b8; max-width: 520px; margin-bottom: 2rem; line-height: 1.7; }
.hero-stats { display: flex; gap: 2.5rem; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,.1); }
.hero-stat .number { font-size: 1.8rem; font-weight: 900; color: #fff; }
.hero-stat .label { font-size: .78rem; color: #94a3b8; }

.features-strip { background: #fff; border-radius: 16px; padding: 1.5rem 2rem; box-shadow: 0 4px 20px rgba(0,0,0,.06); margin-top: -2rem; position: relative; z-index: 10; }
.feature-item { display: flex; align-items: center; gap: 12px; }
.feature-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }

.cat-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 1.5rem 1rem; text-align: center; cursor: pointer; transition: all .25s; text-decoration: none; color: inherit; display: block; }
.cat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(124,58,237,.12); border-color: var(--primary); color: var(--primary); }
.cat-card .cat-icon { font-size: 2.2rem; margin-bottom: .75rem; display: block; }
.cat-card .cat-name { font-weight: 700; font-size: .95rem; }
.cat-card .cat-count { font-size: .78rem; color: var(--gray); }

.promo-banner { background: linear-gradient(135deg, #7c3aed 0%, #1d4ed8 100%); border-radius: 20px; padding: 2.5rem; color: #fff; position: relative; overflow: hidden; }
.promo-banner::before { content: ''; position: absolute; right: -50px; top: -50px; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,.06); }
.promo-banner::after { content: ''; position: absolute; right: 50px; bottom: -80px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,.04); }
.promo-banner .content { position: relative; z-index: 1; }
</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-up">
                <div class="hero-badge"><i class="bi bi-lightning-charge-fill me-1"></i>Livraison express disponible</div>
                <h1 class="hero-title">
                    Shopping en ligne <br>
                    <span class="gradient-text">simple & rapide</span>
                </h1>
                <p class="hero-subtitle">
                    Des milliers de produits de qualité livrés directement chez vous au Sénégal. Électronique, mode, alimentation et bien plus.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('shop.index') }}" class="btn btn-accent btn-lg px-5">
                        <i class="bi bi-grid me-2"></i>Explorer la boutique
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-lg px-4" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:10px;">
                        <i class="bi bi-person-plus me-2"></i>Créer un compte
                    </a>
                    @endguest
                </div>
                <div class="hero-stats">
                    <div class="hero-stat"><div class="number">2K+</div><div class="label">Produits</div></div>
                    <div class="hero-stat"><div class="number">500+</div><div class="label">Clients</div></div>
                    <div class="hero-stat"><div class="number">48h</div><div class="label">Livraison</div></div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-flex justify-content-end fade-up fade-up-2">
                <div style="position:relative">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=600&q=80"
                         style="width:420px;height:420px;object-fit:cover;border-radius:24px;box-shadow:0 30px 60px rgba(0,0,0,.4);" alt="Shopping">
                    <div style="position:absolute;bottom:-20px;left:-20px;background:#fff;border-radius:14px;padding:12px 18px;box-shadow:0 8px 25px rgba(0,0,0,.15);">
                        <div style="font-size:.75rem;color:var(--gray);font-weight:600;">Commande livrée 🎉</div>
                        <div style="font-weight:800;color:var(--dark);font-size:.9rem;">Dakar, Plateau</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">

{{-- FEATURES --}}
<div class="features-strip mb-5 fade-up fade-up-1">
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div class="feature-item">
                <div class="feature-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-truck"></i></div>
                <div><div style="font-weight:700;font-size:.9rem">Livraison rapide</div><div style="font-size:.78rem;color:var(--gray)">48h dans Dakar</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-item">
                <div class="feature-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-shield-check"></i></div>
                <div><div style="font-weight:700;font-size:.9rem">Paiement sécurisé</div><div style="font-size:.78rem;color:var(--gray)">Wave, Orange Money</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-item">
                <div class="feature-icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-arrow-counterclockwise"></i></div>
                <div><div style="font-weight:700;font-size:.9rem">Retour facile</div><div style="font-size:.78rem;color:var(--gray)">7 jours pour changer</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-item">
                <div class="feature-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-headset"></i></div>
                <div><div style="font-weight:700;font-size:.9rem">Support 7j/7</div><div style="font-size:.78rem;color:var(--gray)">8h – 20h</div></div>
            </div>
        </div>
    </div>
</div>

{{-- CATÉGORIES --}}
@if($categories->count())
<section class="mb-5 fade-up fade-up-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">Nos catégories</h2>
            <p class="section-subtitle mb-0">Trouvez ce que vous cherchez rapidement</p>
        </div>
    </div>
    @php $catIcons = ['📱','👕','🥗','🏠','💄','⚽','📚','🎮','🛒','🎵','💻','🎨']; @endphp
    <div class="row g-3">
        @foreach($categories as $i => $cat)
        <div class="col-4 col-md-3 col-lg-2">
            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="cat-card">
                <span class="cat-icon">{{ $catIcons[$i % count($catIcons)] }}</span>
                <div class="cat-name">{{ $cat->name }}</div>
                <div class="cat-count">{{ $cat->products_count }} produits</div>
            </a>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- PRODUITS VEDETTES --}}
@if($featured->count())
<section class="mb-5 fade-up fade-up-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">Produits vedettes</h2>
            <p class="section-subtitle mb-0">Sélection de nos meilleures offres</p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Voir tout <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
        @foreach($featured as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card border-0 shadow-sm h-100">
                <div class="product-img-wrap">
                    <img src="{{ $product->image_url }}" class="product-img" alt="{{ $product->name }}">
                    @if($product->stock <= 3 && $product->stock > 0)
                        <span class="position-absolute top-0 end-0 m-2 badge" style="background:#fef3c7;color:#92400e;font-size:.7rem;border-radius:8px;">Stock limité</span>
                    @endif
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <div class="product-category">{{ $product->category->name ?? '' }}</div>
                    <div class="product-name">{{ $product->name }}</div>
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
</section>
@endif

{{-- PROMO BANNER --}}
<section class="mb-5 fade-up">
    <div class="promo-banner">
        <div class="content row align-items-center">
            <div class="col-md-8">
                <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;opacity:.7;margin-bottom:.5rem;">Offre spéciale</div>
                <h3 style="font-weight:900;font-size:1.7rem;margin-bottom:.75rem">Livraison GRATUITE dès 25 000 FCFA !</h3>
                <p style="opacity:.85;margin-bottom:1.5rem">Valable dans toute la ville de Dakar. Profitez-en maintenant !</p>
                <a href="{{ route('shop.index') }}" class="btn btn-lg" style="background:#fff;color:#7c3aed;font-weight:700;border-radius:10px;padding:.65rem 2rem;">
                    En profiter <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-md-4 d-none d-md-flex justify-content-end" style="font-size:6rem;opacity:.3">🎁</div>
        </div>
    </div>
</section>

{{-- NOUVEAUX PRODUITS --}}
@if($newProducts->count())
<section class="mb-5 fade-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">Nouveautés</h2>
            <p class="section-subtitle mb-0">Les derniers arrivages</p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Tout voir <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
        @foreach($newProducts as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card border-0 shadow-sm h-100">
                <div class="product-img-wrap">
                    <img src="{{ $product->image_url }}" class="product-img" alt="{{ $product->name }}">
                    <span class="position-absolute top-0 start-0 m-2 badge" style="background:var(--primary);color:#fff;border-radius:8px;font-size:.7rem;">Nouveau</span>
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <div class="product-category">{{ $product->category->name ?? '' }}</div>
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-price mt-auto">{{ $product->formatted_price }}</div>
                    <div class="product-actions">
                        <a href="{{ route('shop.show', $product->slug) }}" class="btn-view"><i class="bi bi-eye"></i></a>
                        @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" style="flex:1">
                            @csrf
                            <button class="btn-cart w-100"><i class="bi bi-cart-plus me-1"></i>Ajouter</button>
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
