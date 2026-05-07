@extends('layouts.app')
@section('title', 'Commande ' . $order->order_number)

@section('content')
<div class="container" style="max-width:900px">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left me-1"></i>Mes commandes
            </a>
            <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:.25rem">{{ $order->order_number }}</h1>
            <p class="text-muted mb-0" style="font-size:.85rem">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <span class="badge badge-status badge-status-{{ $order->status }} px-4 py-2" style="font-size:.85rem">
            {{ $order->status_label }}
        </span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px">
                <div class="card-header bg-white fw-bold border-0 pt-4 pb-0 px-4">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Articles commandés
                </div>
                <div class="card-body p-0">
                    @foreach($order->products as $product)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <img src="{{ $product->image_url }}" style="width:65px;height:65px;object-fit:cover;border-radius:12px;flex-shrink:0" alt="">
                        <div class="flex-grow-1">
                            <div style="font-weight:700;font-size:.92rem">{{ $product->name }}</div>
                            <div style="font-size:.78rem;color:var(--gray)">{{ $product->category->name ?? '' }}</div>
                            <div style="font-size:.82rem;color:var(--gray)">{{ number_format($product->pivot->price, 0, ',', ' ') }} FCFA × {{ $product->pivot->quantity }}</div>
                        </div>
                        <div style="font-weight:800;color:var(--primary)">{{ number_format($product->pivot->price * $product->pivot->quantity, 0, ',', ' ') }} FCFA</div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white border-0 px-4 py-3 d-flex justify-content-between fw-bold" style="font-size:1.1rem;border-top:2px solid var(--border)!important">
                    <span>Total</span>
                    <span style="color:var(--primary)">{{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius:16px">
                <div class="card-header bg-white fw-bold border-0 pt-4 pb-0 px-4">
                    <i class="bi bi-person me-2 text-primary"></i>Livraison
                </div>
                <div class="card-body px-4" style="font-size:.88rem">
                    <div class="mb-2"><strong>Nom :</strong> {{ $order->customer_name }}</div>
                    <div class="mb-2"><strong>Email :</strong> {{ $order->customer_email }}</div>
                    @if($order->customer_phone)
                    <div class="mb-2"><strong>Tél :</strong> {{ $order->customer_phone }}</div>
                    @endif
                    <div><strong>Adresse :</strong> {{ $order->shipping_address }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
