@extends('layouts.app')
@section('title', 'Boutique')

@section('content')
<div class="container" style="max-width:1240px">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="letter-spacing:-.03em">{{ request('search') ? 'Résultats pour « '.request('search').' »' : 'Boutique' }}</h1>
            <p class="text-muted mb-0">{{ $products->total() }} produit(s) disponible(s)</p>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-center" data-no-loading>
            @foreach(request()->except(['sort', 'page']) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
            <label class="small fw-bold text-muted">Trier par</label>
            <select name="sort" class="form-select form-select-sm" style="width:auto" data-autosubmit>
                @foreach(['latest' => 'Nouveautés', 'price_asc' => 'Prix croissant', 'price_desc' => 'Prix décroissant', 'name' => 'Nom A-Z'] as $k => $label)
                    <option value="{{ $k }}" @selected(request('sort', 'latest') === $k)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="row g-4">
        <aside class="col-lg-3">
            <form class="filter-box" method="GET" data-no-loading>
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                <h6 class="mb-2">Catégories</h6>
                <div class="mb-3">
                    <a href="{{ route('shop.index', request()->except(['category', 'page'])) }}" class="filter-link {{ request('category') ? '' : 'active' }}">Toutes <span>{{ $allCount }}</span></a>
                    @foreach($categories as $cat)
                        <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" class="filter-link {{ request('category') === $cat->slug ? 'active' : '' }}">{{ $cat->name }} <span>{{ $cat->products_count }}</span></a>
                    @endforeach
                </div>
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif

                <h6 class="mb-2">Prix (FCFA)</h6>
                <div class="d-flex gap-2 mb-3">
                    <input type="number" name="min" min="0" value="{{ request('min') }}" class="form-control form-control-sm" placeholder="Min">
                    <input type="number" name="max" min="0" value="{{ request('max') }}" class="form-control form-control-sm" placeholder="Max">
                </div>

                @if($sellers->count())
                    <h6 class="mb-2">Vendeur</h6>
                    <select name="seller" class="form-select form-select-sm mb-3">
                        <option value="">Tous les vendeurs</option>
                        @foreach($sellers as $s)<option value="{{ $s->id }}" @selected((int) request('seller') === $s->id)>{{ $s->display_name }}</option>@endforeach
                    </select>
                @endif

                <button class="btn btn-primary btn-sm w-100 mb-2">Appliquer</button>
                <a href="{{ route('shop.index') }}" class="btn btn-soft btn-sm w-100">Réinitialiser</a>
            </form>
        </aside>

        <div class="col-lg-9">
            <div class="row g-3">
                @forelse($products as $product)
                    <div class="col-6 col-md-4"><x-product-card :product="$product" /></div>
                @empty
                    <div class="col-12 card-ui"><x-empty-state icon="search" title="Aucun produit trouvé" text="Essayez d'autres mots-clés ou retirez certains filtres.">
                        <a href="{{ route('shop.index') }}" class="btn btn-primary">Voir toute la boutique</a></x-empty-state></div>
                @endforelse
            </div>
            <div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
