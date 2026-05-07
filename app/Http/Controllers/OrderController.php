<?php
namespace App\Http\Controllers;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('products')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load('products.category');
        return view('orders.show', compact('order'));
    }
}