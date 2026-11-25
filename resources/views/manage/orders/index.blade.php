@extends('layouts.dashboard')
@section('title', 'Commandes')

@section('content')
<x-page-head :title="$prefix === 'admin' ? 'Toutes les commandes' : 'Commandes de ma boutique'" subtitle="Confirmez, assignez un livreur et suivez chaque livraison." />

<div class="chips mb-3">
    <a href="{{ route($prefix.'.orders.index') }}" class="chip {{ request('status') ? '' : 'active' }}">Toutes <em>{{ $counts->sum() }}</em></a>
    @foreach($statuses as $key => $label)
        <a href="{{ route($prefix.'.orders.index', ['status' => $key]) }}" class="chip {{ request('status') === $key ? 'active' : '' }}">{{ $label }} <em>{{ $counts->get($key, 0) }}</em></a>
    @endforeach
</div>

<form method="GET" class="ui-card ui-card-body mb-3 row g-2 mx-0 rise" data-no-loading>
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <div class="col-md-10"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="N° de commande ou nom du client…"></div>
    <div class="col-md-2"><button class="btn btn-soft w-100"><i class="bi bi-search me-1"></i>Rechercher</button></div>
</form>

<div class="ui-card rise"><div class="table-responsive"><table class="table table-ui table-hover">
    <thead><tr><th>Commande</th><th>Client</th>@if($prefix === 'admin')<th>Vendeur</th>@endif<th>Total</th><th>Statut</th><th>Livreur</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    @forelse($orders as $o)
        <tr>
            <td><a href="{{ route($prefix.'.orders.show', $o) }}" class="mono">{{ $o->order_number }}</a><div class="small text-muted">{{ $o->created_at->format('d/m/Y H:i') }}</div></td>
            <td><div class="d-flex align-items-center gap-2"><x-avatar :user="$o->user" size="xs" /><div>{{ $o->customer_name }}<div class="small text-muted">{{ $o->customer_phone }}</div></div></div></td>
            @if($prefix === 'admin')<td><small>{{ $o->seller_name }}</small></td>@endif
            <td class="fw-bold">{{ $o->formatted_total }}</td>
            <td><x-status-badge :status="$o->status" /></td>
            <td>@if($o->livreur)<small><i class="bi bi-truck me-1"></i>{{ $o->livreur->name }}</small>@else<small class="text-muted">—</small>@endif</td>
            <td class="text-end text-nowrap">
                @if($o->status === 'pending')
                    <form action="{{ route($prefix.'.orders.update', $o) }}" method="POST" class="d-inline" data-no-loading>@csrf @method('PUT')
                        <input type="hidden" name="action" value="confirm"><button class="btn btn-primary btn-sm"><i class="bi bi-check2 me-1"></i>Confirmer</button></form>
                @endif
                <a href="{{ route($prefix.'.orders.show', $o) }}" class="btn btn-soft btn-sm">{{ $o->status === 'confirmed' ? 'Assigner' : 'Détails' }}</a>
            </td>
        </tr>
    @empty
        <tr><td colspan="7"><x-empty-state icon="receipt" title="Aucune commande" text="Les commandes de vos clients apparaîtront ici." /></td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-4 d-flex justify-content-center">{{ $orders->links() }}</div>
@endsection
