@extends('layouts.dashboard')
@section('title', 'Notifications')

@section('content')
<x-page-head title="Notifications" :subtitle="$unread ? $unread.' non lue(s)' : 'Vous êtes à jour 🎉'">
    <x-slot:actions>
        @if($unread)<form action="{{ route('notifications.readAll') }}" method="POST">@csrf<button class="btn btn-soft"><i class="bi bi-check2-all me-1"></i>Tout marquer comme lu</button></form>@endif
    </x-slot:actions>
</x-page-head>

<div class="ui-card overflow-hidden rise">
    @forelse($notifications as $n)
        <form action="{{ route('notifications.read', $n) }}" method="POST" data-no-loading>@csrf
            <button class="notif w-100 text-start border-0 {{ $n->is_read ? '' : 'unread' }}" style="background:{{ $n->is_read ? 'transparent' : '' }}">
                <span class="position-relative flex-shrink-0">
                    @if($n->actor)<x-avatar :user="$n->actor" /><span class="position-absolute notif-badge"><i class="bi bi-{{ $n->icon }}"></i></span>
                    @else<span class="notif-ico"><i class="bi bi-{{ $n->icon }}"></i></span>@endif
                </span>
                <span class="flex-grow-1">
                    <span class="d-block fw-bold">{{ $n->title }}</span>
                    <span class="d-block small text-muted">{{ $n->body }}</span>
                    <span class="d-block small text-muted mt-1"><i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}</span>
                </span>
                @if(! $n->is_read)<span class="pill pill-pending align-self-center">Nouveau</span>@endif
            </button>
        </form>
    @empty
        <x-empty-state icon="bell-slash" title="Aucune notification" text="Vous serez prévenu ici dès qu'il y a du nouveau." />
    @endforelse
</div>
<div class="mt-4 d-flex justify-content-center">{{ $notifications->links() }}</div>
@endsection
