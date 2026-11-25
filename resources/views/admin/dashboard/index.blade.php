@extends('layouts.dashboard')
@section('title', 'Vue d\'ensemble')

@section('content')
<x-page-head title="Supervision de la plateforme" subtitle="Activité globale de E-Shop SN.">
    <x-slot:actions>
        <a href="{{ route('admin.users.create', ['role' => 'vendeur']) }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Nouveau compte</a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost"><i class="bi bi-receipt me-1"></i>Commandes</a>
    </x-slot:actions>
</x-page-head>

@include('partials.quick-actions')

@if($stats['pending_sellers'] > 0)
    <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="d-flex align-items-center gap-3 p-3 mb-4 rounded-4 rise" style="background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff">
        <i class="bi bi-shop fs-3"></i>
        <div class="flex-grow-1"><b>{{ $stats['pending_sellers'] }} demande(s) de boutique à valider</b><div class="small" style="opacity:.9">Des vendeurs attendent votre décision.</div></div><i class="bi bi-arrow-right fs-4"></i>
    </a>
@endif

<div class="kpis">
    <x-stat-card icon="cash-stack" label="Volume d'affaires" :value="\App\Support\Stats::money($stats['revenue'])" hint="toutes boutiques confondues" />
    <x-stat-card icon="receipt" tone="info" label="Commandes" :value="$stats['orders']" :hint="$stats['pending'].' en attente'" />
    <x-stat-card icon="box-seam" tone="ok" label="Produits" :value="$stats['products']" />
    <x-stat-card icon="people" tone="warn" label="Utilisateurs" :value="$stats['clients'] + $stats['vendeurs'] + $stats['livreurs']" :hint="$stats['clients'].' clients · '.$stats['vendeurs'].' vendeurs · '.$stats['livreurs'].' livreurs'" />
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8"><div class="ui-card h-100 rise">
        <div class="ui-card-head">Volume d'affaires — 14 jours<small>FCFA</small></div>
        <div class="ui-card-body" style="height:300px"><canvas id="revChart"></canvas></div>
    </div></div>
    <div class="col-lg-4"><div class="ui-card h-100 rise">
        <div class="ui-card-head">Répartition des commandes</div>
        <div class="ui-card-body" style="height:300px">
            @if(array_sum($statusCounts->all()) > 0)<canvas id="statusChart"></canvas>@else<x-empty-state icon="pie-chart" title="Pas encore de données" />@endif
        </div>
    </div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7"><div class="ui-card rise">
        <div class="ui-card-head">Dernières commandes<a href="{{ route('admin.orders.index') }}" class="small fw-bold">Tout voir</a></div>
        <div class="table-responsive"><table class="table table-ui table-hover">
            <thead><tr><th>N°</th><th>Client</th><th>Vendeur</th><th>Total</th><th>Statut</th></tr></thead>
            <tbody>
            @forelse($recentOrders as $o)
                <tr onclick="location.href='{{ route('admin.orders.show', $o) }}'" style="cursor:pointer">
                    <td class="mono">{{ $o->order_number }}</td><td><div class="d-flex align-items-center gap-2"><x-avatar :user="$o->user" size="xs" />{{ $o->customer_name }}</div></td><td><small>{{ $o->seller_name }}</small></td>
                    <td class="fw-bold">{{ $o->formatted_total }}</td><td><x-status-badge :status="$o->status" /></td>
                </tr>
            @empty<tr><td colspan="5"><x-empty-state icon="receipt" title="Aucune commande" /></td></tr>@endforelse
            </tbody>
        </table></div>
    </div></div>

    <div class="col-lg-5">
        <div class="ui-card mb-4 rise">
            <div class="ui-card-head">Meilleurs vendeurs</div>
            @forelse($topSellers as $i => $s)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <span class="avatar avatar-sm">{{ $i + 1 }}</span>
                    <div class="flex-grow-1 text-truncate"><b class="small">{{ $s->label }}</b><div class="small text-muted">{{ $s->cnt }} commande(s)</div></div>
                    <b class="small">{{ \App\Support\Stats::money($s->revenue) }}</b>
                </div>
            @empty<x-empty-state icon="shop" title="Pas encore de ventes" />@endforelse
        </div>

        <div class="ui-card rise">
            <div class="ui-card-head">Derniers inscrits<a href="{{ route('admin.users.index') }}" class="small fw-bold">Gérer</a></div>
            @foreach($newUsers as $u)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <x-avatar :user="$u" size="sm" />
                    <div class="flex-grow-1 text-truncate"><b class="small">{{ $u->name }}</b><div class="small text-muted text-truncate">{{ $u->email }}</div></div>
                    <x-role-badge :role="$u->role" />
                </div>
            @endforeach
        </div>
    </div>
</div>

@include('partials.dash-extras')
@endsection

@push('scripts')
    @include('partials.charts')
@endpush
