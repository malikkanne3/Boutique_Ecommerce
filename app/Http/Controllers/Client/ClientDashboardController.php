<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Support\RecentlyViewed;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'orders'    => Order::where('user_id', $user->id)->count(),
            'active'    => Order::where('user_id', $user->id)->whereIn('status', Order::ACTIVE)->count(),
            'delivered' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
            'spent'     => Order::where('user_id', $user->id)->where('status', '!=', 'cancelled')->sum('total_amount'),
        ];

        $recent = Order::with(['products', 'seller'])->where('user_id', $user->id)->latest()->take(5)->get();

        $tracking = Order::with(['products', 'seller', 'livreur'])
            ->where('user_id', $user->id)
            ->whereIn('status', Order::ACTIVE)
            ->latest()->first();

        $suggestions = Product::active()->inStock()->with(['category', 'seller', 'variants'])->inRandomOrder()->take(4)->get();

        $recentlyViewed = RecentlyViewed::products(null, 4);
        $stats['favorites'] = \App\Models\Favorite::where('user_id', $user->id)->count();

        return view('client.dashboard', compact('stats', 'recent', 'tracking', 'suggestions', 'recentlyViewed'));
    }
}
