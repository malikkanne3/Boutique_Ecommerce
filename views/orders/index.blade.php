@extends('layouts.app')
@section('title', 'Mes commandes')

@section('content')
<div class="container">
    <h1 class="fw-bold mb-4">Mes commandes</h1>

    @if($orders->count())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>N° Commande</th>
                                <th>Date</th>
                                <th>Produits</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->order_number }}</td>
                                <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-muted small">{{ $order->products->count() }} article(s)</td>
                                <td class="fw-bold">{{ $order->formatted_total }}</td>
                                <td>
                                    <span class="badge badge-status badge-status-{{ $order->status }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Détail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-box-seam" style="font-size:4rem;color:#cbd5e1"></i>
            <p class="mt-3 text-muted fs-5">Vous n'avez encore passé aucune commande.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary px-5">Aller à la boutique</a>
        </div>
    @endif
</div>
@endsection
