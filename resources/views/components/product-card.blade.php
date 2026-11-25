@props(['product'])
@php $hasVariants = $product->variants->isNotEmpty(); $isFav = \App\Support\Favorites::has($product->id); @endphp
<div class="pcard h-100">
    <div class="pcard-media">
        <a href="{{ route('shop.show', $product) }}" class="pcard-img">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
            @if($product->is_on_sale)<span class="pcard-tag tag-sale">-{{ $product->discount_percent }}%</span>@endif
            @if($product->is_low_stock)<span class="pcard-tag tag-warn">Plus que {{ $product->stock }}</span>@endif
            <span class="pcard-cat">{{ $product->category?->name }}</span>
        </a>
        @auth
            <form action="{{ route('favorites.toggle', $product) }}" method="POST" data-no-loading>@csrf
                <button class="pcard-fav {{ $isFav ? 'on' : '' }}" title="{{ $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}" aria-label="Favori"><i class="bi bi-heart{{ $isFav ? '-fill' : '' }}"></i></button></form>
        @else
            <a href="{{ route('login') }}" class="pcard-fav" title="Connectez-vous pour ajouter aux favoris"><i class="bi bi-heart"></i></a>
        @endauth
    </div>
    <div class="pcard-body">
        <a href="{{ route('shop.show', $product) }}" class="pcard-name">{{ $product->name }}</a>
        <div class="pcard-seller d-flex align-items-center gap-1">
            @if($product->seller)<x-avatar :user="$product->seller" size="xs" />@else<i class="bi bi-shop"></i>@endif
            <span class="text-truncate">{{ $product->seller_name }}</span>
        </div>
        @if($product->rating_count > 0)<div class="mt-1"><x-stars :value="$product->rating_avg" :count="$product->rating_count" size=".8rem" /></div>@endif
        @if($hasVariants)
            <div class="small text-muted mt-1">
                @if($product->sizes->isNotEmpty())<i class="bi bi-rulers"></i> {{ $product->sizes->count() }} taille(s)@endif
                @if($product->colors->isNotEmpty())<i class="bi bi-palette ms-1"></i> {{ $product->colors->count() }} couleur(s)@endif
            </div>
        @endif
        <div class="d-flex align-items-center justify-content-between mt-auto pt-2">
            <span class="pcard-price">{{ $product->formatted_price }}@if($product->is_on_sale)<span class="price-old">{{ $product->formatted_original_price }}</span>@endif</span>
            @if($hasVariants)
                <a href="{{ route('shop.show', $product) }}" class="pcard-add" title="Choisir taille / couleur"><i class="bi bi-sliders"></i></a>
            @else
                <form action="{{ route('cart.add', $product) }}" method="POST">
                    @csrf
                    <button class="pcard-add" title="Ajouter au panier" data-no-loading><i class="bi bi-cart-plus"></i></button>
                </form>
            @endif
        </div>
    </div>
</div>
