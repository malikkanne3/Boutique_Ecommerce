@extends('layouts.app')
@section('title', 'Finaliser la commande')

@push('styles')
<style>
.form-section { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 1.75rem; margin-bottom: 1.5rem; }
.form-section-title { font-weight: 800; font-size: 1rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px; }
.section-icon { width: 32px; height: 32px; background: var(--primary-light); color: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.order-summary { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 1.5rem; position: sticky; top: 85px; }
.order-item { display: flex; align-items: center; gap: 12px; margin-bottom: 1rem; }
.order-item-img { width: 52px; height: 52px; object-fit: cover; border-radius: 10px; flex-shrink: 0; }
.payment-option { display: flex; align-items: center; gap: 12px; background: #f8fafc; border-radius: 10px; padding: .75rem 1rem; border: 2px solid transparent; cursor: pointer; transition: all .2s; margin-bottom: .5rem; }
.payment-option:has(input:checked) { border-color: var(--primary); background: var(--primary-light); }
</style>
@endpush

@section('content')
<div class="container" style="max-width:1000px">
    <h1 style="font-size:1.7rem;font-weight:900;letter-spacing:-0.5px;margin-bottom:2rem">
        <i class="bi bi-bag-check me-2" style="color:var(--primary)"></i>Finaliser la commande
    </h1>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="form-section">
                    <div class="form-section-title">
                        <div class="section-icon"><i class="bi bi-person"></i></div>
                        Informations de livraison
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nom complet *</label>
                            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
                                   value="{{ old('customer_name', auth()->user()->name) }}" required placeholder="Ex: Moussa Diallo">
                            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror"
                                   value="{{ old('customer_email', auth()->user()->email) }}" required>
                            @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror"
                                   value="{{ old('customer_phone') }}" placeholder="+221 77 000 00 00">
                            @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Adresse de livraison *</label>
                            <textarea name="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror"
                                      rows="3" required placeholder="Quartier, rue, numéro, ville...">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <div class="section-icon"><i class="bi bi-credit-card"></i></div>
                        Moyen de paiement
                    </div>
                    @foreach([
                        ['wave', '📱', 'Wave', 'Paiement mobile sécurisé'],
                        ['orange_money', '🟠', 'Orange Money', 'Paiement mobile Orange'],
                        ['cash', '💵', 'Paiement à la livraison', 'Payez cash à la réception'],
                    ] as [$val, $icon, $label, $desc])
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="{{ $val }}" {{ $val == 'wave' ? 'checked' : '' }} style="accent-color:var(--primary)">
                        <span style="font-size:1.5rem">{{ $icon }}</span>
                        <div>
                            <div style="font-weight:700;font-size:.9rem">{{ $label }}</div>
                            <div style="font-size:.75rem;color:var(--gray)">{{ $desc }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-5">
                <div class="order-summary">
                    <h6 style="font-weight:800;margin-bottom:1.25rem"><i class="bi bi-receipt me-2 text-primary"></i>Votre commande</h6>
                    @php $total = 0; @endphp
                    @foreach($cart as $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp
                    <div class="order-item">
                       <img src="{{ $item['image'] ? (Str::startsWith($item['image'], 'http') ? $item['image'] : asset('storage/'.$item['image'])) : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=100&q=80' }}"
                             class="order-item-img" alt="{{ $item['name'] }}">
                        <div class="flex-grow-1">
                            <div style="font-weight:600;font-size:.88rem">{{ Str::limit($item['name'], 28) }}</div>
                            <div style="font-size:.75rem;color:var(--gray)">× {{ $item['quantity'] }}</div>
                        </div>
                        <div style="font-weight:700;font-size:.88rem">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between text-muted mb-2" style="font-size:.85rem">
                        <span>Sous-total</span><span>{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-3" style="font-size:.85rem">
                        <span>Livraison</span><span>{{ $total >= 25000 ? 'Gratuite 🎉' : 'À déterminer' }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold" style="font-size:1.15rem">
                        <span>Total</span>
                        <span style="color:var(--primary)">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg mt-4">
                        <i class="bi bi-check2-circle me-2"></i>Confirmer la commande
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left me-1"></i>Retour au panier
                    </a>
                    <div class="text-center mt-3" style="font-size:.75rem;color:var(--gray)">
                        <i class="bi bi-lock-fill me-1"></i>Transaction 100% sécurisée
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
