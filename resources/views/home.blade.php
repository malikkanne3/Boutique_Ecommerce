@extends('layouts.app')
@section('title', 'Accueil')

@section('content')
<div class="container" style="max-width:1240px">

    {{-- HERO --}}
    <section class="hero rise">
        <div class="row align-items-center position-relative" style="z-index:1">
            <div class="col-lg-7">
                <span class="badge rounded-pill text-bg-light mb-3 px-3 py-2"><i class="bi bi-stars me-1 text-accent"></i>La marketplace made in Sénégal</span>
                <h1 class="mb-3">Achetez malin,<br>faites-vous livrer partout.</h1>
                <p class="mb-4">Des centaines de produits proposés par des vendeurs locaux, livrés chez vous par nos livreurs partenaires.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('shop.index') }}" class="btn btn-light btn-lg px-4"><i class="bi bi-grid me-2"></i>Découvrir la boutique</a>
                    @guest<a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">Devenir vendeur</a>@endguest
                </div>
            </div>
        </div>
    </section>

    <div class="trust rise">
        <div><i class="bi bi-truck"></i><span><b>Livraison rapide</b><small>Suivi en temps réel</small></span></div>
        <div><i class="bi bi-shield-check"></i><span><b>Vendeurs vérifiés</b><small>Achat en confiance</small></span></div>
        <div><i class="bi bi-chat-heart"></i><span><b>Messagerie intégrée</b><small>Parlez au vendeur</small></span></div>
        <div><i class="bi bi-arrow-repeat"></i><span><b>Annulation facile</b><small>Tant que non confirmée</small></span></div>
    </div>

    {{-- CATÉGORIES --}}
    <div class="section-head"><h2>Parcourir par catégorie</h2><a href="{{ route('shop.index') }}" class="fw-bold small">Tout voir <i class="bi bi-arrow-right"></i></a></div>
    <div class="cat-scroll">
        @foreach($categories as $cat)
            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="cat-chip">{{ $cat->name }} <small>{{ $cat->products_count }}</small></a>
        @endforeach
    </div>

    {{-- NOUVEAUTÉS --}}
    <div class="section-head"><h2>Les dernières nouveautés</h2><a href="{{ route('shop.index') }}" class="fw-bold small">Voir plus <i class="bi bi-arrow-right"></i></a></div>
    <div class="row g-3">
        @forelse($featured as $product)
            <div class="col-6 col-md-4 col-xl-3 rise"><x-product-card :product="$product" /></div>
        @empty
            <div class="col-12"><x-empty-state icon="box-seam" title="Aucun produit pour le moment" text="Revenez bientôt, de nouveaux articles arrivent !" /></div>
        @endforelse
    </div>

    @if($recent->count())
        <div class="section-head"><h2>Reprenez où vous en étiez</h2></div>
        <div class="row g-3">@foreach($recent as $product)<div class="col-6 col-md-4 col-lg-2"><x-product-card :product="$product" /></div>@endforeach</div>
    @endif

    {{-- BOUTIQUES --}}
    @if($shops->count())
        <div class="section-head"><h2>Nos vendeurs</h2></div>
        <div class="row g-3">
            @foreach($shops as $shop)
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('shops.show', $shop) }}" class="shop-card">
                        <div class="d-flex justify-content-center mb-2"><x-avatar :user="$shop" size="lg" /></div>
                        <div class="fw-bold small text-truncate">{{ $shop->display_name }}</div>
                        <small class="text-muted">{{ $shop->products_count }} produit(s)</small>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if($newProducts->count())
        <div class="section-head"><h2>À ne pas manquer</h2></div>
        <div class="row g-3">
            @foreach($newProducts as $product)
                <div class="col-6 col-md-4 col-xl-3"><x-product-card :product="$product" /></div>
            @endforeach
        </div>
    @endif

    @guest
    <section class="card-ui p-4 p-md-5 mt-5 text-center">
        <h3 class="fw-bold">Vous avez des produits à vendre ?</h3>
        <p class="text-muted mb-4">Ouvrez votre boutique gratuitement, gérez vos stocks, vos commandes et vos livraisons depuis un seul tableau de bord.</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">Ouvrir ma boutique</a>
    </section>
    @endguest
</div>
@endsection
