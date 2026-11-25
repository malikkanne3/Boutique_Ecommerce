<?php

use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\ForcePasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Livreur\LivreurController;
use App\Http\Controllers\Manage\OrderController as ManageOrderController;
use App\Http\Controllers\Manage\ProductController as ManageProductController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShopPageController;
use App\Http\Controllers\Vendeur\VendeurDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Liens secrets des espaces pro (voir config/portals.php et `php artisan portails:liens`)
|--------------------------------------------------------------------------
*/
$portal = config('portals');

Route::get('/' . $portal['admin'],   [PortalController::class, 'show'])->defaults('role', 'admin')->name('portal.admin');
Route::get('/' . $portal['vendeur'], [PortalController::class, 'show'])->defaults('role', 'vendeur')->name('portal.vendeur');
Route::get('/' . $portal['livreur'], [PortalController::class, 'show'])->defaults('role', 'livreur')->name('portal.livreur');
Route::redirect('/client', '/login');

/*
|--------------------------------------------------------------------------
| Boutique publique
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/boutique/{seller}', [ShopPageController::class, 'show'])->name('shops.show');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',                  [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}',    [CartController::class, 'add'])->name('add');
    Route::post('/update/{product}', [CartController::class, 'update'])->name('update');
    Route::post('/remove/{product}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear',            [CartController::class, 'clear'])->name('clear');
});

/*
|--------------------------------------------------------------------------
| Tous les utilisateurs connectés
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/mot-de-passe-provisoire',  [ForcePasswordController::class, 'show'])->name('password.force');
    Route::post('/mot-de-passe-provisoire', [ForcePasswordController::class, 'update'])->name('password.force.update');

    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/my-orders',                 [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{order}',         [OrderController::class, 'show'])->name('orders.show');
    Route::post('/my-orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/my-orders/{order}/position', [OrderController::class, 'position'])->name('orders.position');
    Route::get('/preuves/{order}', [InvoiceController::class, 'proof'])->name('orders.proof');
    Route::post('/my-orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
    Route::get('/factures/{order}',        [InvoiceController::class, 'invoice'])->name('orders.invoice');
    Route::get('/bons-livraison/{order}',  [InvoiceController::class, 'deliveryNote'])->name('orders.delivery_note');

    Route::get('/favoris',                 [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favoris/{product}',      [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::post('/avis/produit/{product}', [ReviewController::class, 'product'])->name('reviews.product');
    Route::post('/avis/vendeur/{seller}',  [ReviewController::class, 'seller'])->name('reviews.seller');
    Route::post('/avis/{review}/reponse',  [ReviewController::class, 'reply'])->name('reviews.reply');

    Route::prefix('litiges')->name('disputes.')->group(function () {
        Route::get('/',                    [DisputeController::class, 'index'])->name('index');
        Route::get('/nouveau/{order}',     [DisputeController::class, 'create'])->name('create');
        Route::post('/nouveau/{order}',    [DisputeController::class, 'store'])->name('store');
        Route::get('/{dispute}',           [DisputeController::class, 'show'])->name('show');
        Route::post('/{dispute}/repondre', [DisputeController::class, 'reply'])->name('reply');
        Route::post('/{dispute}/arbitrage', [DisputeController::class, 'escalate'])->name('escalate');
        Route::post('/{dispute}/cloturer', [DisputeController::class, 'close'])->name('close');
        Route::post('/{dispute}/decision', [DisputeController::class, 'decide'])->name('decide');
    });

    Route::get('/annonces', [AnnouncementController::class, 'index'])->name('announcements.index');

    Route::get('/notifications',                      [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',            [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/pj/{kind}/{id}', [MessageController::class, 'attachment'])->where('kind', 'direct|group|dispute')->whereNumber('id')->name('chat.attachment');

    Route::prefix('groupes')->name('groups.')->group(function () {
        Route::get('/nouveau',                    [GroupController::class, 'create'])->name('create');
        Route::post('/',                          [GroupController::class, 'store'])->name('store');
        Route::get('/{group}',                    [GroupController::class, 'show'])->name('show');
        Route::post('/{group}/envoyer',           [GroupController::class, 'send'])->name('send');
        Route::post('/{group}/membres',           [GroupController::class, 'addMembers'])->name('members.add');
        Route::delete('/{group}/membres/{user}',  [GroupController::class, 'removeMember'])->name('members.remove');
        Route::post('/{group}/quitter',           [GroupController::class, 'leave'])->name('leave');
        Route::delete('/{group}',                 [GroupController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::post('/transferer', [MessageController::class, 'forward'])->name('forward');
        Route::get('/',        [MessageController::class, 'index'])->name('index');
        Route::get('/{user}',  [MessageController::class, 'conversation'])->name('conversation');
        Route::post('/{user}', [MessageController::class, 'send'])->name('send');
    });

    // Pas de suppression de compte par l'utilisateur : seul un admin peut supprimer ou désactiver
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| 1) CLIENT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/mon-espace', [ClientDashboardController::class, 'index'])->name('client.dashboard');
});

/*
|--------------------------------------------------------------------------
| Gestion produits + commandes (partagée Admin / Vendeur)
|--------------------------------------------------------------------------
*/
$manageRoutes = function (bool $isAdmin = false) {
    // L'admin supervise le catalogue mais ne crée pas de produits et ne touche pas au stock
    Route::resource('products', ManageProductController::class)->except($isAdmin ? ['show', 'create', 'store'] : ['show']);
    Route::patch('products/{product}/toggle', [ManageProductController::class, 'toggle'])->name('products.toggle');
    Route::patch('products/{product}/stock',  [ManageProductController::class, 'stock'])->name('products.stock');
    Route::patch('products/{product}/availability', [ManageProductController::class, 'availability'])->name('products.availability');

    Route::resource('orders', ManageOrderController::class)->only(['index', 'show', 'update']);
};

