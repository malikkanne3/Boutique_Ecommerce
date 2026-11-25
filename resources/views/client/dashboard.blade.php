@extends('layouts.dashboard')
@section('title', 'Mon espace')

@section('content')
@php $user = auth()->user(); @endphp

<div class="hero-card mb-4 rise">
    <h2>Bonjour {{ \Illuminate\Support\Str::before($user->name, ' ') }} 👋</h2>
    <p class="mb-3" style="opacity:.92">Retrouvez vos commandes, leur suivi en direct et vos messages.</p>
    <a href="{{ route('shop.index') }}" class="btn btn-light fw-bold"><i class="bi bi-bag-plus me-1"></i>Continuer mes achats</a>
</div>

@include('partials.quick-actions')

<div class="kpis">
    <x-stat-card icon="box-seam" label="Commandes" :value="$stats['orders']" hint="depuis votre inscription" />
    <x-stat-card icon="truck" tone="info" label="En cours" :value="$stats['active']" hint="en préparation ou livraison" />
    <x-stat-card icon="bag-check" tone="ok" label="Livrées" :value="$stats['delivered']" />
    <x-stat-card icon="wallet2" tone="warn" label="Total dépensé" :value="\App\Support\Stats::money($stats['spent'])" />
    <x-stat-card icon="heart" tone="bad" label="Favoris" :value="$stats['favorites']" hint="produits sauvegardés" />
</div>

<div class="row g-4">
    <div class="col-lg-7">
        @if($tracking)
            <div class="ui-card mb-4 rise">
                <div class="ui-card-head"><span><i class="bi bi-geo-alt text-accent me-2"></i>Suivi de ma dernière commande</span>
                    <a href="{{ route('orders.show', $tracking) }}" class="btn btn-sm btn-soft">Détails</a></div>
                <div class="ui-card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                        <span class="mono">{{ $tracking->order_number }}</span><span class="fw-bold">{{ $tracking->formatted_total }}</span>
                    </div>
                    <x-order-steps :order="$tracking" />
                    <div class="small text-muted mt-3"><i class="bi bi-shop me-1"></i>{{ $tracking->seller_name }}
                        @if($tracking->livreur) · <i class="bi bi-truck ms-1 me-1"></i>Livreur : {{ $tracking->livreur->name }}@endif</div>
                </div>
            </div>
        @endif

        <div class="ui-card rise">
            <div class="ui-card-head"><span>Mes dernières commandes</span><a href="{{ route('orders.index') }}" class="small fw-bold">Tout voir</a></div>
            @forelse($recent as $order)
                <a href="{{ route('orders.show', $order) }}" class="d-flex align-items-center gap-3 p-3 border-bottom text-reset">
                    <div class="d-flex">@foreach($order->products->take(2) as $p)<img class="thumb" style="margin-left:-8px;border:2px solid var(--surface)" src="{{ $p->image_url }}" alt="">@endforeach</div>
                    <div class="flex-grow-1"><div class="mono small">{{ $order->order_number }}</div><small class="text-muted">{{ $order->created_at->format('d/m/Y') }} · {{ $order->seller_name }}</small></div>
                    <div class="text-end"><x-status-badge :status="$order->status" /><div class="fw-bold small mt-1">{{ $order->formatted_total }}</div></div>
                </a>
            @empty
                <x-empty-state icon="bag" title="Aucune commande pour l'instant" text="Votre première commande apparaîtra ici."><a href="{{ route('shop.index') }}" class="btn btn-primary">Découvrir la boutique</a></x-empty-state>
            @endforelse
        </div>
    </div>

    <div class="col-lg-5">
        <div class="ui-card rise">
            <div class="ui-card-head"><span>Pour vous</span><a href="{{ route('shop.index') }}" class="small fw-bold">Boutique</a></div>
            <div class="ui-card-body"><div class="row g-3">
                @foreach($suggestions as $product)<div class="col-6"><x-product-card :product="$product" /></div>@endforeach
            </div></div>
        </div>
    </div>
</div>

@if($recentlyViewed->count())
    <div class="ui-card mt-4 rise">
        <div class="ui-card-head"><span><i class="bi bi-clock-history text-accent me-2"></i>Récemment consultés</span></div>
        <div class="ui-card-body"><div class="row g-3">@foreach($recentlyViewed as $product)<div class="col-6 col-md-3"><x-product-card :product="$product" /></div>@endforeach</div></div>
    </div>
@endif

@include('partials.dash-extras')
@endsection
