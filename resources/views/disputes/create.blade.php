@extends('layouts.dashboard')
@section('title', 'Signaler un problème')

@section('content')
<x-page-head :title="'Signaler un problème — '.$order->order_number" subtitle="Le vendeur sera prévenu et vous répondra. Vous pourrez demander l'arbitrage de l'administration si besoin.">
    <x-slot:actions><a href="{{ route('orders.show', $order) }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Retour à la commande</a></x-slot:actions>
</x-page-head>

<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('disputes.store', $order) }}" enctype="multipart/form-data" class="ui-card rise">
            @csrf
            <div class="ui-card-body">
                <label class="form-label">Quel est le problème ?</label>
                <div class="chips mb-3">
                    @foreach(\App\Models\Dispute::REASONS as $k => $l)
                        <label class="chip" style="cursor:pointer"><input type="radio" name="reason" value="{{ $k }}" class="form-check-input me-1" @checked(old('reason') === $k) required>{{ $l }}</label>
                    @endforeach
                </div>
                @error('reason')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                <div class="mb-3"><label class="form-label">Décrivez ce qui s'est passé</label>
                    <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Ex : le produit est arrivé avec l'écran fissuré…" required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

                <div class="mb-4"><label class="form-label">Photo ou document <small class="text-muted">(facultatif, 10 Mo max)</small></label>
                    <input type="file" name="file" accept="image/*,.pdf" class="form-control @error('file') is-invalid @enderror">
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

                <button class="btn btn-primary btn-lg"><i class="bi bi-send me-1"></i>Envoyer le signalement</button>
            </div>
        </form>
    </div>
    <div class="col-lg-5">
        <div class="ui-card rise"><div class="ui-card-head">Commande concernée</div>
            @foreach($order->products as $p)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom"><img class="thumb" src="{{ $p->image_url }}" alt=""><div class="small"><b>{{ $p->name }}</b><div class="text-muted">{{ $p->pivot->quantity }} × {{ number_format($p->pivot->price, 0, ',', ' ') }} FCFA</div></div></div>
            @endforeach
            <div class="p-3 small text-muted"><i class="bi bi-info-circle me-1"></i>Vous avez {{ (int) config('shop.dispute_days', 14) }} jours après la livraison pour signaler un problème.</div>
        </div>
    </div>
</div>
@endsection
