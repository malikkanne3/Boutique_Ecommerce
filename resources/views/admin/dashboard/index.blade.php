@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Clients',    'value'=>$stats['users'],    'icon'=>'people-fill',   'bg'=>'#ede9fe','color'=>'#7c3aed'],
        ['label'=>'Produits',   'value'=>$stats['products'], 'icon'=>'box-seam-fill', 'bg'=>'#dbeafe','color'=>'#1d4ed8'],
        ['label'=>'Catégories', 'value'=>$stats['categories'],'icon'=>'tag-fill',     'bg'=>'#d1fae5','color'=>'#059669'],
        ['label'=>'Commandes',  'value'=>$stats['orders'],   'icon'=>'receipt',       'bg'=>'#fef3c7','color'=>'#d97706'],
        ['label'=>'En attente', 'value'=>$stats['pending'],  'icon'=>'clock-fill',    'bg'=>'#fee2e2','color'=>'#dc2626'],
        ['label'=>'Revenus',    'value'=>number_format($stats['revenue'],0,',',' ').' F', 'icon'=>'graph-up-arrow','bg'=>'#f0fdf4','color'=>'#16a34a'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-6 col-lg-4 col-xl-2">
        <div class="card stat-card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:{{ $c['bg'] }}">
                    <i class="bi bi-{{ $c['icon'] }}" style="color:{{ $c['color'] }}"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5 lh-1 mb-1">{{ $c['value'] }}</div>
                    <div class="text-muted" style="font-size:.75rem">{{ $c['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Dernières commandes</span>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>N°</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @forelse($latestOrders as $order)
                        <tr style="cursor:pointer" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                            <td class="small fw-semibold">{{ $order->order_number }}</td>
                            <td class="small">{{ $order->customer_name }}</td>
                            <td class="small fw-bold">{{ $order->formatted_total }}</td>
                            <td><span class="badge badge-status badge-status-{{ $order->status }}">{{ $order->status_label }}</span></td>
                            <td class="small text-muted">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucune commande</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-exclamation-triangle text-warning me-1"></i>Stock faible</span>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-warning">Gérer</a>
            </div>
            <div class="card-body p-0">
                @forelse($lowStock as $product)
                <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <img src="{{ $product->image_url }}" style="width:42px;height:42px;object-fit:cover;border-radius:8px;flex-shrink:0" alt="">
                    <div class="flex-grow-1">
                        <div class="small fw-semibold">{{ $product->name }}</div>
                        <div class="text-muted" style="font-size:.73rem">{{ $product->category->name ?? '' }}</div>
                    </div>
                    <span class="badge bg-{{ $product->stock == 0 ? 'danger' : 'warning text-dark' }}">{{ $product->stock }}</span>
                </div>
                @empty
                <div class="p-4 text-center text-muted small">Aucun produit en stock faible 🎉</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
