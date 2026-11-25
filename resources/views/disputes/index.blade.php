@extends('layouts.dashboard')
@section('title', 'Litiges')

@section('content')
@php $me = auth()->user(); @endphp
<x-page-head title="Retours &amp; litiges" :subtitle="$me->isAdmin() ? 'Arbitrez les litiges transmis par les clients.' : ($me->isVendeur() ? 'Répondez aux signalements de vos clients.' : 'Suivez vos signalements de problèmes.')" />

<div class="chips mb-3">
    <a href="{{ route('disputes.index') }}" class="chip {{ request('status') ? '' : 'active' }}">Tous <em>{{ $counts->sum() }}</em></a>
    @foreach(\App\Models\Dispute::STATUSES as $k => $label)
        <a href="{{ route('disputes.index', ['status' => $k]) }}" class="chip {{ request('status') === $k ? 'active' : '' }}">{{ $label }} <em>{{ $counts->get($k, 0) }}</em></a>
    @endforeach
</div>

<div class="ui-card rise"><div class="table-responsive"><table class="table table-ui table-hover">
    <thead><tr><th>Commande</th><th>Problème</th>@unless($me->isClient())<th>Client</th>@endunless @unless($me->isVendeur())<th>Vendeur</th>@endunless<th>Statut</th><th>Ouvert le</th><th></th></tr></thead>
    <tbody>
    @forelse($disputes as $d)
        <tr>
            <td class="mono">{{ $d->order->order_number }}</td>
            <td>{{ $d->reason_label }}</td>
            @unless($me->isClient())<td><div class="d-flex align-items-center gap-2"><x-avatar :user="$d->client" size="xs" />{{ $d->client->name }}</div></td>@endunless
            @unless($me->isVendeur())<td>{{ $d->seller ? $d->seller->display_name : 'E-Shop SN' }}</td>@endunless
            <td><span class="pill {{ $d->pill_class }}">{{ $d->status_label }}</span></td>
            <td class="small text-muted">{{ $d->created_at->format('d/m/Y') }}</td>
            <td class="text-end"><a href="{{ route('disputes.show', $d) }}" class="btn btn-soft btn-sm">Ouvrir</a></td>
        </tr>
    @empty
        <tr><td colspan="7"><x-empty-state icon="shield-check" title="Aucun litige" text="Tout se passe bien : aucun problème signalé pour l'instant." /></td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-4 d-flex justify-content-center">{{ $disputes->links() }}</div>
@endsection
