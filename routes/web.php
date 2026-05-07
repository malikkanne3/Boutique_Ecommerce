<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Boutique
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

// Panier (accessible sans connexion)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',                    [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}',      [CartController::class, 'add'])->name('add');
    Route::post('/update/{product}',   [CartController::class, 'update'])->name('update');
    Route::post('/remove/{product}',   [CartController::class, 'remove'])->name('remove');
    Route::post('/clear',              [CartController::class, 'clear'])->name('clear');
});

/*
|--------------------------------------------------------------------------
| Routes authentifiées (clients)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/my-orders',       [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

/*
|--------------------------------------------------------------------------
| Routes admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', AdminCategoryController::class);
    Route::resource('products',   AdminProductController::class);

    Route::resource('orders', AdminOrderController::class)
        ->only(['index', 'show', 'update']);

    Route::resource('users', AdminUserController::class)
    ->only(['index', 'show', 'create', 'store', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Auth (généré par Breeze — déjà inclus via require, mais rappel)
|--------------------------------------------------------------------------
*/

// Routes livreur
Route::middleware(['auth', 'livreur'])
    ->prefix('livreur')
    ->name('livreur.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Livreur\LivreurController::class, 'index'])->name('dashboard');
        Route::post('/commandes/{order}/accepter', [App\Http\Controllers\Livreur\LivreurController::class, 'accepter'])->name('accepter');
        Route::post('/commandes/{order}/refuser', [App\Http\Controllers\Livreur\LivreurController::class, 'refuser'])->name('refuser');
        Route::post('/commandes/{order}/livree', [App\Http\Controllers\Livreur\LivreurController::class, 'livree'])->name('livree');
    });

    // Notifications
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'readAll'])->name('notifications.readAll');
});

// Messagerie
Route::middleware('auth')->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [App\Http\Controllers\MessageController::class, 'index'])->name('index');
    Route::get('/{user}', [App\Http\Controllers\MessageController::class, 'conversation'])->name('conversation');
    Route::post('/{user}', [App\Http\Controllers\MessageController::class, 'send'])->name('send');
});
    
require __DIR__.'/auth.php';
