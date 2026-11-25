{{-- Raccourcis du tableau de bord, selon le rôle --}}
@php
    $qaUser = auth()->user();
    if ($qaUser->isAdmin()) {
        $pendingSellers = \App\Models\User::where('role', 'vendeur')->where('status', 'pending')->count();
        $qa = [
            ['person-check', 'Valider les vendeurs', route('admin.users.index', ['status' => 'pending']), $pendingSellers],
            ['person-plus', 'Créer un compte', route('admin.users.create'), null],
            ['receipt', 'Commandes', route('admin.orders.index'), null],
            ['box-seam', 'Catalogue', route('admin.products.index'), null],
            ['megaphone', 'Envoyer une annonce', route('admin.announcements.index'), null],
        ];
    } elseif ($qaUser->isVendeur()) {
        $qa = [
            ['plus-circle', 'Nouveau produit', route('vendeur.products.create'), null],
            ['receipt', 'Commandes', route('vendeur.orders.index', ['status' => 'pending']), \App\Models\Order::where('seller_id', $qaUser->id)->where('status', 'pending')->count()],
            ['box-seam', 'Mes produits', route('vendeur.products.index'), null],
            ['chat-dots', 'Messages', route('messages.index'), $qaUser->unreadMessagesCount()],
            ['shop', 'Ma boutique', route('profile.edit'), null],
        ];
    } elseif ($qaUser->isLivreur()) {
        $qa = [
            ['truck', 'Mes missions', route('livreur.dashboard'), \App\Models\Order::where('livreur_id', $qaUser->id)->where('status', 'assigned')->count()],
            ['clock-history', 'Historique', route('livreur.history'), null],
            ['chat-dots', 'Messages', route('messages.index'), $qaUser->unreadMessagesCount()],
            ['person-circle', 'Mon profil', route('profile.edit'), null],
        ];
    } else {
        $qa = [
            ['bag-plus', 'Boutique', route('shop.index'), null],
            ['cart3', 'Mon panier', route('cart.index'), \App\Support\Cart::count()],
            ['box-seam', 'Mes commandes', route('orders.index'), null],
            ['chat-dots', 'Messages', route('messages.index'), $qaUser->unreadMessagesCount()],
            ['person-gear', 'Mon profil', route('profile.edit'), null],
        ];
    }
@endphp
<div class="qa-grid mb-4">
    @foreach($qa as [$icon, $label, $url, $badge])
        <a href="{{ $url }}" class="qa rise">
            <span class="qa-ico"><i class="bi bi-{{ $icon }}"></i>@if($badge)<span class="dot">{{ $badge }}</span>@endif</span>
            <span class="qa-label">{{ $label }}</span>
        </a>
    @endforeach
</div>
