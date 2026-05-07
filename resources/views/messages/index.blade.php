@extends('layouts.app')
@section('title', 'Messagerie')
@section('content')
<div class="container" style="max-width:900px">
    <h5 class="fw-bold mb-4"><i class="bi bi-chat-dots me-2 text-primary"></i>Messagerie</h5>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Conversations</div>
                <div class="list-group list-group-flush">
                    @forelse($conversations as $contact)
                    <a href="{{ route('messages.conversation', $contact) }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0">
                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:.88rem">{{ $contact->name }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ ucfirst($contact->role) }}</div>
                        </div>
                    </a>
                    @empty
                    <div class="p-3 text-muted text-center" style="font-size:.85rem">Aucune conversation</div>
                    @endforelse
                </div>
            </div>

            {{-- Nouveau message --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white fw-bold">Nouveau message</div>
                <div class="card-body">
                    <select class="form-select form-select-sm mb-2" id="newConvSelect">
                        <option value="">Choisir un utilisateur</option>
                        @foreach($users as $u)
                        <option value="{{ route('messages.conversation', $u) }}">{{ $u->name }} ({{ $u->role }})</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary btn-sm w-100"
                        onclick="if(document.getElementById('newConvSelect').value) window.location=document.getElementById('newConvSelect').value">
                        <i class="bi bi-chat-plus me-1"></i>Démarrer
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center" style="min-height:400px">
                <div class="text-center text-muted">
                    <i class="bi bi-chat-dots fs-1 d-block mb-3" style="color:#7c3aed;opacity:.3"></i>
                    <p>Sélectionne une conversation pour commencer</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection