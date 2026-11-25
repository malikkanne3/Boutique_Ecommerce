@extends('layouts.dashboard')
@section('title', 'Historique & gains')

@section('content')
<x-page-head title="Historique &amp; gains" subtitle="Vos livraisons terminées et ce qu'elles vous ont rapporté." />

<div class="row g-2 mb-4">
    @foreach([['sun', "Aujourd'hui", $earn['today'], ''], ['calendar-week', 'Cette semaine', $earn['week'], ''], ['calendar-month', 'Ce mois', $earn['month'], ''], ['wallet2', 'Total', $earn['total'], 'ok']] as [$ic, $lb, $val, $tone])
        <div class="col-6 col-md-3">
            <div class="ui-card p-3 text-center rise h-100"><div class="stat-ico {{ $tone }} mx-auto mb-2" style="width:42px;height:42px;font-size:1.2rem"><i class="bi bi-{{ $ic }}"></i></div>
                <div class="stat-value" style="font-size:1.2rem">{{ number_format($val, 0, ',', ' ') }} <small style="font-size:.6em">FCFA</small></div><div class="stat-label" style="font-size:.66rem">{{ $lb }}</div></div>
        </div>
    @endforeach
</div>

<div class="ui-card mb-4 rise">
    <div class="ui-card-head"><span><i class="bi bi-bar-chart-line text-accent me-2"></i>Récapitulatif hebdomadaire</span><small>{{ $earn['count'] }} livraison(s) au total · {{ number_format((int) config('shop.courier_fee', 1000), 0, ',', ' ') }} FCFA par course</small></div>
    <div class="table-responsive"><table class="table table-ui">
        <thead><tr><th>Semaine</th><th class="text-end">Courses</th><th class="text-end">Gains</th></tr></thead>
        <tbody>
        @php $max = max(1, collect($weeks)->max('sum')); @endphp
        @foreach($weeks as $w)
            <tr><td>{{ $w['label'] }}<div class="progress-thin mt-1" style="max-width:220px"><span style="width:{{ round($w['sum'] / $max * 100) }}%"></span></div></td>
                <td class="text-end">{{ $w['count'] }}</td><td class="text-end fw-bold">{{ number_format($w['sum'], 0, ',', ' ') }} FCFA</td></tr>
        @endforeach
        </tbody>
    </table></div>
</div>

<h5 class="fw-bold mb-3">Livraisons terminées</h5>
@forelse($orders as $order)
    @include('livreur.partials.mission', ['order' => $order, 'mode' => 'done'])
@empty
    <div class="ui-card"><x-empty-state icon="clock-history" title="Aucune livraison terminée" text="Vos livraisons terminées apparaîtront ici." /></div>
@endforelse
<div class="mt-3 d-flex justify-content-center">{{ $orders->links() }}</div>
@endsection
