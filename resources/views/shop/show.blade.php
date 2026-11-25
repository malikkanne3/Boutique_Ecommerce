@extends('layouts.app')
@section('title', $product->name)

@section('content')
@php
    $variantsData = $product->variants->map(fn ($v) => ['id' => $v->id, 'size' => $v->size, 'color' => $v->color, 'stock' => (int) $v->stock])->values();
    $sizes  = $product->sizes;
    $colors = $product->colors;
    $canBuy = $product->is_available && $product->stock > 0 && ! $onVacation;
@endphp
<div class="container" style="max-width:1140px">
    <nav class="small mb-3 text-muted">
        <a href="{{ route('home') }}">Accueil</a> / <a href="{{ route('shop.index') }}">Boutique</a>
        @if($product->category) / <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>@endif
    </nav>

    <div class="row g-4 g-lg-5">
        <div class="col-md-6">
            <div class="gallery rise"><img id="main-photo" src="{{ $product->gallery[0] }}" alt="{{ $product->name }}"></div>
            @if(count($product->gallery) > 1)
                <div class="thumbs">
                    @foreach($product->gallery as $i => $url)<img src="{{ $url }}" alt="Photo {{ $i + 1 }}" class="{{ $i === 0 ? 'on' : '' }}" onclick="document.getElementById('main-photo').src=this.src;document.querySelectorAll('.thumbs img').forEach(function(x){x.classList.remove('on')});this.classList.add('on')">@endforeach
                </div>
            @endif
        </div>

        <div class="col-md-6 rise">
            @if(! $product->is_active)<div class="alert alert-warning py-2">Produit masqué : visible uniquement par son vendeur et les admins.</div>@endif
            @if($product->category)<span class="badge rounded-pill bg-light text-accent border mb-2">{{ $product->category->name }}</span>@endif
            <div class="d-flex justify-content-between align-items-start gap-3">
                <h1 class="fw-bold" style="letter-spacing:-.03em">{{ $product->name }}</h1>
                @auth
                    @php $isFav = \App\Support\Favorites::has($product->id); @endphp
                    <form action="{{ route('favorites.toggle', $product) }}" method="POST" data-no-loading>@csrf
                        <button class="fav-btn {{ $isFav ? 'on' : '' }}" title="{{ $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"><i class="bi bi-heart{{ $isFav ? '-fill' : '' }}"></i></button></form>
                @else
                    <a href="{{ route('login') }}" class="fav-btn" title="Connectez-vous pour ajouter aux favoris"><i class="bi bi-heart"></i></a>
                @endauth
            </div>
            @if($product->rating_count > 0)
                <a href="#avis" class="d-inline-flex align-items-center gap-2 mb-2 text-reset"><x-stars :value="$product->rating_avg" size="1.05rem" /><b>{{ number_format($product->rating_avg, 1) }}</b><span class="text-muted small">({{ $product->rating_count }} avis)</span></a>
            @endif

            <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                @if($product->seller)<x-avatar :user="$product->seller" size="sm" />@else<i class="bi bi-shop text-accent"></i>@endif
                <span>Vendu par <a href="{{ $product->seller ? route('shops.show', $product->seller) : route('shop.index') }}" class="fw-bold">{{ $product->seller_name }}</a>
                    @if($product->seller && $product->seller->seller_rating_count > 0)<span class="ms-1"><x-stars :value="$product->seller->seller_rating_avg" :count="$product->seller->seller_rating_count" size=".8rem" /></span>@endif</span>
            </div>

            <div class="d-flex align-items-baseline gap-2 mb-2">
                <span class="display-6 fw-bold text-accent" style="letter-spacing:-.03em">{{ $product->formatted_price }}</span>
                @if($product->is_on_sale)<span class="price-old fs-5">{{ $product->formatted_original_price }}</span><span class="badge-sale">-{{ $product->discount_percent }}%</span>@endif
            </div>
            @if($onVacation)
                <div class="alert alert-warning d-flex gap-2 align-items-center py-2"><i class="bi bi-umbrella fs-5"></i><div><b>Boutique en vacances</b>@if($product->seller->vacation_until) — retour le {{ $product->seller->vacation_until->format('d/m/Y') }}@endif<br><small>{{ $product->seller->vacation_message ?: 'Les commandes sont suspendues temporairement.' }}</small></div></div>
            @endif
            @if($product->is_on_sale && $product->discount_ends_at)
                <div class="promo-banner mb-2"><i class="bi bi-lightning-charge-fill"></i> Promotion jusqu'au {{ $product->discount_ends_at->format('d/m/Y à H:i') }}</div>
            @endif
            @if($product->bulk_min_qty && $product->bulk_percent)
                <div class="bulk-banner mb-2"><i class="bi bi-stack"></i> À partir de {{ $product->bulk_min_qty }} unités : -{{ $product->bulk_percent }}% sur chaque article</div>
            @endif

            <p class="text-muted mt-3" style="line-height:1.7">{{ $product->description ?: 'Aucune description fournie par le vendeur.' }}</p>

            <form action="{{ route('cart.add', $product) }}" method="POST" id="buy-form">
                @csrf
                <input type="hidden" name="variant" id="variant-id" value="">

                @if($sizes->isNotEmpty())
                    <div class="mb-3">
                        <div class="opt-title">Taille <span id="size-sel" class="text-accent"></span></div>
                        <div class="opt-chips" id="size-chips">
                            @foreach($sizes as $sz)<span><input type="radio" name="size_pick" id="sz-{{ $loop->index }}" value="{{ $sz }}"><label for="sz-{{ $loop->index }}">{{ $sz }}</label></span>@endforeach
                        </div>
                    </div>
                @endif
                @if($colors->isNotEmpty())
                    <div class="mb-3">
                        <div class="opt-title">Couleur <span id="color-sel" class="text-accent"></span></div>
                        <div class="opt-chips" id="color-chips">
                            @foreach($colors as $co)<span><input type="radio" name="color_pick" id="co-{{ $loop->index }}" value="{{ $co }}"><label for="co-{{ $loop->index }}">{{ $co }}</label></span>@endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-3" id="stock-line">
                    @if(! $canBuy)<span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Rupture de stock</span>
                    @elseif($sizes->isEmpty() && $colors->isEmpty())
                        @if($product->is_low_stock)<span class="stock-low"><i class="bi bi-exclamation-circle-fill me-1"></i>Plus que {{ $product->stock }} en stock !</span>
                        @else<span class="stock-ok"><i class="bi bi-check-circle-fill me-1"></i>Disponible ({{ $product->stock }} en stock)</span>@endif
                    @else<span class="text-muted"><i class="bi bi-hand-index me-1"></i>Choisissez vos options pour voir la disponibilité</span>@endif
                </div>

                @if($canBuy)
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <div class="qty" data-qty>
                            <button type="button" data-qty-minus aria-label="Moins">−</button>
                            <input type="number" name="quantity" id="qty-input" value="1" min="1" max="{{ $product->stock }}" aria-label="Quantité">
                            <button type="button" data-qty-plus aria-label="Plus">+</button>
                        </div>
                        <button class="btn btn-primary btn-lg px-4 flex-grow-1" id="add-btn"><i class="bi bi-cart-plus me-2"></i>Ajouter au panier</button>
                    </div>
                @endif
            </form>

            @auth
                @if($product->seller && auth()->user()->canMessage($product->seller))
                    <a href="{{ route('messages.conversation', $product->seller) }}" class="btn btn-soft mt-3"><i class="bi bi-chat-dots me-1"></i>{{ auth()->user()->isVendeur() ? 'Discuter avec ce vendeur' : 'Écrire au vendeur' }}</a>
                @elseif($product->seller && auth()->id() !== $product->seller_id && auth()->user()->isClient())
                    <div class="small text-muted mt-3"><i class="bi bi-chat-dots me-1"></i>Vous pourrez écrire au vendeur dès votre première commande chez lui.</div>
                @endif
            @endauth

            <div class="row g-2 mt-4 small text-muted">
                <div class="col-6"><i class="bi bi-truck text-accent me-1"></i>Livraison par nos partenaires</div>
                <div class="col-6"><i class="bi bi-arrow-repeat text-accent me-1"></i>Annulation avant confirmation</div>
            </div>
        </div>
    </div>

    {{-- Avis --}}
    <div class="card-ui p-4 mt-5" id="avis">
        <div class="row g-4">
            <div class="col-md-4">
                <h4 class="fw-bold mb-3">Avis des clients</h4>
                @if($product->rating_count > 0)
                    <div class="display-5 fw-bold">{{ number_format($product->rating_avg, 1) }}<small class="fs-6 text-muted"> / 5</small></div>
                    <x-stars :value="$product->rating_avg" size="1.2rem" /><div class="small text-muted mb-3">{{ $product->rating_count }} avis</div>
                    @foreach([5, 4, 3, 2, 1] as $n)
                        @php $c = (int) ($distribution[$n] ?? 0); $pct = $product->rating_count ? round($c / $product->rating_count * 100) : 0; @endphp
                        <div class="d-flex align-items-center gap-2 small mb-1"><span style="width:14px">{{ $n }}</span><i class="bi bi-star-fill stars"></i><div class="rating-bar flex-grow-1"><span style="width:{{ $pct }}%"></span></div><span class="text-muted" style="width:26px">{{ $c }}</span></div>
                    @endforeach
                @else
                    <p class="text-muted">Pas encore d'avis. Les clients ayant reçu ce produit peuvent le noter depuis leur commande.</p>
                @endif
            </div>
            <div class="col-md-8">
                @forelse($reviews as $r)
                    <div class="review-card">
                        <div class="d-flex align-items-center gap-2 mb-1"><x-avatar :user="$r->user" size="xs" /><b class="small">{{ $r->user ? \Illuminate\Support\Str::before($r->user->name, ' ') : 'Client' }}</b><x-stars :value="$r->rating" size=".85rem" /><small class="text-muted ms-auto">{{ $r->created_at->format('d/m/Y') }}</small></div>
                        @if($r->comment)<p class="mb-1">{{ $r->comment }}</p>@endif
                        @if($r->reply)<div class="review-reply"><b>Réponse de {{ $product->seller_name }} :</b> {{ $r->reply }}</div>@endif
                        @auth
                            @if(auth()->id() === (int) $r->seller_id && ! $r->reply)
                                <form action="{{ route('reviews.reply', $r) }}" method="POST" class="mt-2 d-flex gap-2">@csrf
                                    <input type="text" name="reply" class="form-control form-control-sm" placeholder="Répondre à cet avis…" maxlength="1000" required><button class="btn btn-sm btn-primary">Répondre</button></form>
                            @endif
                        @endauth
                    </div>
                @empty
                    <div class="empty"><i class="bi bi-chat-square-heart"></i><p class="mb-0">Soyez le premier à donner votre avis.</p></div>
                @endforelse
            </div>
        </div>
    </div>

    @if($recent->count())
        <div class="section-head"><h2>Récemment consultés</h2></div>
        <div class="row g-3">@foreach($recent as $p)<div class="col-6 col-md-4 col-lg-2"><x-product-card :product="$p" /></div>@endforeach</div>
    @endif

    @if($related->count())
        <div class="section-head"><h2>Dans la même catégorie</h2></div>
        <div class="row g-3">@foreach($related as $p)<div class="col-6 col-md-3"><x-product-card :product="$p" /></div>@endforeach</div>
    @endif
