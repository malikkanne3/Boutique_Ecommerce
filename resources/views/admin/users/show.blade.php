@extends('layouts.admin')
@section('title', 'Client : ' . $user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
    <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
    <p class="text-muted mb-0" style="font-size:.85rem">{{ $user->email }}</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.8rem;margin:0 auto 1rem">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h6 class="fw-bold mb-1">{{ $user->name }}</h6>
                <p class="text-muted mb-2" style="font-size:.85rem">{{ $user->email }}</p>
                @if($user->role === 'admin')
                    <span class="badge" style="background:#fee2e2;color:#991b1b;border-radius:8px">Administrateur</span>
                @else
                    <span class="badge" style="background:#d1fae5;color:#065f46;border-radius:8px">Client</span>
                @endif
                <hr class="my-3">
                <div class="d-flex justify-content-around">
                    <div class="text-center">
                        <div class="fw-bold fs-4" style="color:#7c3aed">{{ $user->orders->count() }}</div>
                        <div class="text-muted" style="font-size:.78rem">Commandes</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-4" style="color:#7c3aed">
                            {{ number_format($user->orders->sum('total_amount'), 0, ',', ' ') }}
                        </div>
                        <div class="text-muted" style="font-size:.78rem">FCFA dépensés</div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="text-muted" style="font-size:.82rem">
                    <i class="bi bi-calendar3 me-1"></i>Inscrit le {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-receipt me-2 text-primary"></i>Historique des commandes
            </div>
            <div class="card-body p-0">
                @forelse($user->orders->sortByDesc('created_at') as $order)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <div class="fw-semibold" style="font-family:monospace;font-size:.88rem">{{ $order->order_number }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $order->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <span class="badge badge-status badge-status-{{ $order->status }}">{{ $order->status_label }}</span>
                    <div class="fw-bold" style="color:#7c3aed">{{ $order->formatted_total }}</div>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @empty
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                    Aucune commande pour ce client
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
