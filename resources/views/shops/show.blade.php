@extends('layouts.app')
@section('title', $seller->display_name)

@section('content')
@php $open = $seller->isOpenNow(); $today = (int) now()->dayOfWeekIso; @endphp
<div class="container" style="max-width:1140px">

    <div class="shop-banner rise" @if($seller->shop_banner_url) style="background-image:url('{{ $seller->shop_banner_url }}')" @endif></div>
    <div class="shop-head rise">
        <x-avatar :user="$seller" size="xl" />
        <div class="flex-grow-1 pb-1" style="min-width:220px">
            <h1 class="fw-bold mb-1" style="letter-spacing:-.03em">{{ $seller->display_name }}</h1>
            <div class="d-flex flex-wrap align-items-center gap-2 small">
                @if($seller->seller_rating_count > 0)<x-stars :value="$seller->seller_rating_avg" :count="$seller->seller_rating_count" />@else<span class="text-muted">Pas encore d'avis</span>@endif
                <span class="text-muted">· {{ $stats['products'] }} produit(s) · {{ $stats['delivered'] }} commande(s) livrée(s)</span>
                @if($seller->on_vacation)<span class="pill pill-warn"><i class="bi bi-umbrella"></i> En vacances</span>
                @elseif($open === true)<span class="pill pill-ok"><i class="bi bi-door-open"></i> Ouvert maintenant</span>
                @elseif($open === false)<span class="pill pill-off"><i class="bi bi-door-closed"></i> Fermé actuellement</span>@endif
            </div>
        </div>
        <div class="pb-1">
            @auth @if(auth()->user()->canMessage($seller))<a href="{{ route('messages.conversation', $seller) }}" class="btn btn-primary"><i class="bi bi-chat-dots me-1"></i>Contacter</a>@endif @endauth
        </div>
    </div>

    @if($seller->on_vacation)
        <div class="alert alert-warning d-flex gap-2 align-items-center mt-4"><i class="bi bi-umbrella fs-4"></i><div><b>Cette boutique est en vacances</b>@if($seller->vacation_until) — retour le {{ $seller->vacation_until->format('d/m/Y') }}@endif<br><small>{{ $seller->vacation_message ?: 'Les commandes sont suspendues temporairement. Revenez bientôt !' }}</small></div></div>
    @endif

    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            <div class="card-ui p-4 mb-4 rise">
                <h5 class="fw-bold">À propos</h5>
                <p class="text-muted mb-0" style="white-space:pre-line;line-height:1.7">{{ $seller->shop_description ?: 'Ce vendeur n\'a pas encore présenté sa boutique.' }}</p>
            </div>

            <div class="section-head" style="margin-top:1rem"><h2>Produits</h2></div>
            <div class="row g-3">
                @forelse($products as $product)
                    <div class="col-6 col-md-4"><x-product-card :product="$product" /></div>
                @empty
                    <div class="col-12 card-ui"><x-empty-state icon="box-seam" title="Aucun produit disponible" :text="$seller->on_vacation ? 'La boutique est en vacances.' : 'Revenez bientôt !'" /></div>
                @endforelse
            </div>
            <div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
        </div>

        <div class="col-lg-4">
            <div class="card-ui p-4 mb-3 rise">
                <h6 class="fw-bold mb-3"><i class="bi bi-share text-accent me-2"></i>Partager cette boutique</h6>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="{{ $share['whatsapp'] }}" target="_blank" rel="noopener" class="share-btn wa"><i class="bi bi-whatsapp"></i>WhatsApp</a>
                    <a href="{{ $share['facebook'] }}" target="_blank" rel="noopener" class="share-btn fb"><i class="bi bi-facebook"></i>Facebook</a>
                    <a href="{{ $share['x'] }}" target="_blank" rel="noopener" class="share-btn x"><i class="bi bi-twitter-x"></i>X</a>
                </div>
                <div class="input-group input-group-sm"><input type="text" id="shop-url" class="form-control" value="{{ $url }}" readonly><button type="button" class="btn btn-soft" data-copy="#shop-url">Copier</button></div>
            </div>

            @if($seller->has_hours)
                <div class="card-ui p-4 mb-3 rise">
                    <h6 class="fw-bold mb-2"><i class="bi bi-clock text-accent me-2"></i>Horaires d'ouverture</h6>
                    <table class="hours-table w-100">
                        @foreach(\App\Models\User::DAYS as $d => $label)
                            @php $h = $seller->hoursForDay($d); @endphp
                            <tr class="{{ $d === $today ? 'today' : '' }}"><td>{{ $label }}</td><td class="text-end">{{ $h['closed'] ? 'Fermé' : $h['open'].' – '.$h['close'] }}</td></tr>
                        @endforeach
                    </table>
                </div>
            @endif

            @if($reviews->isNotEmpty())
                <div class="card-ui p-4 rise">
                    <h6 class="fw-bold mb-2"><i class="bi bi-star-fill text-warning me-2"></i>Avis sur le vendeur</h6>
                    @foreach($reviews as $r)
                        <div class="review-card"><div class="d-flex align-items-center gap-2"><x-avatar :user="$r->user" size="xs" /><b class="small">{{ $r->user ? \Illuminate\Support\Str::before($r->user->name, ' ') : 'Client' }}</b><x-stars :value="$r->rating" size=".8rem" /></div>
                            @if($r->comment)<p class="small mb-0 mt-1">{{ $r->comment }}</p>@endif</div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
