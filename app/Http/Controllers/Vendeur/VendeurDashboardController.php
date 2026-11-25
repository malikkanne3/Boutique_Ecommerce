<?php
namespace App\Http\Controllers\Vendeur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Support\Stats;
use Illuminate\Support\Facades\DB;

class VendeurDashboardController extends Controller
{
    public function index()
    {
        $id = auth()->id();

        $stats = [
            'revenue'  => Order::where('seller_id', $id)->where('status', '!=', 'cancelled')->sum('total_amount'),
            'orders'   => Order::where('seller_id', $id)->count(),
            'pending'  => Order::where('seller_id', $id)->where('status', 'pending')->count(),
            'toship'   => Order::where('seller_id', $id)->whereIn('status', ['confirmed', 'assigned', 'delivering', 'problem'])->count(),
            'products' => Product::where('seller_id', $id)->count(),
            'low'      => Product::where('seller_id', $id)->where('stock', '<=', Product::LOW_STOCK)->count(),
            'incidents' => Order::where('seller_id', $id)->where('status', 'problem')->count(),
        ];

        $recentOrders = Order::with('user')->where('seller_id', $id)->latest()->take(6)->get();

        $lowStock = Product::with('variants')->where('seller_id', $id)->where('stock', '<=', Product::LOW_STOCK)
            ->orderBy('stock')->take(6)->get();

        $chart = Stats::daily(
            Order::where('seller_id', $id)->where('status', '!=', 'cancelled')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())->get(['total_amount', 'created_at']),
            14
        );

        $statusCounts = Order::where('seller_id', $id)->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        $topProducts = DB::table('order_product')
            ->join('orders', 'orders.id', '=', 'order_product.order_id')
            ->join('products', 'products.id', '=', 'order_product.product_id')
            ->where('orders.seller_id', $id)
            ->where('orders.status', '!=', 'cancelled')
            ->select('products.id', 'products.name', DB::raw('SUM(order_product.quantity) as qty'), DB::raw('SUM(order_product.quantity * order_product.price) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        return view('vendeur.dashboard', compact('stats', 'recentOrders', 'lowStock', 'chart', 'statusCounts', 'topProducts'));
    }
}
