@extends('layouts.dashboard')
@section('title', 'Mes favoris')

@section('content')
<x-page-head title="Mes favoris" subtitle="Les produits que vous avez mis de côté.">
    <x-slot:actions><a href="{{ route('shop.index') }}" class="btn btn-primary"><i class="bi bi-bag-plus me-1"></i>Continuer mes achats</a></x-slot:actions>
</x-page-head>

<div class="row g-3">
    @forelse($products as $product)
        <div class="col-6 col-md-4 col-xl-3 rise"><x-product-card :product="$product" /></div>
    @empty
        <div class="col-12 ui-card"><x-empty-state icon="heart" title="Aucun favori pour l'instant" text="Touchez le cœur d'un produit pour le retrouver ici.">
            <a href="{{ route('shop.index') }}" class="btn btn-primary">Découvrir la boutique</a></x-empty-state></div>
    @endforelse
</div>
<div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
@endsection
