@extends('layouts.dashboard')
@section('title', 'Utilisateurs')

@section('content')
<x-page-head title="Utilisateurs" subtitle="Clients, vendeurs, livreurs et administrateurs.">
    <x-slot:actions><a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Créer un compte</a></x-slot:actions>
</x-page-head>

@if(session('generated_password'))
    @php $gp = session('generated_password'); @endphp
    <div class="alert alert-warning rise">
        <div class="d-flex gap-3 align-items-start">
            <i class="bi bi-key-fill fs-4"></i>
            <div class="flex-grow-1">
                <b>Identifiants provisoires — affichés une seule fois, notez-les maintenant.</b><br>
                Email : <span class="mono">{{ $gp['email'] }}</span> · Mot de passe : <span class="mono">{{ $gp['password'] }}</span><br>
                <small>Lien de connexion : <span class="mono">{{ $gp['link'] }}</span><br>L'utilisateur devra choisir son propre mot de passe à la première connexion. Vous ne pourrez plus le revoir ensuite (seulement le réinitialiser).</small>
            </div>
        </div>
    </div>
@endif

<div class="chips mb-3">
    @foreach(['' => ['Tous', 'all'], 'client' => ['Clients', 'client'], 'vendeur' => ['Vendeurs', 'vendeur'], 'livreur' => ['Livreurs', 'livreur'], 'admin' => ['Admins', 'admin']] as $key => [$label, $ck])
        <a href="{{ route('admin.users.index', $key ? ['role' => $key] : []) }}" class="chip {{ ! request('status') && request('role', '') === (string) $key ? 'active' : '' }}">{{ $label }} <em>{{ $roleCounts[$ck] }}</em></a>
    @endforeach
    <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="chip {{ request('status') === 'pending' ? 'active' : '' }}"><i class="bi bi-hourglass-split"></i> À valider <em>{{ $pendingCount }}</em></a>
    <a href="{{ route('admin.users.index', ['status' => 'suspended']) }}" class="chip {{ request('status') === 'suspended' ? 'active' : '' }}"><i class="bi bi-slash-circle"></i> Désactivés</a>
</div>

<form method="GET" class="ui-card ui-card-body mb-3 row g-2 mx-0 rise" data-no-loading>
    @if(request('role'))<input type="hidden" name="role" value="{{ request('role') }}">@endif
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <div class="col-md-10"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nom, email ou boutique…"></div>
    <div class="col-md-2"><button class="btn btn-soft w-100"><i class="bi bi-search me-1"></i>Rechercher</button></div>
</form>

<div class="ui-card rise"><div class="table-responsive"><table class="table table-ui table-hover">
    <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Statut</th><th>Commandes</th><th>Inscrit le</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    @forelse($users as $u)
        @php
            $st = $u->account_status;
            $stClass = ['active' => 'pill-ok', 'pending' => 'pill-warn', 'suspended' => 'pill-off', 'rejected' => 'pill-bad'][$st] ?? 'pill-off';
            $can = $u->canBeManagedBy(auth()->user());
        @endphp
        <tr>
            <td><div class="d-flex align-items-center gap-3"><x-avatar :user="$u" size="sm" />
                <div><a href="{{ route('admin.users.show', $u) }}" class="fw-bold text-reset">{{ $u->name }}</a>@if($u->isVendeur() && $u->shop_name)<small class="text-muted"> · {{ $u->shop_name }}</small>@endif
                <div class="small text-muted">{{ $u->email }}</div></div></div></td>
            <td><x-role-badge :role="$u->role" /></td>
            <td><span class="pill {{ $stClass }}">{{ $u->status_label }}</span>@if($u->must_change_password)<div class="small text-muted mt-1"><i class="bi bi-key"></i> mdp provisoire</div>@endif</td>
            <td>{{ $u->orders_count }}</td>
            <td class="small text-muted">{{ $u->created_at?->format('d/m/Y') }}</td>
            <td class="text-end text-nowrap">
                @if($st === 'pending')
                    <form action="{{ route('admin.users.approve', $u) }}" method="POST" class="d-inline">@csrf<button class="btn btn-primary btn-sm"><i class="bi bi-check2"></i> Valider</button></form>
                    <form action="{{ route('admin.users.reject', $u) }}" method="POST" class="d-inline" data-confirm="Refuser la demande de {{ $u->name }} ?">@csrf<button class="btn btn-outline-danger btn-sm">Refuser</button></form>
                @endif
                <a href="{{ route('admin.users.show', $u) }}" class="btn btn-soft btn-sm" title="Détails"><i class="bi bi-eye"></i></a>
                @if($can)
                    @if(in_array($st, ['active', 'suspended']))
                        <form action="{{ route('admin.users.status', $u) }}" method="POST" class="d-inline" data-confirm="{{ $st === 'active' ? 'Désactiver le compte de '.$u->name.' ? Il sera déconnecté immédiatement.' : 'Réactiver le compte de '.$u->name.' ?' }}">@csrf
                            <button class="btn btn-ghost btn-sm" title="{{ $st === 'active' ? 'Désactiver' : 'Réactiver' }}"><i class="bi bi-{{ $st === 'active' ? 'slash-circle' : 'check-circle' }}"></i></button></form>
                    @endif
                    <form action="{{ route('admin.users.reset', $u) }}" method="POST" class="d-inline" data-confirm="Réinitialiser le mot de passe de {{ $u->name }} ?">@csrf
                        <button class="btn btn-ghost btn-sm" title="Réinitialiser le mot de passe"><i class="bi bi-key"></i></button></form>
                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" data-confirm="Supprimer définitivement le compte de {{ $u->name }} ? Ses données associées seront perdues (préférez Désactiver).">@csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" title="Supprimer"><i class="bi bi-trash3"></i></button></form>
                @endif
            </td>
        </tr>
    @empty<tr><td colspan="6"><x-empty-state icon="people" title="Aucun utilisateur trouvé" /></td></tr>@endforelse
    </tbody>
</table></div></div>
<div class="mt-4 d-flex justify-content-center">{{ $users->links() }}</div>
@endsection
