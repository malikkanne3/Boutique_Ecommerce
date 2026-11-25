@extends('layouts.app')
@section('title', 'Commande confirmée')

@section('content')
<div class="container" style="max-width:860px">
    <div class="d-flex justify-content-center mb-4"><div class="step-dots"><span class="on"><span class="n"><i class="bi bi-check"></i></span> Panier</span><i class="bi bi-chevron-right"></i><span class="on"><span class="n"><i class="bi bi-check"></i></span> Livraison &amp; paiement</span><i class="bi bi-chevron-right"></i><span class="on"><span class="n">3</span> Confirmation</span></div></div>

    <div class="card-ui p-4 p-md-5 text-center mb-4 rise">
        <div class="mx-auto mb-3 d-grid" style="width:84px;height:84px;border-radius:50%;background:rgba(16,185,129,.14);color:#10b981;place-items:center;font-size:2.6rem"><i class="bi bi-check-lg"></i></div>
        <h1 class="fw-bold" style="letter-spacing:-.03em">Merci pour votre commande !</h1>
        <p class="text-muted mb-0">{{ $orders->count() > 1 ? 'Vos '.$orders->count().' commandes ont été enregistrées (une par vendeur).' : 'Votre commande a bien été enregistrée.' }} Vous recevez une confirmation par e-mail et par notification.</p>
    </div>

    @foreach($orders as $order)
        @php $pm = $methods[$order->payment_method] ?? $methods['cash']; @endphp
        <div class="card-ui mb-3 rise">
            <div class="p-3 d-flex justify-content-between flex-wrap gap-2 border-bottom">
                <div><b class="font-monospace">{{ $order->order_number }}</b><div class="small text-muted"><i class="bi bi-shop me-1"></i>{{ $order->seller_name }}</div></div>
                <div class="text-end"><span class="fs-5 fw-bold text-accent">{{ $order->formatted_total }}</span><div class="small text-muted">En attente de confirmation du vendeur</div></div>
            </div>
            <div class="p-3">
                @foreach($order->products as $p)
                    <div class="d-flex gap-3 align-items-center mb-2">
                        <img src="{{ $p->image_url }}" style="width:44px;height:44px;border-radius:10px;object-fit:cover" alt="">
                        <div class="flex-grow-1 small"><b>{{ $p->name }}</b>
                            @if($p->pivot->size)<span class="var-chip ms-1">{{ $p->pivot->size }}</span>@endif @if($p->pivot->color)<span class="var-chip">{{ $p->pivot->color }}</span>@endif
                            <span class="text-muted"> × {{ $p->pivot->quantity }}</span></div>
                    </div>
                @endforeach
                <hr>
                <div class="row g-3 small">
                    <div class="col-md-6"><div class="text-muted">Livraison</div><b>{{ $order->customer_name }}</b><br>{{ $order->shipping_address }}
                        @if($order->has_exact_location)<br><a href="{{ $order->map_url }}" target="_blank"><i class="bi bi-geo-alt-fill"></i> Position exacte enregistrée</a>@endif
                        @if($order->delivery_note)<br><span class="text-muted">Note : {{ $order->delivery_note }}</span>@endif</div>
                    <div class="col-md-6"><div class="text-muted">Paiement</div><b><i class="bi bi-{{ $pm['icon'] }} text-accent me-1"></i>{{ $pm['label'] }}</b><br>
                        @if($order->payment_method === 'wave' && $wave)Numéro Wave : <b>{{ $wave }}</b>
                        @elseif($order->payment_method === 'orange_money' && $om)Numéro Orange Money : <b>{{ $om }}</b>
                        @else<span class="text-muted">À régler à la réception de la commande.</span>@endif</div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
        <a href="{{ $orders->count() === 1 ? route('orders.show', $orders->first()) : route('orders.index') }}" class="btn btn-primary btn-lg px-4"><i class="bi bi-box-seam me-1"></i>Suivre ma commande</a>
        <a href="{{ route('shop.index') }}" class="btn btn-soft btn-lg px-4">Continuer mes achats</a>
    </div>
</div>
@endsection
