{{-- Bloc commun en bas des tableaux de bord : dernières notifications + annonces --}}
@php
    $exUser = auth()->user();
    $exNotifs = \App\Models\Notification::with('actor')->where('user_id', $exUser->id)->latest()->take(5)->get();
    $exAnn = $exUser->isAdmin() ? collect() : \App\Models\Announcement::whereIn('audience', ['all', $exUser->isVendeur() ? 'vendeur' : ($exUser->isLivreur() ? 'livreur' : 'client')])->latest()->take(3)->get();
@endphp
<div class="row g-4 mt-1">
    <div class="col-lg-{{ $exUser->isAdmin() ? '12' : '7' }}">
        <div class="ui-card rise h-100">
            <div class="ui-card-head"><span><i class="bi bi-bell text-accent me-2"></i>Dernières notifications</span><a href="{{ route('notifications.index') }}" class="small fw-bold">Tout voir</a></div>
            @forelse($exNotifs as $n)
                <a href="{{ $n->link ?: route('notifications.index') }}" class="notif {{ $n->is_read ? '' : 'unread' }}">
                    <span class="position-relative flex-shrink-0">
                        @if($n->actor)<x-avatar :user="$n->actor" size="md" />@else<span class="notif-ico"><i class="bi bi-{{ $n->icon }}"></i></span>@endif
                    </span>
                    <span class="flex-grow-1"><span class="d-block fw-bold small">{{ $n->title }}</span><span class="d-block small text-muted">{{ \Illuminate\Support\Str::limit($n->body, 90) }}</span>
                        <span class="d-block small text-muted"><i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}</span></span>
                </a>
            @empty
                <x-empty-state icon="bell-slash" title="Rien de nouveau" text="Vos notifications s'afficheront ici." />
            @endforelse
        </div>
    </div>
    @unless($exUser->isAdmin())
        <div class="col-lg-5">
            <div class="ui-card rise h-100">
                <div class="ui-card-head"><span><i class="bi bi-megaphone text-accent me-2"></i>Annonces</span><a href="{{ route('announcements.index') }}" class="small fw-bold">Tout voir</a></div>
                @forelse($exAnn as $a)
                    <div class="p-3 border-bottom"><div class="fw-bold small">{{ $a->title }}</div><div class="small text-muted">{{ \Illuminate\Support\Str::limit($a->body, 100) }}</div><div class="small text-muted mt-1">{{ $a->created_at->diffForHumans() }}</div></div>
                @empty
                    <x-empty-state icon="megaphone" title="Aucune annonce" text="Les messages de l'administration apparaîtront ici." />
                @endforelse
            </div>
        </div>
    @endunless
</div>
