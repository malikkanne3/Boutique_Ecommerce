<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'      => User::where('role', 'user')->count(),
            'products'   => Product::count(),
            'categories' => Category::count(),
            'orders'     => Order::count(),
            'revenue'    => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'pending'    => Order::where('status', 'pending')->count(),
        ];

        $latestOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        $lowStock = Product::where('stock', '<=', 5)
            ->where('is_active', true)
            ->with('category')
            ->take(6)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'latestOrders', 'lowStock'));
    }
}
