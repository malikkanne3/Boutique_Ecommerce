@extends('layouts.dashboard')
@section('title', 'Litige '.$dispute->order->order_number)

@section('content')
<x-page-head :title="'Litige — '.$dispute->order->order_number" :subtitle="$dispute->reason_label">
    <x-slot:actions><a href="{{ route('disputes.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Litiges</a></x-slot:actions>
</x-page-head>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ui-card chat-wrap rise" style="display:block;min-height:0">
            <div class="chat-head"><i class="bi bi-chat-square-text text-accent"></i><div class="flex-grow-1">Discussion</div><span class="pill {{ $dispute->pill_class }}">{{ $dispute->status_label }}</span></div>
            <div class="chat-body" style="max-height:480px">
                @foreach($dispute->messages as $m)
                    @php $mine = (int) $m->user_id === (int) auth()->id(); $url = $m->attachment_path ? route('chat.attachment', ['dispute', $m->id]) : null; @endphp
                    <div class="bubble {{ $mine ? 'me' : 'them' }}">
                        @unless($mine)<div class="d-flex align-items-center gap-1 mb-1 fw-bold small"><x-avatar :user="$m->user" size="xs" />{{ $m->user->display_name }} <span class="pill pill-off">{{ $m->user->isAdmin() ? 'Administration' : ((int) $m->user_id === (int) $dispute->user_id ? 'Client' : 'Vendeur') }}</span></div>@endunless
                        @if($m->type === 'image' && $url)<a href="{{ $url }}" target="_blank"><img src="{{ $url }}" class="chat-img" alt=""></a>
                        @elseif($m->type === 'file' && $url)<a href="{{ $url }}" class="chat-file"><i class="bi bi-file-earmark-arrow-down"></i><span><b>{{ $m->attachment_name }}</b><small>{{ $m->file_size_label }} · Télécharger</small></span></a>@endif
                        @if($m->body)<div style="white-space:pre-line">{{ $m->body }}</div>@endif
                        <small>{{ $m->created_at->format('d/m H:i') }}</small>
                    </div>
                @endforeach
            </div>

            @if($dispute->is_open)
                <form action="{{ route('disputes.reply', $dispute) }}" method="POST" enctype="multipart/form-data" class="chat-form-wrap" data-no-loading>
                    @csrf
                    <div class="chat-form">
                        <input type="text" name="body" class="form-control" placeholder="Votre réponse…" maxlength="2000" autocomplete="off">
                        <label class="icon-btn mb-0" title="Joindre un fichier ou une photo" style="cursor:pointer"><i class="bi bi-paperclip"></i><input type="file" name="file" class="d-none" accept="image/*,.pdf,.doc,.docx,.txt,.zip" onchange="this.parentNode.style.background='var(--accent-soft)'"></label>
                        <button class="btn btn-primary px-4"><i class="bi bi-send-fill"></i></button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ui-card mb-3 rise"><div class="ui-card-body">
            <h6 class="fw-bold mb-3">Informations</h6>
            <div class="small mb-2"><span class="text-muted">Commande</span><br><a href="{{ $role === 'client' ? route('orders.show', $dispute->order) : ($role === 'seller' ? route('vendeur.orders.show', $dispute->order) : route('admin.orders.show', $dispute->order)) }}" class="fw-bold mono">{{ $dispute->order->order_number }}</a> · {{ $dispute->order->formatted_total }}</div>
            <div class="small mb-2"><span class="text-muted">Client</span><br><span class="d-inline-flex align-items-center gap-2"><x-avatar :user="$dispute->client" size="xs" />{{ $dispute->client->name }}</span></div>
            <div class="small mb-2"><span class="text-muted">Vendeur</span><br>{{ $dispute->seller ? $dispute->seller->display_name : 'E-Shop SN' }}</div>
            <div class="small"><span class="text-muted">Ouvert le</span><br>{{ $dispute->created_at->format('d/m/Y à H:i') }}</div>
        </div></div>

        @if($dispute->status === 'resolved')
            <div class="ui-card rise" style="border-color:#10b981"><div class="ui-card-body">
                <h6 class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>Litige résolu</h6>
                <div class="small"><b>{{ $dispute->resolution_label }}</b></div>
                @if($dispute->admin_decision)<p class="small mt-2 mb-0" style="white-space:pre-line">{{ $dispute->admin_decision }}</p>@endif
            </div></div>
        @else
            @if($role === 'client')
                <div class="ui-card rise"><div class="ui-card-body d-grid gap-2">
                    <form action="{{ route('disputes.close', $dispute) }}" method="POST" data-confirm="Confirmer que le problème est réglé ?">@csrf<button class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1"></i>Le problème est résolu</button></form>
                    @if(in_array($dispute->status, ['open', 'seller_replied']))
                        <form action="{{ route('disputes.escalate', $dispute) }}" method="POST" data-confirm="Transmettre ce litige à l'administration pour arbitrage ?">@csrf<button class="btn btn-outline-danger w-100"><i class="bi bi-shield-exclamation me-1"></i>Demander l'arbitrage de l'admin</button></form>
                    @endif
                    <small class="text-muted">Si le vendeur ne vous répond pas ou que sa réponse ne vous convient pas, demandez l'arbitrage.</small>
                </div></div>
            @elseif($role === 'admin')
                <form action="{{ route('disputes.decide', $dispute) }}" method="POST" class="ui-card rise" data-confirm="Enregistrer cette décision ? Elle clôturera le litige.">@csrf
                    <div class="ui-card-head">Décision de l'administration</div>
                    <div class="ui-card-body">
                        @if($dispute->status !== 'escalated')<div class="alert alert-info small py-2">Le client n'a pas encore demandé l'arbitrage. Vous pouvez tout de même trancher.</div>@endif
                        <label class="form-label">Résolution</label>
                        <select name="resolution" class="form-select mb-3" required>
                            <option value="">Choisir…</option>
                            @foreach(['refund' => 'Remboursement', 'exchange' => 'Échange du produit', 'no_action' => 'Aucune suite'] as $k => $l)<option value="{{ $k }}" @selected(old('resolution') === $k)>{{ $l }}</option>@endforeach
                        </select>
                        <label class="form-label">Explication aux deux parties</label>
                        <textarea name="admin_decision" rows="4" class="form-control mb-3 @error('admin_decision') is-invalid @enderror" required>{{ old('admin_decision') }}</textarea>
                        <button class="btn btn-primary w-100"><i class="bi bi-shield-check me-1"></i>Trancher et clôturer</button>
                    </div>
                </form>
            @endif
        @endif
    </div>
</div>
@endsection
