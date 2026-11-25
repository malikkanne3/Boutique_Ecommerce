@extends('layouts.dashboard')
@section('title', 'Messages')

@section('content')
<x-page-head title="Messagerie" subtitle="Discutez, partagez photos, fichiers et position, créez des groupes.">
    <x-slot:actions>
        <a href="{{ route('groups.create') }}" class="btn btn-primary"><i class="bi bi-people me-1"></i>Nouveau groupe</a>
        <a href="{{ route('announcements.index') }}" class="btn btn-ghost"><i class="bi bi-megaphone me-1"></i>Annonces</a>
    </x-slot:actions>
</x-page-head>

<div class="ui-card chat-wrap rise">
    <div class="chat-list">
        @if($groups->isNotEmpty())
            <div class="chat-section"><i class="bi bi-people-fill me-1"></i>Groupes</div>
            @foreach($groups as $g)
                <a href="{{ route('groups.show', $g) }}" class="chat-item {{ $activeGroup && $activeGroup->id === $g->id ? 'active' : '' }}">
                    <span class="avatar" style="width:40px;height:40px"><i class="bi bi-people-fill"></i></span>
                    <span class="flex-grow-1 min-w-0">
                        <span class="d-flex justify-content-between"><b class="small text-truncate">{{ $g->name }}</b>@if($g->last_message)<small class="text-muted">{{ $g->last_message->created_at->format('H:i') }}</small>@endif</span>
                        <span class="snippet d-block">{{ $g->last_message ? $g->last_message->preview_text : 'Aucun message' }}</span>
                    </span>
                    @if($g->unread_count)<span class="nav-badge">{{ $g->unread_count }}</span>@endif
                </a>
            @endforeach
        @endif

        <div class="chat-section"><i class="bi bi-chat-dots me-1"></i>Conversations</div>
        @forelse($conversations as $c)
            <a href="{{ route('messages.conversation', $c->user) }}" class="chat-item {{ $active && $active->id === $c->user->id ? 'active' : '' }}">
                <x-avatar :user="$c->user" />
                <span class="flex-grow-1 min-w-0">
                    <span class="d-flex justify-content-between"><b class="small text-truncate">{{ $c->user->display_name }}</b><small class="text-muted">{{ $c->last->created_at->format('H:i') }}</small></span>
                    <span class="snippet d-block">{{ $c->last->sender_id === auth()->id() ? 'Vous : ' : '' }}{{ $c->last->preview_text }}</span>
                </span>
                @if($c->unread)<span class="nav-badge">{{ $c->unread }}</span>@endif
            </a>
        @empty
            <div class="px-3 py-2 small text-muted">Aucune conversation pour l'instant.</div>
        @endforelse

        <div class="p-3">
            <label class="form-label small text-muted mb-1">Nouvelle conversation</label>
            @if($contacts->isEmpty())
                <div class="small text-muted"><i class="bi bi-info-circle me-1"></i>Aucun contact pour le moment : vous pourrez écrire aux personnes liées à vos commandes (vendeur, livreur, client).</div>
            @else
                <select class="form-select form-select-sm" onchange="if(this.value) location.href=this.value">
                    <option value="">Choisir un contact lié à vos commandes…</option>
                    @foreach($contacts->groupBy('role') as $role => $list)
                        <optgroup label="{{ \App\Models\User::ROLES[$role] ?? 'Client' }}s">
                            @foreach($list as $u)<option value="{{ route('messages.conversation', $u) }}">{{ $u->display_name }}</option>@endforeach
                        </optgroup>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    <div class="chat-main">
        @if($active)
            <div class="chat-head"><x-avatar :user="$active" size="sm" /><div class="lh-sm">{{ $active->display_name }}<br><x-role-badge :role="$active->role" /></div></div>
            <div class="chat-body">
                @forelse($messages as $m)@include('messages._bubble', ['m' => $m, 'kind' => 'direct'])
                @empty<div class="empty my-auto"><i class="bi bi-chat-heart"></i><p class="mb-0">Dites bonjour à {{ $active->name }} 👋</p></div>@endforelse
            </div>
            @include('messages._composer', ['action' => route('messages.send', $active)])

        @elseif($activeGroup)
            @php $iCreate = $activeGroup->isCreator(auth()->user()); @endphp
            <div class="chat-head">
                <span class="avatar" style="width:40px;height:40px"><i class="bi bi-people-fill"></i></span>
                <div class="lh-sm flex-grow-1">{{ $activeGroup->name }}<br><small class="text-muted fw-normal">{{ $activeGroup->members->count() }} membre(s)</small></div>
                <button class="btn btn-soft btn-sm" data-bs-toggle="collapse" data-bs-target="#group-panel" data-no-loading><i class="bi bi-gear me-1"></i>Membres</button>
            </div>
            <div class="collapse" id="group-panel">
                <div class="p-3 border-bottom" style="background:var(--surface-2)">
                    @foreach($activeGroup->members as $mb)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <x-avatar :user="$mb" size="sm" /><span class="flex-grow-1 small"><b>{{ $mb->display_name }}</b> <x-role-badge :role="$mb->role" />@if($activeGroup->isCreator($mb)) <span class="pill pill-warn">Créateur</span>@endif</span>
                            @if($iCreate && ! $activeGroup->isCreator($mb))
                                <form action="{{ route('groups.members.remove', [$activeGroup, $mb]) }}" method="POST" data-confirm="Retirer {{ $mb->name }} du groupe ?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-person-dash"></i></button></form>
                            @endif
                        </div>
                    @endforeach
                    @if($iCreate && isset($addable) && $addable->isNotEmpty())
                        <form action="{{ route('groups.members.add', $activeGroup) }}" method="POST" class="mt-3">@csrf
                            <label class="form-label small">Ajouter des membres</label>
                            <select name="members[]" class="form-select form-select-sm mb-2" multiple size="4">@foreach($addable as $u)<option value="{{ $u->id }}">{{ $u->display_name }} — {{ $u->role_label }}</option>@endforeach</select>
                            <button class="btn btn-sm btn-primary"><i class="bi bi-person-plus me-1"></i>Ajouter</button>
                        </form>
                    @endif
                    <div class="mt-3">
                        @if($iCreate)
                            <form action="{{ route('groups.destroy', $activeGroup) }}" method="POST" class="d-inline" data-confirm="Supprimer ce groupe et tous ses messages ?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3 me-1"></i>Supprimer le groupe</button></form>
                        @else
                            <form action="{{ route('groups.leave', $activeGroup) }}" method="POST" class="d-inline" data-confirm="Quitter ce groupe ?">@csrf<button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-left me-1"></i>Quitter le groupe</button></form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="chat-body">
                @forelse($messages as $m)@include('messages._bubble', ['m' => $m, 'kind' => 'group'])
                @empty<div class="empty my-auto"><i class="bi bi-people"></i><p class="mb-0">Lancez la discussion du groupe 👋</p></div>@endforelse
            </div>
            @include('messages._composer', ['action' => route('groups.send', $activeGroup)])

        @else
            <div class="empty my-auto"><i class="bi bi-chat-square-text"></i><h5>Vos conversations</h5><p class="mb-3">Sélectionnez une conversation, ou créez un groupe.</p>
                <a href="{{ route('groups.create') }}" class="btn btn-primary"><i class="bi bi-people me-1"></i>Créer un groupe</a></div>
        @endif
    </div>
</div>

@if($active || $activeGroup)
    @include('messages._forward-modal')
@endif
@endsection
