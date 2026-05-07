@extends('layouts.admin')
@section('title', 'Commande ' . $order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">{{ $order->order_number }}</h5>
        <span class="text-muted small">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="row g-4">

    {{-- PRODUITS --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-box-seam me-2 text-primary"></i>Articles
            </div>
            <div class="card-body p-0">
                @foreach($order->products as $product)
                <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <img src="{{ $product->image_url }}" width="55" height="55"
                         style="object-fit:cover;border-radius:10px" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $product->name }}</div>
                        <div class="text-muted" style="font-size:.78rem">
                            {{ number_format($product->pivot->price, 2, ',', ' ') }} FCFA × {{ $product->pivot->quantity }}
                        </div>
                    </div>
                    <div class="fw-bold small text-primary">
                        {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', ' ') }} FCFA
                    </div>
                </div>
                @endforeach
            </div>
            <div class="card-footer bg-white d-flex justify-content-between fw-bold">
                <span>Total</span>
                <span class="text-primary">{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-5">

        {{-- CHANGER STATUT --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-arrow-repeat me-2 text-primary"></i>Changer le statut
            </div>
            <div class="card-body">
                <div class="mb-2">
                    Statut actuel :
                    <span class="badge badge-status badge-status-{{ $order->status }} ms-1">
                        {{ $order->status_label }}
                    </span>
                </div>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm">
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary btn-sm">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- INFOS CLIENT --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-person me-2 text-primary"></i>Client
            </div>
            <div class="card-body small">
                <div class="mb-1"><strong>Nom :</strong> {{ $order->customer_name }}</div>
                <div class="mb-1"><strong>Email :</strong> {{ $order->customer_email }}</div>
                @if($order->customer_phone)
                    <div class="mb-1"><strong>Tél :</strong> {{ $order->customer_phone }}</div>
                @endif
                <div><strong>Adresse :</strong> {{ $order->shipping_address }}</div>
            </div>
        </div>

    </div>
</div>
@endsection