/*
|--------------------------------------------------------------------------
| 2) VENDEUR (gestionnaire de sa boutique)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:vendeur'])->prefix($portal['vendeur'])->name('vendeur.')->group(function () use ($manageRoutes) {
    Route::get('/dashboard', [VendeurDashboardController::class, 'index'])->name('dashboard');
    $manageRoutes();
    Route::patch('orders/{order}/note', [ManageOrderController::class, 'note'])->name('orders.note');
});

/*
|--------------------------------------------------------------------------
| 3) LIVREUR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:livreur'])->prefix($portal['livreur'])->name('livreur.')->group(function () {
    Route::get('/dashboard',  [LivreurController::class, 'index'])->name('dashboard');
    Route::get('/historique', [LivreurController::class, 'history'])->name('history');
    Route::post('/commandes/{order}/accepter', [LivreurController::class, 'accepter'])->name('accepter');
    Route::post('/commandes/{order}/refuser',  [LivreurController::class, 'refuser'])->name('refuser');
    Route::post('/commandes/{order}/livree',   [LivreurController::class, 'livree'])->name('livree');
    Route::post('/commandes/{order}/incident', [LivreurController::class, 'incident'])->name('incident');
    Route::post('/position',                   [LivreurController::class, 'position'])->name('position');
    Route::post('/disponibilite',              [LivreurController::class, 'availability'])->name('availability');
});

/*
|--------------------------------------------------------------------------
| 4) ADMIN (supervision de toute la plateforme)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix($portal['admin'])->name('admin.')->group(function () use ($manageRoutes) {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    $manageRoutes(true);

    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    Route::resource('users', AdminUserController::class)->only(['index', 'show', 'create', 'store', 'destroy']);
    Route::post('users/{user}/approve',        [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('users/{user}/reject',         [AdminUserController::class, 'reject'])->name('users.reject');
    Route::post('users/{user}/status',         [AdminUserController::class, 'toggleStatus'])->name('users.status');
    Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset');

    // Modération des avis
    Route::get('/avis', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/avis/{review}', [AdminReviewController::class, 'toggle'])->name('reviews.toggle');
    Route::delete('/avis/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Annonces : l'admin écrit, personne ne peut répondre
    Route::get('/annonces',                  [AdminAnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/annonces',                 [AdminAnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/annonces/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');
});

require __DIR__.'/auth.php';
