@extends('layouts.dashboard')
@section('title', $user->name)

@section('content')
<x-page-head :title="$user->name" :subtitle="$user->email">
    <x-slot:actions>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Utilisateurs</a>
    </x-slot:actions>
</x-page-head>

<div class="row g-4">
    <div class="col-lg-4"><div class="ui-card rise"><div class="ui-card-body text-center">
        <div class="d-flex justify-content-center mb-3"><x-avatar :user="$user" size="xl" /></div>
        <h5 class="fw-bold mb-1">{{ $user->display_name }}</h5><x-role-badge :role="$user->role" />
        <hr>
        <div class="text-start small">
            <p class="mb-2"><i class="bi bi-telephone me-2 text-accent"></i>{{ $user->phone ?: '—' }}</p>
            <p class="mb-2"><i class="bi bi-geo-alt me-2 text-accent"></i>{{ $user->address ?: '—' }}</p>
            <p class="mb-0"><i class="bi bi-calendar3 me-2 text-accent"></i>Inscrit le {{ $user->created_at?->format('d/m/Y') }}</p>
        </div>
        @php $stClass = ['active' => 'pill-ok', 'pending' => 'pill-warn', 'suspended' => 'pill-off', 'rejected' => 'pill-bad'][$user->account_status] ?? 'pill-off'; @endphp
        <div class="mt-2"><span class="pill {{ $stClass }}">{{ $user->status_label }}</span></div>
        @if($creator)<div class="small text-muted mt-2">Créé par {{ $creator->name }}</div>@endif
        @if($user->canBeManagedBy(auth()->user()))
            <div class="d-grid gap-2 mt-3 text-start">
                @if($user->account_status === 'pending')
                    <form action="{{ route('admin.users.approve', $user) }}" method="POST">@csrf<button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i>Valider la boutique</button></form>
                    <form action="{{ route('admin.users.reject', $user) }}" method="POST" data-confirm="Refuser cette demande ?">@csrf<button class="btn btn-outline-danger w-100">Refuser</button></form>
                @endif
                @if(in_array($user->account_status, ['active', 'suspended']))
                    <form action="{{ route('admin.users.status', $user) }}" method="POST" data-confirm="Confirmer ?">@csrf
                        <button class="btn btn-ghost w-100"><i class="bi bi-{{ $user->account_status === 'active' ? 'slash-circle' : 'check-circle' }} me-1"></i>{{ $user->account_status === 'active' ? 'Désactiver le compte' : 'Réactiver le compte' }}</button></form>
                @endif
                <form action="{{ route('admin.users.reset', $user) }}" method="POST" data-confirm="Réinitialiser le mot de passe ?">@csrf<button class="btn btn-ghost w-100"><i class="bi bi-key me-1"></i>Réinitialiser le mot de passe</button></form>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" data-confirm="Supprimer définitivement ce compte ?">@csrf @method('DELETE')<button class="btn btn-outline-danger w-100"><i class="bi bi-trash3 me-1"></i>Supprimer le compte</button></form>
            </div>
        @endif
        @if($user->isVendeur())
            <div class="row g-2 mt-3"><div class="col-6"><div class="p-2 rounded-3" style="background:var(--accent-soft)"><b>{{ $productsCount }}</b><div class="small text-muted">produits</div></div></div>
                <div class="col-6"><div class="p-2 rounded-3" style="background:var(--accent-soft)"><b>{{ \App\Support\Stats::money($revenue) }}</b><div class="small text-muted">CA</div></div></div></div>
        @endif
    </div></div></div>

    <div class="col-lg-8"><div class="ui-card rise">
        <div class="ui-card-head">{{ $user->isVendeur() ? 'Commandes de la boutique' : ($user->isLivreur() ? 'Livraisons' : 'Commandes') }}</div>
        <div class="table-responsive"><table class="table table-ui">
            <thead><tr><th>N°</th><th>Date</th><th>Total</th><th>Statut</th></tr></thead>
            <tbody>
            @forelse($orders as $o)
                <tr><td><a href="{{ route('admin.orders.show', $o) }}" class="mono">{{ $o->order_number }}</a></td><td>{{ $o->created_at->format('d/m/Y') }}</td><td class="fw-bold">{{ $o->formatted_total }}</td><td><x-status-badge :status="$o->status" /></td></tr>
            @empty<tr><td colspan="4"><x-empty-state icon="inbox" title="Aucune commande" /></td></tr>@endforelse
            </tbody>
        </table></div>
    </div></div>
</div>
@endsection
