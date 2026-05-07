@extends('layouts.admin')
@section('title', 'Commande ' . $order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
        <h5 class="fw-bold mb-0" style="font-family:monospace">{{ $order->order_number }}</h5>
        <p class="text-muted mb-0" style="font-size:.82rem">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
    </div>
    <span class="badge badge-status badge-status-{{ $order->status }}" style="font-size:.85rem;padding:.5rem 1rem">
        {{ $order->status_label }}
    </span>
</div>

<div class="row g-4">

    {{-- COLONNE GAUCHE --}}
    <div class="col-lg-8">

        {{-- ARTICLES --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-box-seam me-2 text-primary"></i>Articles commandés
            </div>
            <div class="card-body p-0">
                @foreach($order->products as $product)
                <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <img src="{{ $product->image_url }}"
                         style="width:60px;height:60px;object-fit:cover;border-radius:10px;flex-shrink:0" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.9rem">{{ $product->name }}</div>
                        <div class="text-muted" style="font-size:.78rem">{{ $product->category->name ?? '' }}</div>
                        <div class="text-muted" style="font-size:.82rem">
                            {{ number_format($product->pivot->price, 0, ',', ' ') }} FCFA × {{ $product->pivot->quantity }}
                        </div>
                    </div>
                    <div class="fw-bold" style="color:#7c3aed;white-space:nowrap">
                        {{ number_format($product->pivot->price * $product->pivot->quantity, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                @endforeach
            </div>
            <div class="card-footer bg-white d-flex justify-content-between fw-bold py-3"
                 style="font-size:1.05rem;border-top:2px solid #e2e8f0!important">
                <span>Total commande</span>
                <span style="color:#7c3aed">{{ $order->formatted_total }}</span>
            </div>
        </div>

        {{-- CHANGER STATUT --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-arrow-repeat me-2 text-primary"></i>Mettre à jour le statut
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="d-flex gap-3 align-items-end">
                    @csrf @method('PUT')
                    <div class="flex-grow-1">
                        <label class="form-label">Nouveau statut</label>
                        <select name="status" class="form-select">
                            @foreach($statuses as $val => $label)
                                <option value="{{ $val }}" {{ $order->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Mettre à jour
                    </button>
                </form>
            </div>
        </div>

        {{-- ATTRIBUER UN LIVREUR --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-person-check me-2 text-warning"></i>Attribuer un livreur
            </div>
            <div class="card-body">
                @if($order->livreur)
                <div class="alert alert-info mb-3">
                    <i class="bi bi-truck me-2"></i>Livreur actuel : <strong>{{ $order->livreur->name }}</strong>
                </div>
                @endif
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="d-flex gap-3 align-items-end">
                    @csrf @method('PUT')
                    <div class="flex-grow-1">
                        <label class="form-label">Choisir un livreur</label>
                        <select name="livreur_id" class="form-select">
                            <option value="">-- Sélectionner un livreur --</option>
                            @foreach($livreurs as $livreur)
                            <option value="{{ $livreur->id }}" {{ $order->livreur_id == $livreur->id ? 'selected' : '' }}>
                                {{ $livreur->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-truck me-1"></i>Assigner
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- COLONNE DROITE --}}
    <div class="col-lg-4">

        {{-- INFO CLIENT --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-person me-2 text-primary"></i>Informations client
            </div>
            <div class="card-body" style="font-size:.88rem">
                <div class="mb-2">
                    <span class="text-muted">Nom :</span>
                    <span class="fw-semibold ms-1">{{ $order->customer_name }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Email :</span>
                    <a href="mailto:{{ $order->customer_email }}" class="ms-1">{{ $order->customer_email }}</a>
                </div>
                @if($order->customer_phone)
                <div class="mb-2">
                    <span class="text-muted">Téléphone :</span>
                    <a href="tel:{{ $order->customer_phone }}" class="ms-1">{{ $order->customer_phone }}</a>
                </div>
                @endif
                <div class="mb-2">
                    <span class="text-muted">Adresse :</span>
                    <div class="mt-1 p-2 rounded" style="background:#f8fafc;font-size:.85rem">{{ $order->shipping_address }}</div>
                </div>
            </div>
        </div>

        {{-- RÉSUMÉ --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-info-circle me-2 text-primary"></i>Résumé
            </div>
            <div class="card-body" style="font-size:.88rem">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Statut</span>
                    <span class="badge badge-status badge-status-{{ $order->status }}">{{ $order->status_label }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Articles</span>
                    <span class="fw-semibold">{{ $order->products->count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date</span>
                    <span class="fw-semibold">{{ $order->created_at->format('d/m/Y') }}</span>
                </div>
                @if($order->livreur)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Livreur</span>
                    <span class="fw-semibold">{{ $order->livreur->name }}</span>
                </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between fw-bold" style="font-size:1rem">
                    <span>Total</span>
                    <span style="color:#7c3aed">{{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection