@extends('layouts.admin')
@section('title', 'Produits')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Gestion des produits</h5>
        <p class="text-muted mb-0" style="font-size:.85rem">{{ $products->total() }} produit(s) au total</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau produit
    </a>
</div>

{{-- FILTRES --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control" style="max-width:260px"
                   placeholder="Rechercher un produit..." value="{{ request('search') }}">
            <select name="category" class="form-select" style="max-width:180px">
                <option value="">Toutes catégories</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select" style="max-width:150px">
                <option value="">Tous statuts</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
            </select>
            <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filtrer</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $product->image_url }}"
                                     style="width:48px;height:48px;object-fit:cover;border-radius:10px;flex-shrink:0"
                                     alt="{{ $product->name }}">
                                <div>
                                    <div class="fw-semibold" style="font-size:.88rem">{{ Str::limit($product->name, 35) }}</div>
                                    <div class="text-muted" style="font-size:.75rem">{{ Str::limit($product->description, 45) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background:#ede9fe;color:#7c3aed;border-radius:8px;font-size:.75rem">
                                {{ $product->category->name ?? '—' }}
                            </span>
                        </td>
                        <td class="fw-bold" style="font-size:.88rem">{{ $product->formatted_price }}</td>
                        <td>
                            @if($product->stock == 0)
                                <span class="badge bg-danger">Rupture</span>
                            @elseif($product->stock <= 5)
                                <span class="badge bg-warning text-dark">{{ $product->stock }} restants</span>
                            @else
                                <span class="badge bg-success">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td>
                            @if($product->trashed())
                                <span class="badge bg-secondary">Supprimé</span>
                            @elseif($product->is_active)
                                <span class="badge" style="background:#d1fae5;color:#065f46;border-radius:8px">Actif</span>
                            @else
                                <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:8px">Inactif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$product->trashed())
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce produit ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                            Aucun produit trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-white d-flex justify-content-center py-3">
        {{ $products->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
