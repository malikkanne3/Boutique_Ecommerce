@extends('layouts.app')
@section('title', 'Commande ' . $order->order_number)

@push('styles')
<style>
.order-img { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; }
.info-block { background: #f8fafc; border-radius: 12px; padding: 1.25rem; }
</style>
@endpush

@section('content')
<div class="container">

    {{-- EN-TÊTE --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="fw-bold mb-1">Commande {{ $order->order_number }}</h1>
            <span class="text-muted small">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</span>
        </div>
        <span class="badge badge-status badge-status-{{ $order->status }} fs-6 px-3 py-2">
            {{ $order->status_label }}
        </span>
    </div>

    <div class="row g-4">

        {{-- PRODUITS --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Articles commandés
                </div>
                <div class="card-body p-0">
                    @foreach($order->products as $product)
                    <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <img src="{{ $product->image_url }}" class="order-img" alt="{{ $product->name }}">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <div class="text-muted small">{{ $product->category->name }}</div>
                            <div class="text-muted small">
                                {{ number_format($product->pivot->price, 2, ',', ' ') }} FCFA × {{ $product->pivot->quantity }}
                            </div>
                        </div>
                        <div class="fw-bold text-primary">
                            {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', ' ') }} FCFA
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-primary">{{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>

        {{-- INFOS --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Informations client
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Nom :</strong> {{ $order->customer_name }}</div>
                    <div class="mb-2"><strong>Email :</strong> {{ $order->customer_email }}</div>
                    @if($order->customer_phone)
                        <div class="mb-2"><strong>Tél :</strong> {{ $order->customer_phone }}</div>
                    @endif
                    <div><strong>Adresse :</strong> {{ $order->shipping_address }}</div>
                </div>
            </div>

            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left me-1"></i>Mes commandes
            </a>
        </div>

    </div>
</div>
@endsection
