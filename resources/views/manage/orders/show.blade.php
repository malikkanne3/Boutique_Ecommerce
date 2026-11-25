@extends('layouts.dashboard')
@section('title', 'Commande ' . $order->order_number)

@section('content')
<x-page-head :title="'Commande '.$order->order_number" :subtitle="'Reçue le '.$order->created_at->format('d/m/Y à H:i')">
    <x-slot:actions>
        <a href="{{ route($prefix.'.orders.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Commandes</a>
        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="btn btn-ghost"><i class="bi bi-file-earmark-text me-1"></i>Facture</a>
        <a href="{{ route('orders.delivery_note', $order) }}" target="_blank" class="btn btn-ghost"><i class="bi bi-truck me-1"></i>Bon de livraison</a>
    </x-slot:actions>
</x-page-head>

<div class="ui-card mb-4 rise"><div class="ui-card-body">
    <div class="d-flex justify-content-between align-items-center mb-3"><x-status-badge :status="$order->status" /><b class="fs-5">{{ $order->formatted_total }}</b></div>
    <x-order-steps :order="$order" />
</div></div>

@if($order->status === 'problem')
    <div class="ui-card mb-4 rise" style="border-color:#f97316"><div class="ui-card-body d-flex gap-3">
        <i class="bi bi-exclamation-triangle-fill fs-2" style="color:#f97316"></i>
        <div><b>Incident de livraison : {{ $order->incident_label }}</b>
            <div class="small text-muted">Signalé le {{ $order->incident_at?->format('d/m/Y à H:i') }}@if($order->livreur) par {{ $order->livreur->name }}@endif</div>
            @if($order->incident_note)<p class="mb-1 mt-2">« {{ $order->incident_note }} »</p>@endif
            <div class="small mt-2">Contactez le client puis <b>réassignez un livreur</b>, ou annulez la commande.</div></div>
    </div></div>
@endif

