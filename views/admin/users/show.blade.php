@extends('layouts.admin')
@section('title', $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Profil client</h5>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-4">
                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:64px;height:64px;font-size:1.6rem;color:#fff">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <div class="text-muted small">{{ $user->email }}</div>
                <div class="mt-2">
                    <span class="badge {{ $user->isAdmin() ? 'bg-danger' : 'bg-secondary' }}">
                        {{ $user->isAdmin() ? 'Administrateur' : 'Client' }}
                    </span>
                </div>
                <div class="mt-3 text-muted small">Inscrit le {{ $user->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-receipt me-2 text-primary"></i>Commandes ({{ $user->orders->count() }})
            </div>
            <div class="card-body p-0">
                @forelse($user->orders->sortByDesc('created_at') as $order)
                <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <div class="small fw-semibold">{{ $order->order_number }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $order->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="text-center">
                        <span class="badge badge-status badge-status-{{ $order->status }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div class="fw-bold small text-primary">{{ $order->formatted_total }}</div>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @empty
                <div class="p-4 text-muted text-center small">Aucune commande.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
