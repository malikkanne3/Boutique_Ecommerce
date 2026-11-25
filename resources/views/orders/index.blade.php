@extends('layouts.dashboard')
@section('title', 'Mes commandes')

@section('content')
<x-page-head title="Mes commandes" subtitle="Suivez l'état de chacune de vos commandes.">
    <x-slot:actions><a href="{{ route('shop.index') }}" class="btn btn-primary"><i class="bi bi-bag-plus me-1"></i>Nouvel achat</a></x-slot:actions>
</x-page-head>

@php $filter = request('status', ''); @endphp
<div class="chips mb-3">
    @foreach(['' => 'Toutes', 'active' => 'En cours', 'delivered' => 'Livrées', 'cancelled' => 'Annulées'] as $k => $label)
        <a href="{{ route('orders.index', $k ? ['status' => $k] : []) }}" class="chip {{ $filter === (string) $k ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="row g-3">
    @forelse($orders as $order)
        <div class="col-12 rise">
            <div class="ui-card">
                <div class="ui-card-body">
                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <div>
                            <div class="mono">{{ $order->order_number }}</div>
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d/m/Y à H:i') }} · <i class="bi bi-shop ms-1 me-1"></i>{{ $order->seller_name }}</small>
                        </div>
                        <div class="text-end"><x-status-badge :status="$order->status" /><div class="fs-5 fw-bold text-accent mt-1">{{ $order->formatted_total }}</div></div>
                    </div>
                    @if($order->is_active)<div class="mt-3"><x-order-steps :order="$order" /></div>@endif
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <div class="d-flex gap-2 align-items-center">
                            @foreach($order->products->take(4) as $p)<img class="thumb" src="{{ $p->image_url }}" alt="{{ $p->name }}" title="{{ $p->name }}">@endforeach
                            @if($order->products->count() > 4)<span class="small text-muted">+{{ $order->products->count() - 4 }}</span>@endif
                        </div>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-soft btn-sm">Voir le détail <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 ui-card"><x-empty-state icon="bag" title="Aucune commande ici" text="Vos commandes apparaîtront dans cette liste."><a href="{{ route('shop.index') }}" class="btn btn-primary">Découvrir la boutique</a></x-empty-state></div>
    @endforelse
</div>
<div class="mt-4 d-flex justify-content-center">{{ $orders->links() }}</div>
@endsection
