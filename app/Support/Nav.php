<?php
namespace App\Support;

use App\Models\Order;
use App\Models\User;

/** Menu de navigation de chaque tableau de bord. */
class Nav
{
    public static function items(User $user): array
    {
        $msgs   = $user->unreadMessagesCount();
        $notifs = $user->unreadNotificationsCount();

        if ($user->isAdmin()) {
            return [
                ['Tableau de bord', 'speedometer2', 'admin.dashboard', 'admin.dashboard', null],
                ['Commandes', 'receipt', 'admin.orders.index', 'admin.orders.*', Order::where('status', 'pending')->count()],
                ['Produits', 'box-seam', 'admin.products.index', 'admin.products.*', null],
                ['Catégories', 'tags', 'admin.categories.index', 'admin.categories.*', null],
                ['Litiges', 'exclamation-octagon', 'disputes.index', 'disputes.*', \App\Models\Dispute::where('status', 'escalated')->count()],
                ['Avis', 'star', 'admin.reviews.index', 'admin.reviews.*', null],
                ['Utilisateurs', 'people', 'admin.users.index', 'admin.users.*', User::where('status', 'pending')->count()],
                ['Annonces', 'megaphone', 'admin.announcements.index', 'admin.announcements.*', null],
                ['Notifications', 'bell', 'notifications.index', 'notifications.*', $notifs],
                ['Mon profil', 'person-gear', 'profile.edit', 'profile.*', null],
            ];
        }

        if ($user->isVendeur()) {
            return [
                ['Tableau de bord', 'speedometer2', 'vendeur.dashboard', 'vendeur.dashboard', null],
                ['Mes produits', 'box-seam', 'vendeur.products.index', 'vendeur.products.*', null],
                ['Litiges', 'exclamation-octagon', 'disputes.index', 'disputes.*', \App\Models\Dispute::where('seller_id', $user->id)->whereIn('status', ['open', 'escalated'])->count()],
                ['Commandes', 'receipt', 'vendeur.orders.index', 'vendeur.orders.*', Order::where('seller_id', $user->id)->where('status', 'pending')->count()],
                ['Messages', 'chat-dots', 'messages.index', 'messages.*', $msgs],
                ['Annonces', 'megaphone', 'announcements.index', 'announcements.*', null],
                ['Notifications', 'bell', 'notifications.index', 'notifications.*', $notifs],
                ['Ma boutique', 'shop', 'profile.edit', 'profile.*', null],
            ];
        }

        if ($user->isLivreur()) {
            return [
                ['Missions', 'truck', 'livreur.dashboard', 'livreur.dashboard', Order::where('livreur_id', $user->id)->where('status', 'assigned')->count()],
                ['Gains', 'cash-coin', 'livreur.history', 'livreur.history', null],
                ['Messages', 'chat-dots', 'messages.index', 'messages.*', $msgs],
                ['Annonces', 'megaphone', 'announcements.index', 'announcements.*', null],
                ['Profil', 'person-circle', 'profile.edit', 'profile.*', null],
            ];
        }

        return [
            ['Mon espace', 'house-heart', 'client.dashboard', 'client.dashboard', null],
            ['Mon panier', 'cart3', 'cart.index', 'cart.*', \App\Support\Cart::count()],
            ['Favoris', 'heart', 'favorites.index', 'favorites.*', null],
            ['Mes commandes', 'box-seam', 'orders.index', 'orders.*', Order::where('user_id', $user->id)->whereIn('status', Order::ACTIVE)->count()],
            ['Messages', 'chat-dots', 'messages.index', 'messages.*', $msgs],
            ['Litiges', 'exclamation-octagon', 'disputes.index', 'disputes.*', null],
            ['Annonces', 'megaphone', 'announcements.index', 'announcements.*', null],
            ['Notifications', 'bell', 'notifications.index', 'notifications.*', $notifs],
            ['Mon profil', 'person-gear', 'profile.edit', 'profile.*', null],
        ];
    }
}
