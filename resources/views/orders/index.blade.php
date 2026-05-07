@extends('layouts.app')
@section('title', 'Mes commandes')

@section('content')
<div class="container" style="max-width:900px">
    <h1 style="font-size:1.7rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:.5rem">Mes commandes</h1>
    <p class="text-muted mb-4">Suivez l'état de vos commandes</p>

    @if($orders->count())
    <div class="d-flex flex-column gap-3">
        @foreach($orders as $order)
        <div class="card border-0 shadow-sm" style="border-radius:16px">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div style="font-size:.72rem;color:var(--gray);font-weight:700;text-transform:uppercase;letter-spacing:.5px">Commande</div>
                        <div style="font-weight:800;font-size:1.05rem;font-family:monospace">{{ $order->order_number }}</div>
                        <div style="font-size:.82rem;color:var(--gray);margin-top:4px">
                            <i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d/m/Y à H:i') }}
                            · {{ $order->products->count() }} article(s)
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-status badge-status-{{ $order->status }} mb-2 d-block">
                            @if($order->status === 'pending') <i class="bi bi-clock me-1"></i>
                            @elseif($order->status === 'paid') <i class="bi bi-check-circle me-1"></i>
                            @elseif($order->status === 'shipped') <i class="bi bi-truck me-1"></i>
                            @else <i class="bi bi-x-circle me-1"></i> @endif
                            {{ $order->status_label }}
                        </span>
                        <div style="font-size:1.2rem;font-weight:900;color:var(--primary)">{{ $order->formatted_total }}</div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        @foreach($order->products->take(3) as $product)
                        <img src="{{ $product->image_url }}" style="width:40px;height:40px;object-fit:cover;border-radius:8px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.1)" alt="">
                        @endforeach
                        @if($order->products->count() > 3)
                        <span style="width:40px;height:40px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:var(--gray)">+{{ $order->products->count() - 3 }}</span>
                        @endif
                    </div>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>Voir le détail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $orders->links() }}</div>
    @else
    <div class="text-center py-5">
        <div style="font-size:4rem;margin-bottom:1rem">📦</div>
        <h5 style="font-weight:800;margin-bottom:.5rem">Aucune commande pour l'instant</h5>
        <p class="text-muted mb-4">Commencez à faire du shopping !</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary px-5">Découvrir la boutique</a>
    </div>
    @endif
</div>
@endsection