</div>
@endsection

@push('scripts')
@if($variantsData->isNotEmpty())
<script>
(function () {
    var variants = @json($variantsData);
    var hasSize = {{ $sizes->isNotEmpty() ? 'true' : 'false' }}, hasColor = {{ $colors->isNotEmpty() ? 'true' : 'false' }};
    var form = document.getElementById('buy-form'), vid = document.getElementById('variant-id'), line = document.getElementById('stock-line');
    var qty = document.getElementById('qty-input'), btn = document.getElementById('add-btn');

    function picked(name) { var r = form.querySelector('input[name=' + name + ']:checked'); return r ? r.value : null; }

    function refresh() {
        var size = picked('size_pick'), color = picked('color_pick');
        document.getElementById('size-sel') && (document.getElementById('size-sel').textContent = size ? '— ' + size : '');
        document.getElementById('color-sel') && (document.getElementById('color-sel').textContent = color ? '— ' + color : '');

        // Désactive les options sans stock compte tenu de l'autre choix
        form.querySelectorAll('input[name=size_pick]').forEach(function (i) {
            i.disabled = !variants.some(function (v) { return v.size === i.value && v.stock > 0 && (!color || v.color === color); });
        });
        form.querySelectorAll('input[name=color_pick]').forEach(function (i) {
            i.disabled = !variants.some(function (v) { return v.color === i.value && v.stock > 0 && (!size || v.size === size); });
        });

        var ready = (!hasSize || size) && (!hasColor || color);
        var match = ready ? variants.find(function (v) { return (!hasSize || v.size === size) && (!hasColor || v.color === color); }) : null;
        vid.value = match ? match.id : '';

        if (!ready) { line.innerHTML = '<span class="text-muted"><i class="bi bi-hand-index me-1"></i>Choisissez ' + (hasSize && !size ? 'une taille' : 'une couleur') + '</span>'; if (btn) btn.disabled = true; return; }
        if (!match || match.stock < 1) { line.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Cette combinaison est en rupture</span>'; if (btn) btn.disabled = true; return; }
        line.innerHTML = match.stock <= 5
            ? '<span class="stock-low"><i class="bi bi-exclamation-circle-fill me-1"></i>Plus que ' + match.stock + ' disponible(s) !</span>'
            : '<span class="stock-ok"><i class="bi bi-check-circle-fill me-1"></i>Disponible (' + match.stock + ' en stock)</span>';
        if (qty) { qty.max = match.stock; if (+qty.value > match.stock) qty.value = match.stock; }
        if (btn) btn.disabled = false;
    }
    form.addEventListener('change', refresh);
    refresh();
})();
</script>
@endif
@endpush
