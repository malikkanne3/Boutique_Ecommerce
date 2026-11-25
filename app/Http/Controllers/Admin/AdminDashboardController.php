<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\Stats;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'revenue'  => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'orders'   => Order::count(),
            'pending'  => Order::where('status', 'pending')->count(),
            'products' => Product::count(),
            'clients'  => User::where('role', 'client')->count(),
            'vendeurs' => User::where('role', 'vendeur')->count(),
            'livreurs' => User::where('role', 'livreur')->count(),
            'pending_sellers' => User::where('role', 'vendeur')->where('status', 'pending')->count(),
        ];

        $recentOrders = Order::with(['user', 'seller'])->latest()->take(8)->get();

        $chart = Stats::daily(
            Order::where('status', '!=', 'cancelled')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())->get(['total_amount', 'created_at']),
            14
        );

        $statusCounts = Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        // Meilleurs vendeurs par chiffre d'affaires
        $rows = Order::where('status', '!=', 'cancelled')
            ->selectRaw('seller_id, SUM(total_amount) as revenue, COUNT(*) as cnt')
            ->groupBy('seller_id')->orderByDesc('revenue')->take(5)->get();
        $names = User::whereIn('id', $rows->pluck('seller_id')->filter())->pluck('shop_name', 'id');
        $fallback = User::whereIn('id', $rows->pluck('seller_id')->filter())->pluck('name', 'id');
        $topSellers = $rows->map(function ($r) use ($names, $fallback) {
            $r->label = $r->seller_id ? ($names[$r->seller_id] ?: ($fallback[$r->seller_id] ?? 'Vendeur supprimé')) : 'Boutique officielle';
            return $r;
        });

        $newUsers = User::latest()->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'recentOrders', 'chart', 'statusCounts', 'topSellers', 'newUsers'));
    }
}
