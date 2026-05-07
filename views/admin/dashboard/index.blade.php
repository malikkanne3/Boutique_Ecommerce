@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- STATS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#ede9fe">
                    <i class="bi bi-people-fill" style="color:#7c3aed"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ $stats['users'] }}</div>
                    <div class="text-muted small">Clients</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#dbeafe">
                    <i class="bi bi-box-seam-fill" style="color:#1d4ed8"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ $stats['products'] }}</div>
                    <div class="text-muted small">Produits</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#dcfce7">
                    <i class="bi bi-receipt" style="color:#15803d"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ $stats['orders'] }}</div>
                    <div class="text-muted small">Commandes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fef9c3">
                    <i class="bi bi-currency-dollar" style="color:#a16207"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ number_format($stats['revenue'], 0, ',', ' ') }}</div>
                    <div class="text-muted small">Revenus (FCFA)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- DERNIÈRES COMMANDES --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Dernières commandes</span>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestOrders as $order)
                        <tr style="cursor:pointer" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                            <td class="small fw-semibold">{{ $order->order_number }}</td>
                            <td class="small">{{ $order->customer_name }}</td>
                            <td class="small fw-bold">{{ $order->formatted_total }}</td>
                            <td>
                                <span class="badge badge-status badge-status-{{ $order->status }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- STOCK FAIBLE --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-exclamation-triangle text-warning me-1"></i>Stock faible</span>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-warning">Gérer</a>
            </div>
            <div class="card-body p-0">
                @forelse($lowStock as $product)
                <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <div class="small fw-semibold">{{ $product->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $product->category->name }}</div>
                    </div>
                    <span class="badge bg-{{ $product->stock == 0 ? 'danger' : 'warning text-dark' }}">
                        {{ $product->stock }} restant(s)
                    </span>
                </div>
                @empty
                <div class="p-3 text-muted small text-center">Aucun produit en stock faible 🎉</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
