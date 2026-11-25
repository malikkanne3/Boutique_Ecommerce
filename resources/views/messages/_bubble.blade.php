{{-- Bulle de message : $m, $kind = 'direct' | 'group' --}}
@php
    $mine = (int) $m->sender_id === (int) auth()->id();
    $url  = $m->attachment_path ? route('chat.attachment', [$kind, $m->id]) : null;
@endphp
<div class="bubble {{ $mine ? 'me' : 'them' }}">
    @if($kind === 'group' && ! $mine && $m->sender)
        <div class="d-flex align-items-center gap-1 mb-1 fw-bold small"><x-avatar :user="$m->sender" size="xs" />{{ $m->sender->display_name }}</div>
    @endif
    @if($m->forwarded_from)
        <div class="fwd"><i class="bi bi-reply-fill"></i> Transféré — message de {{ $m->forwarded_from }}</div>
    @endif

    @if($m->type === 'image' && $url)
        <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" alt="{{ $m->attachment_name }}" class="chat-img" loading="lazy"></a>
    @elseif($m->type === 'file' && $url)
        <a href="{{ $url }}" class="chat-file">
            <i class="bi bi-file-earmark-arrow-down"></i>
            <span><b>{{ $m->attachment_name }}</b><small>{{ $m->file_size_label }} · Télécharger</small></span>
        </a>
    @elseif($m->type === 'location' && $m->has_location)
        <div class="chat-loc">
            <iframe src="{{ $m->embed_map_url }}" loading="lazy" title="Position"></iframe>
            <a href="{{ $m->map_url }}" target="_blank"><i class="bi bi-geo-alt-fill"></i> Position exacte — ouvrir dans Maps</a>
        </div>
    @endif

    @if($m->body)<div style="white-space:pre-line">{{ $m->body }}</div>@endif

    <small class="d-flex align-items-center justify-content-between gap-2">
        <span>{{ $m->created_at->format('d/m H:i') }}</span>
        <button type="button" class="fwd-btn" data-forward data-kind="{{ $kind }}" data-id="{{ $m->id }}" title="Transférer"><i class="bi bi-reply-fill" style="transform:scaleX(-1)"></i> Transférer</button>
    </small>
</div>
