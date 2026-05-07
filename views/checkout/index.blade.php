@extends('layouts.app')
@section('title', 'Passer la commande')

@push('styles')
<style>
.checkout-card { border-radius: 16px; }
.summary-card { border-radius: 16px; position: sticky; top: 90px; }
.form-label { font-weight: 600; font-size: .88rem; }
.cart-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
</style>
@endpush

@section('content')
<div class="container">
    <h1 class="fw-bold mb-4">Finaliser la commande</h1>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="row g-4">

            {{-- FORMULAIRE --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm checkout-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-person-lines-fill me-2 text-primary"></i>Informations de livraison
                        </h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nom complet *</label>
                                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
                                       value="{{ old('customer_name', auth()->user()->name) }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror"
                                       value="{{ old('customer_email', auth()->user()->email) }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror"
                                       value="{{ old('customer_phone') }}" placeholder="+221 77 000 00 00">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Adresse de livraison *</label>
                                <textarea name="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror"
                                          rows="3" required placeholder="Rue, quartier, ville...">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RÉSUMÉ --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm summary-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-receipt me-2 text-primary"></i>Votre commande
                        </h5>

                        @foreach($cart as $item)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://via.placeholder.com/50' }}"
                                 class="cart-thumb" alt="{{ $item['name'] }}">
                            <div class="flex-grow-1">
                                <div class="small fw-semibold">{{ $item['name'] }}</div>
                                <div class="text-muted" style="font-size:.78rem">× {{ $item['quantity'] }}</div>
                            </div>
                            <div class="small fw-bold">{{ number_format($item['price'] * $item['quantity'], 2, ',', ' ') }} FCFA</div>
                        </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span>Total à payer</span>
                            <span class="text-primary">{{ number_format($total, 2, ',', ' ') }} FCFA</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-check2-circle me-2"></i>Valider la commande
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left me-1"></i>Retour au panier
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
