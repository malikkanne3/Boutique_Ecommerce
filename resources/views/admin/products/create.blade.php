@extends('layouts.admin')
@section('title', isset($product) ? 'Modifier le produit' : 'Nouveau produit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
        <h5 class="fw-bold mb-0">{{ isset($product) ? 'Modifier : ' . $product->name : 'Nouveau produit' }}</h5>
    </div>
</div>

<form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($product)) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Informations générales</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nom du produit *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name ?? '') }}" required placeholder="Ex: iPhone 15 Pro">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4" placeholder="Description détaillée du produit...">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Prix (FCFA) *</label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', $product->price ?? '') }}" required min="0" step="0.01" placeholder="Ex: 25000">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock *</label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', $product->stock ?? 0) }}" required min="0" placeholder="Ex: 50">
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Image du produit</div>
                <div class="card-body">
                    @if(isset($product) && $product->image)
                    <div class="mb-3">
                        <img src="{{ $product->image_url }}"
                             style="height:160px;width:200px;object-fit:cover;border-radius:12px;border:2px solid var(--border)"
                             alt="{{ $product->name }}" id="currentImg">
                        <div class="text-muted small mt-1">Image actuelle</div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">URL d'une image (recommandé)</label>
                        <input type="url" name="image_url" class="form-control"
                               value="{{ old('image_url', (isset($product) && filter_var($product->image, FILTER_VALIDATE_URL)) ? $product->image : '') }}"
                               placeholder="https://images.unsplash.com/...">
                        <div class="form-text">Collez l'URL d'une image depuis Unsplash, Google Images, etc.</div>
                    </div>

                    <div class="text-center text-muted my-2" style="font-size:.82rem">— OU —</div>

                    <div>
                        <label class="form-label">Télécharger une image</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                               accept="image/*" id="imageFile">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div id="imgPreview" class="mt-3 d-none">
                        <img id="previewImg" style="height:140px;border-radius:12px;border:2px solid var(--border)" alt="Aperçu">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Organisation</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Catégorie *</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">— Choisir —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                               {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isActive">Produit actif (visible)</label>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-lg me-1"></i>
                        {{ isset($product) ? 'Mettre à jour' : 'Créer le produit' }}
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">Annuler</a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('imageFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        document.getElementById('previewImg').src = ev.target.result;
        document.getElementById('imgPreview').classList.remove('d-none');
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
@endsection