@if($order->status === 'delivered' && $order->proof_path)
    <div class="ui-card mb-4 rise"><div class="ui-card-body d-flex gap-3 align-items-center">
        <a href="{{ route('orders.proof', $order) }}" target="_blank"><img src="{{ route('orders.proof', $order) }}" alt="Preuve" style="width:90px;height:90px;object-fit:cover;border-radius:14px"></a>
        <div><b><i class="bi bi-camera-fill text-accent me-1"></i>Preuve de livraison (photo)</b><div class="small text-muted">Livré le {{ $order->delivered_at?->format('d/m/Y à H:i') }} par {{ $order->livreur?->name }}</div></div>
    </div></div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ui-card mb-4 rise">
            <div class="ui-card-head">Articles ({{ $order->products->count() }})</div>
            @foreach($order->products as $p)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <img class="thumb" style="width:56px;height:56px" src="{{ $p->image_url }}" alt="">
                    <div class="flex-grow-1"><b>{{ $p->name }}</b><div class="small text-muted">@if($p->pivot->size)<span class="var-chip">{{ $p->pivot->size }}</span>@endif @if($p->pivot->color)<span class="var-chip">{{ $p->pivot->color }}</span>@endif {{ $p->category?->name }} · {{ $p->pivot->quantity }} × {{ number_format($p->pivot->price, 0, ',', ' ') }} FCFA</div></div>
                    <b>{{ number_format($p->pivot->price * $p->pivot->quantity, 0, ',', ' ') }} FCFA</b>
                </div>
            @endforeach
        </div>

        <div class="ui-card rise">
            <div class="ui-card-head">Client &amp; livraison</div>
            <div class="ui-card-body row g-3">
                <div class="col-md-6"><small class="text-muted d-block">Client</small><div class="d-flex align-items-center gap-2"><x-avatar :user="$order->user" size="sm" /><div><b>{{ $order->customer_name }}</b><div class="small">{{ $order->customer_email }}</div></div></div></div>
                <div class="col-md-6"><small class="text-muted d-block">Téléphone</small><a href="tel:{{ $order->customer_phone }}" class="fw-bold">{{ $order->customer_phone }}</a></div>
                <div class="col-12"><small class="text-muted d-block">Adresse de livraison</small><b>{{ $order->shipping_address }}</b>
                    <a href="{{ $order->map_url }}" target="_blank" class="small ms-2"><i class="bi bi-map"></i> {{ $order->has_exact_location ? 'Position exacte (GPS)' : 'Voir sur la carte' }}</a>
                    @if($order->delivery_note)<div class="small text-muted mt-1"><i class="bi bi-sticky me-1"></i>{{ $order->delivery_note }}</div>@endif</div>
                <div class="col-md-6"><small class="text-muted d-block">Paiement</small><b>{{ $order->payment_label }}</b> <span class="pill {{ $order->payment_status === 'paid' ? 'pill-ok' : 'pill-warn' }}">{{ $order->payment_status === 'paid' ? 'Payé' : 'À encaisser' }}</span></div>
                @if($order->has_exact_location)<div class="col-12"><iframe class="map-frame" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox={{ (float) $order->delivery_lng - 0.004 }},{{ (float) $order->delivery_lat - 0.0025 }},{{ (float) $order->delivery_lng + 0.004 }},{{ (float) $order->delivery_lat + 0.0025 }}&layer=mapnik&marker={{ $order->delivery_lat }},{{ $order->delivery_lng }}"></iframe></div>@endif
                @if($order->user)<div class="col-12"><a href="{{ route('messages.conversation', $order->user) }}" class="btn btn-soft btn-sm"><i class="bi bi-chat-dots me-1"></i>Écrire au client</a></div>@endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ui-card rise mb-4">
            <div class="ui-card-head">Actions</div>
            <div class="ui-card-body d-grid gap-3">
                @if($order->status === 'pending')
                    <form action="{{ route($prefix.'.orders.update', $order) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="action" value="confirm">
                        <button class="btn btn-primary btn-lg w-100"><i class="bi bi-check2-circle me-1"></i>Confirmer la commande</button></form>
                @endif

                @if(in_array($order->status, ['confirmed', 'assigned', 'problem']))
                    <form action="{{ route($prefix.'.orders.update', $order) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="action" value="assign">
                        <label class="form-label">{{ $order->status === 'assigned' ? 'Changer de livreur' : 'Assigner un livreur' }}</label>
                        <select name="livreur_id" class="form-select mb-2" required>
                            <option value="">Choisir un livreur…</option>
                            @foreach($livreurs as $l)<option value="{{ $l->id }}" @selected($order->livreur_id === $l->id) @disabled(! $l->is_available)>{{ $l->name }} — {{ $l->active_count }} mission(s) en cours{{ $l->is_available ? '' : ' (indisponible)' }}</option>@endforeach
                        </select>
                        <button class="btn btn-primary w-100"><i class="bi bi-truck me-1"></i>Assigner</button>
                        @if($livreurs->isEmpty())<small class="text-muted d-block mt-2">Aucun livreur disponible. Demandez à l'admin d'en créer un.</small>@endif
                    </form>
                @endif

                @if($order->livreur)
                    <div class="p-3 rounded-3" style="background:var(--accent-soft)"><small class="text-muted d-block">Livreur</small><b>{{ $order->livreur->name }}</b>
                        <a href="{{ route('messages.conversation', $order->livreur) }}" class="small d-block mt-1"><i class="bi bi-chat-dots"></i> Envoyer un message</a></div>
                @endif

                @if(in_array($order->status, ['pending', 'confirmed', 'assigned', 'problem']))
                    <form action="{{ route($prefix.'.orders.update', $order) }}" method="POST" data-confirm="Annuler cette commande ? Le stock sera remis.">@csrf @method('PUT')<input type="hidden" name="action" value="cancel">
                        <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle me-1"></i>Annuler la commande</button></form>
                @endif

                @if(in_array($order->status, ['delivered', 'cancelled']))<p class="text-muted small mb-0 text-center">Cette commande est terminée.</p>@endif
            </div>
        </div>
 @if($prefix === 'vendeur' && $siblings->isNotEmpty())
            <div class="ui-card rise mb-4">
                <div class="ui-card-head"><span><i class="bi bi-people text-accent me-1"></i>Autres vendeurs de ce panier</span><small>même client</small></div>
                @foreach($siblings as $sib)
                    <div class="p-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-2"><x-avatar :user="$sib->seller" size="sm" /><b class="small flex-grow-1">{{ $sib->seller->display_name }}</b>
                            @if(auth()->user()->canMessage($sib->seller))<a href="{{ route('messages.conversation', $sib->seller) }}" class="btn btn-soft btn-sm"><i class="bi bi-chat-dots me-1"></i>Discuter</a>@endif</div>
                        @foreach($sib->products as $sp)
                            <div class="d-flex align-items-center gap-2 small text-muted mb-1"><img class="thumb" style="width:30px;height:30px" src="{{ $sp->image_url }}" alt="">{{ $sp->name }} × {{ $sp->pivot->quantity }}</div>
                        @endforeach
                    </div>
                @endforeach
                <div class="p-3 small text-muted"><i class="bi bi-info-circle me-1"></i>Ces articles du même panier sont livrés séparément : coordonnez-vous avec l'autre vendeur.</div>
            </div>
        @endif
        @if($prefix === 'vendeur')
            <form action="{{ route('vendeur.orders.note', $order) }}" method="POST" class="ui-card rise mb-4">@csrf @method('PATCH')
                <div class="ui-card-head"><span><i class="bi bi-lock-fill text-accent me-1"></i>Notes internes</span><small>visibles uniquement par vous</small></div>
                <div class="ui-card-body">
                    <textarea name="seller_note" rows="4" maxlength="1000" class="form-control mb-2" placeholder="Ex : client à rappeler après 17h, emballage cadeau…">{{ old('seller_note', $order->seller_note) }}</textarea>
                    <button class="btn btn-soft btn-sm w-100">Enregistrer la note</button>
                </div>
            </form>
        @endif
        @if($prefix === 'admin')<div class="ui-card rise"><div class="ui-card-body small"><span class="text-muted">Vendeur :</span> <b>{{ $order->seller_name }}</b></div></div>@endif
    </div>
</div>
@endsection
