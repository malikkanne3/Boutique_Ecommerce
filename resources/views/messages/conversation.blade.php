@extends('layouts.app')
@section('title', 'Message — ' . $user->name)
@section('content')
<div class="container" style="max-width:900px">
    <h5 class="fw-bold mb-4"><i class="bi bi-chat-dots me-2 text-primary"></i>Messagerie</h5>
    <div class="row g-4">
        {{-- LISTE CONTACTS --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Conversations</div>
                <div class="list-group list-group-flush">
                    @foreach($users as $contact)
                    <a href="{{ route('messages.conversation', $contact) }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 {{ $contact->id == $user->id ? 'active' : '' }}">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0">
                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:.88rem">{{ $contact->name }}</div>
                            <div style="font-size:.75rem;opacity:.7">{{ ucfirst($contact->role) }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CONVERSATION --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="height:550px;display:flex;flex-direction:column">
                {{-- Header --}}
                <div class="card-header bg-white d-flex align-items-center gap-3">
                    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ $user->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ ucfirst($user->role) }}</div>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-grow-1 p-3 overflow-auto" id="chatBox" style="background:#f8fafc">
                    @forelse($messages as $msg)
                    <div class="d-flex {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                        <div style="max-width:70%;padding:10px 14px;border-radius:{{ $msg->sender_id == auth()->id() ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }};background:{{ $msg->sender_id == auth()->id() ? 'linear-gradient(135deg,#7c3aed,#a855f7)' : '#fff' }};color:{{ $msg->sender_id == auth()->id() ? '#fff' : '#0f172a' }};box-shadow:0 2px 8px rgba(0,0,0,.08);font-size:.88rem">
                            {{ $msg->body }}
                            <div style="font-size:.68rem;opacity:.6;margin-top:4px;text-align:right">
                                {{ $msg->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5" style="font-size:.85rem">
                        <i class="bi bi-chat-dots d-block fs-2 mb-2"></i>
                        Commencez la conversation !
                    </div>
                    @endforelse
                </div>

                {{-- Input --}}
                <div class="card-footer bg-white p-3">
                    <form method="POST" action="{{ route('messages.send', $user) }}" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="body" class="form-control" placeholder="Écrire un message..." autocomplete="off" required>
                        <button type="submit" class="btn btn-primary px-3">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    // Auto-scroll vers le bas
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endpush