@props(['order'])
@php
    $cancelled = $order->status === 'cancelled';
    $labels = ['pending' => ['En attente', 'hourglass-split'], 'confirmed' => ['Confirmée', 'check2-circle'], 'assigned' => ['Livreur assigné', 'person-check'], 'delivering' => ['En route', 'truck'], 'delivered' => ['Livrée', 'bag-check']];
    $idx = $order->flow_index;
@endphp
@if($order->status === 'problem')
    <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:rgba(249,115,22,.12);color:#ea580c;font-weight:700"><i class="bi bi-exclamation-triangle-fill fs-5"></i> Un incident de livraison a été signalé{{ $order->incident_label ? ' : '.$order->incident_label : '' }}. Le vendeur organise une nouvelle livraison.</div>
@elseif($cancelled)
    <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:rgba(239,68,68,.12);color:#ef4444;font-weight:700"><i class="bi bi-x-circle-fill fs-5"></i> Cette commande a été annulée.</div>
@else
    <div class="steps">
        @foreach(\App\Models\Order::FLOW as $i => $st)
            <div class="step {{ $i < $idx ? 'done' : ($i === $idx ? ($st === 'delivered' ? 'done' : 'now') : '') }}">
                <i class="bi bi-{{ ($i < $idx || $st === 'delivered' && $i === $idx) ? 'check-lg' : $labels[$st][1] }}"></i>{{ $labels[$st][0] }}
            </div>
        @endforeach
    </div>
@endif
