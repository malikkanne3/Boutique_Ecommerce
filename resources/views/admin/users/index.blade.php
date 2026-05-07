@extends('layouts.admin')
@section('title', 'Utilisateurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Gestion des utilisateurs</h5>
        <p class="text-muted mb-0" style="font-size:.85rem">{{ $users->total() }} utilisateur(s)</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-warning fw-bold">
        <i class="bi bi-person-plus me-2"></i>Nouveau livreur
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control" style="max-width:250px"
                   placeholder="Nom ou email..." value="{{ request('search') }}">
            <select name="role" class="form-select" style="max-width:180px">
                <option value="">Tous les rôles</option>
                <option value="admin"   {{ request('role')=='admin'   ? 'selected' : '' }}>Admin</option>
                <option value="client"  {{ request('role')=='client'  ? 'selected' : '' }}>Client</option>
                <option value="livreur" {{ request('role')=='livreur' ? 'selected' : '' }}>Livreur</option>
            </select>
            <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filtrer</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Commandes</th>
                    <th>Inscrit le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;flex-shrink:0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:.88rem">{{ $user->name }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->role === 'admin')
                            <span class="badge" style="background:#fee2e2;color:#991b1b;border-radius:8px">Admin</span>
                        @elseif($user->role === 'livreur')
                            <span class="badge" style="background:#fef9c3;color:#92400e;border-radius:8px">🚚 Livreur</span>
                        @else
                            <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:8px">Client</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="background:#ede9fe;color:#7c3aed;border-radius:8px">
                            {{ $user->orders_count }} commande(s)
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.82rem">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye me-1"></i>Voir
                        </a>
                        @if($user->role !== 'admin')
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                              onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        Aucun utilisateur trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white d-flex justify-content-center py-3">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection