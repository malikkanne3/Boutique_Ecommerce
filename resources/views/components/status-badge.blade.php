@props(['status'])
@php
    $icons = ['pending' => 'hourglass-split', 'confirmed' => 'check2-circle', 'assigned' => 'person-check', 'delivering' => 'truck', 'delivered' => 'bag-check', 'cancelled' => 'x-circle', 'problem' => 'exclamation-triangle'];
@endphp
<span class="pill pill-{{ $status }}"><i class="bi bi-{{ $icons[$status] ?? 'circle' }}"></i>{{ \App\Models\Order::STATUSES[$status] ?? $status }}</span>
