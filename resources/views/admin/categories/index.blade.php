@extends('layouts.admin')
@section('title', 'Catégories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Gestion des catégories</h5>
        <p class="text-muted mb-0" style="font-size:.85rem">{{ $categories->total() }} catégorie(s)</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouvelle catégorie
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Produits</th>
                    <th>Créée le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td class="fw-semibold">{{ $category->name }}</td>
                    <td><code style="font-size:.78rem;background:#f1f5f9;padding:2px 8px;border-radius:6px">{{ $category->slug }}</code></td>
                    <td class="text-muted" style="font-size:.85rem">{{ Str::limit($category->description, 50) ?? '—' }}</td>
                    <td>
                        <span class="badge" style="background:#ede9fe;color:#7c3aed;border-radius:8px">
                            {{ $category->products_count }} produit(s)
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.82rem">{{ $category->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cette catégorie ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-tag fs-1 d-block mb-2 opacity-25"></i>
                        Aucune catégorie
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white d-flex justify-content-center py-3">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
