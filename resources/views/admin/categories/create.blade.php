@extends('layouts.admin')
@section('title', isset($category) ? 'Modifier la catégorie' : 'Nouvelle catégorie')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
    <h5 class="fw-bold mb-0">{{ isset($category) ? 'Modifier : ' . $category->name : 'Nouvelle catégorie' }}</h5>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($category)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label">Nom de la catégorie *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $category->name ?? '') }}" required
                               placeholder="Ex: Électronique">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Le slug sera généré automatiquement.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Description de la catégorie...">{{ old('description', $category->description ?? '') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            {{ isset($category) ? 'Mettre à jour' : 'Créer la catégorie' }}
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
