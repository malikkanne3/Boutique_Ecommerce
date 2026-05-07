@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="container" style="max-width:700px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-primary"></i>Mes notifications</h5>
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button class="btn btn-sm btn-outline-primary">Tout marquer comme lu</button>
        </form>
    </div>

    @forelse($notifications as $notif)
    <div class="card mb-2 border-0 shadow-sm {{ !$notif->is_read ? 'border-start border-primary border-3' : '' }}">
        <div class="card-body d-flex align-items-start gap-3 py-3">
            <div style="width:42px;height:42px;border-radius:12px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-{{ $notif->icon }} text-{{ $notif->color }} fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="font-size:.9rem">{{ $notif->title }}</div>
                <div class="text-muted" style="font-size:.82rem">{{ $notif->body }}</div>
                <div class="text-muted mt-1" style="font-size:.75rem">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
            @if(!$notif->is_read)
            <span class="badge bg-primary rounded-pill" style="font-size:.65rem">Nouveau</span>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
        Aucune notification pour le moment.
    </div>
    @endforelse
</div>
@endsection