@extends('layouts.dashboard')
@section('title', $prefix === 'admin' ? 'Catalogue' : 'Mes produits')

@section('content')
@php $isAdmin = $prefix === 'admin'; @endphp
<x-page-head :title="$isAdmin ? 'Catalogue de la plateforme' : 'Mes produits'" :subtitle="$summary['total'].' produit(s) · '.$summary['active'].' visible(s) en boutique'">
    <x-slot:actions>@unless($isAdmin)<a href="{{ route('vendeur.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau produit</a>@endunless</x-slot:actions>
</x-page-head>

@if($isAdmin)
    <div class="alert alert-info d-flex gap-2 align-items-center rise"><i class="bi bi-info-circle fs-5"></i><div>Vous supervisez le catalogue. <b>Le stock et la disponibilité sont gérés uniquement par chaque vendeur.</b> Vous pouvez masquer, modifier les informations ou supprimer un produit.</div></div>
@endif

<div class="chips mb-3">
    <a href="{{ route($prefix.'.products.index') }}" class="chip {{ ! request('stock') && ! request('state') ? 'active' : '' }}">Tous <em>{{ $summary['total'] }}</em></a>
    <a href="{{ route($prefix.'.products.index', ['stock' => 'low']) }}" class="chip {{ request('stock') === 'low' ? 'active' : '' }}">Stock faible <em>{{ $summary['low'] }}</em></a>
    <a href="{{ route($prefix.'.products.index', ['stock' => 'out']) }}" class="chip {{ request('stock') === 'out' ? 'active' : '' }}">Rupture <em>{{ $summary['out'] }}</em></a>
    <a href="{{ route($prefix.'.products.index', ['state' => 'inactive']) }}" class="chip {{ request('state') === 'inactive' ? 'active' : '' }}">Masqués <em>{{ $summary['total'] - $summary['active'] }}</em></a>
</div>

<form method="GET" class="ui-card ui-card-body mb-3 row g-2 align-items-center mx-0 rise" data-no-loading>
    <div class="col-md-6"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Rechercher un produit…"></div>
    <div class="col-md-4"><select name="category" class="form-select" data-autosubmit>
        <option value="">Toutes les catégories</option>
        @foreach($categories as $c)<option value="{{ $c->id }}" @selected((int) request('category') === $c->id)>{{ $c->name }}</option>@endforeach
    </select></div>
    <div class="col-md-2"><button class="btn btn-soft w-100"><i class="bi bi-search me-1"></i>Filtrer</button></div>
</form>

<div class="ui-card rise">
    <div class="table-responsive"><table class="table table-ui table-hover">
        <thead><tr><th>Produit</th>@if($isAdmin)<th>Vendeur</th>@endif<th>Prix</th><th>{{ $isAdmin ? 'Disponibilité' : 'Stock' }}</th><th>Visible</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse($products as $p)
            @php $out = ! $p->is_available || $p->stock <= 0; @endphp
            <tr>
                <td><div class="d-flex align-items-center gap-3"><img class="thumb" src="{{ $p->image_url }}" alt="">
                    <div><div class="fw-bold">{{ $p->name }}</div><small class="text-muted">{{ $p->category?->name ?? 'Sans catégorie' }}</small>
                    @if($p->is_on_sale)<span class="badge-sale ms-1">-{{ $p->discount_percent }}%</span>@endif
                    @if($p->variants->isNotEmpty())<div><span class="var-chip">{{ $p->variants->count() }} variante(s)</span></div>@endif</div></div></td>
                @if($isAdmin)<td><small>{{ $p->seller_name }}</small></td>@endif
                <td class="fw-bold">{{ $p->formatted_price }}@if($p->is_on_sale)<div class="price-old ms-0">{{ $p->formatted_original_price }}</div>@endif</td>
                <td>
                    @if($isAdmin)
                        <span class="pill {{ $out ? 'pill-bad' : ($p->is_low_stock ? 'pill-warn' : 'pill-ok') }}">{{ $p->availability_label }}</span>
                    @else
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            @if($p->variants->isEmpty())
                                <form action="{{ route($prefix.'.products.stock', $p) }}" method="POST" data-no-loading>@csrf @method('PATCH')
                                    <input type="number" name="stock" min="0" value="{{ $p->stock }}" class="form-control form-control-sm" style="width:78px" onchange="this.form.submit()" title="Quantité en stock"></form>
                            @else
                                <a href="{{ route($prefix.'.products.edit', $p) }}" class="small fw-bold" title="Modifier les variantes"><i class="bi bi-boxes me-1"></i>{{ $p->stock }} au total</a>
                            @endif
                            <form action="{{ route($prefix.'.products.availability', $p) }}" method="POST" data-no-loading>@csrf @method('PATCH')
                                <button class="pill border-0 {{ $out ? 'pill-bad' : ($p->is_low_stock ? 'pill-warn' : 'pill-ok') }}" title="Cliquer pour basculer disponible / rupture">{{ $out ? 'Rupture' : ($p->is_low_stock ? 'Stock faible' : 'Disponible') }}</button></form>
                        </div>
                    @endif
                </td>
                <td>
                    <form action="{{ route($prefix.'.products.toggle', $p) }}" method="POST" data-no-loading>@csrf @method('PATCH')
                        <button class="pill border-0 {{ $p->is_active ? 'pill-ok' : 'pill-off' }}" title="Cliquer pour changer"><i class="bi bi-{{ $p->is_active ? 'eye' : 'eye-slash' }}"></i>{{ $p->is_active ? 'Visible' : 'Masqué' }}</button></form>
                </td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('shop.show', $p) }}" target="_blank" class="btn btn-ghost btn-sm" title="Voir en boutique"><i class="bi bi-box-arrow-up-right"></i></a>
                    <a href="{{ route($prefix.'.products.edit', $p) }}" class="btn btn-soft btn-sm"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route($prefix.'.products.destroy', $p) }}" method="POST" class="d-inline" data-confirm="Supprimer « {{ $p->name }} » ?">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6"><x-empty-state icon="box-seam" title="Aucun produit" :text="$isAdmin ? 'Aucun produit ne correspond à ces filtres.' : 'Ajoutez votre premier produit pour le rendre visible en boutique.'">
                @unless($isAdmin)<a href="{{ route('vendeur.products.create') }}" class="btn btn-primary">Ajouter un produit</a>@endunless</x-empty-state></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
<div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
@endsection
