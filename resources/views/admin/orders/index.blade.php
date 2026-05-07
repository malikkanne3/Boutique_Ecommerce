@extends('layouts.admin')
@section('title', 'Commandes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Gestion des commandes</h5>
        <p class="text-muted mb-0" style="font-size:.85rem">{{ $orders->total() }} commande(s)</p>
    </div>
</div>

{{-- FILTRES --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control" style="max-width:260px"
                   placeholder="N° commande, nom client..." value="{{ request('search') }}">
            <select name="status" class="form-select" style="max-width:180px">
                <option value="">Tous les statuts</option>
                @foreach($statuses as $val => $label)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filtrer</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N° Commande</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Articles</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-weight:700;font-size:.85rem">{{ $order->order_number }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold" style="font-size:.88rem">{{ $order->customer_name }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ $order->customer_email }}</div>
                        </td>
                        <td class="fw-bold" style="color:#7c3aed;font-size:.9rem">{{ $order->formatted_total }}</td>
                        <td>
                            <span class="badge badge-status badge-status-{{ $order->status }}">
                                @if($order->status==='pending') <i class="bi bi-clock me-1"></i>
                                @elseif($order->status==='paid') <i class="bi bi-check-circle me-1"></i>
                                @elseif($order->status==='shipped') <i class="bi bi-truck me-1"></i>
                                @else <i class="bi bi-x-circle me-1"></i> @endif
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="text-muted" style="font-size:.85rem">{{ $order->products->count() }} article(s)</td>
                        <td class="text-muted" style="font-size:.82rem">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Détail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                            Aucune commande trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white d-flex justify-content-center py-3">
        {{ $orders->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
