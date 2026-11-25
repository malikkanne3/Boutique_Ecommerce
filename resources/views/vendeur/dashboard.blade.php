@extends('layouts.dashboard')
@section('title', 'Tableau de bord')
@section('crumb', auth()->user()->display_name)

@section('content')
<x-page-head :title="'Bonjour '.\Illuminate\Support\Str::before(auth()->user()->name, ' ').' 👋'" subtitle="Voici l'activité de votre boutique.">
    <x-slot:actions>
        <a href="{{ route('vendeur.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau produit</a>
        <a href="{{ route('vendeur.orders.index') }}" class="btn btn-ghost"><i class="bi bi-receipt me-1"></i>Commandes</a>
    </x-slot:actions>
</x-page-head>

@include('partials.quick-actions')

@if($stats['incidents'] > 0)
    <a href="{{ route('vendeur.orders.index', ['status' => 'problem']) }}" class="d-flex align-items-center gap-3 p-3 mb-3 rounded-4 rise" style="background:linear-gradient(135deg,#ef4444,#f97316);color:#fff">
        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
        <div class="flex-grow-1"><b>{{ $stats['incidents'] }} livraison(s) en incident</b><div class="small" style="opacity:.9">Réassignez un livreur ou contactez le client.</div></div><i class="bi bi-arrow-right fs-4"></i>
    </a>
@endif

@if($stats['pending'] > 0)
    <a href="{{ route('vendeur.orders.index', ['status' => 'pending']) }}" class="d-flex align-items-center gap-3 p-3 mb-4 rounded-4 rise" style="background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff">
        <i class="bi bi-bell-fill fs-3"></i>
        <div class="flex-grow-1"><b>{{ $stats['pending'] }} commande(s) à confirmer</b><div class="small" style="opacity:.9">Vos clients attendent votre validation.</div></div><i class="bi bi-arrow-right fs-4"></i>
    </a>
@endif

<div class="kpis">
    <x-stat-card icon="cash-stack" label="Chiffre d'affaires" :value="\App\Support\Stats::money($stats['revenue'])" hint="hors commandes annulées" />
    <x-stat-card icon="receipt" tone="info" label="Commandes" :value="$stats['orders']" :hint="$stats['toship'].' en cours de livraison'" />
    <x-stat-card icon="box-seam" tone="ok" label="Produits" :value="$stats['products']" hint="dans votre catalogue" />
    <x-stat-card icon="exclamation-triangle" :tone="$stats['low'] ? 'warn' : 'ok'" label="Stock faible" :value="$stats['low']" hint="5 unités ou moins" />
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8"><div class="ui-card h-100 rise">
        <div class="ui-card-head">Ventes des 14 derniers jours<small>FCFA</small></div>
        <div class="ui-card-body" style="height:300px"><canvas id="revChart"></canvas></div>
    </div></div>
    <div class="col-lg-4"><div class="ui-card h-100 rise">
        <div class="ui-card-head">Commandes par statut</div>
        <div class="ui-card-body" style="height:300px">
            @if(array_sum($statusCounts->all()) > 0)<canvas id="statusChart"></canvas>
            @else<x-empty-state icon="pie-chart" title="Pas encore de données" />@endif
        </div>
    </div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7"><div class="ui-card rise">
        <div class="ui-card-head">Dernières commandes<a href="{{ route('vendeur.orders.index') }}" class="small fw-bold">Tout voir</a></div>
        <div class="table-responsive"><table class="table table-ui">
            <thead><tr><th>N°</th><th>Client</th><th>Total</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            @forelse($recentOrders as $o)
                <tr>
                    <td class="mono">{{ $o->order_number }}</td>
                    <td><div class="d-flex align-items-center gap-2"><x-avatar :user="$o->user" size="xs" /><div>{{ $o->customer_name }}<div class="small text-muted">{{ $o->created_at->diffForHumans() }}</div></div></div></td>
                    <td class="fw-bold">{{ $o->formatted_total }}</td>
                    <td><x-status-badge :status="$o->status" /></td>
                    <td class="text-end"><a href="{{ route('vendeur.orders.show', $o) }}" class="btn btn-soft btn-sm">Gérer</a></td>
                </tr>
            @empty
                <tr><td colspan="5"><x-empty-state icon="receipt" title="Aucune commande" text="Vos commandes apparaîtront ici." /></td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div></div>

    <div class="col-lg-5">
        <div class="ui-card mb-4 rise">
            <div class="ui-card-head">Produits les plus vendus</div>
            @forelse($topProducts as $i => $t)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <span class="avatar avatar-sm">{{ $i + 1 }}</span>
                    <div class="flex-grow-1 text-truncate"><b class="small">{{ $t->name }}</b><div class="small text-muted">{{ $t->qty }} vendu(s)</div></div>
                    <b class="small">{{ \App\Support\Stats::money($t->revenue) }}</b>
                </div>
            @empty<x-empty-state icon="trophy" title="Pas encore de ventes" />@endforelse
        </div>

        <div class="ui-card rise">
            <div class="ui-card-head">Réapprovisionner<small>stock ≤ 5</small></div>
            @forelse($lowStock as $p)
                <form action="{{ route('vendeur.products.stock', $p) }}" method="POST" class="d-flex align-items-center gap-2 px-3 py-2 border-bottom" data-no-loading>
                    @csrf @method('PATCH')
                    <img class="thumb" src="{{ $p->image_url }}" alt="" style="width:38px;height:38px">
                    <div class="flex-grow-1 text-truncate small fw-bold">{{ $p->name }}</div>
                    @if($p->variants->isEmpty())
                        <input type="number" name="stock" min="0" value="{{ $p->stock }}" class="form-control form-control-sm" style="width:76px">
                        <button class="btn btn-sm btn-primary"><i class="bi bi-check-lg"></i></button>
                    @else
                        <a href="{{ route('vendeur.products.edit', $p) }}" class="btn btn-sm btn-soft">Variantes</a>
                    @endif
                </form>
            @empty<x-empty-state icon="check2-circle" title="Stocks en bonne santé" />@endforelse
        </div>
    </div>
</div>

@include('partials.dash-extras')
@endsection

@push('scripts')
    @include('partials.charts')
@endpush
