@extends('layouts.dashboard')
@section('title', 'Nouvelle catégorie')

@section('content')
@php $cat = new \App\Models\Category; @endphp
<x-page-head title="Nouvelle catégorie">
    <x-slot:actions><a href="{{ route('admin.categories.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Retour</a></x-slot:actions>
</x-page-head>
<div class="row"><div class="col-lg-6">
<form method="POST" action="{{ route('admin.categories.store') }}" class="ui-card rise">
    @csrf 
    <div class="ui-card-body">
        <div class="mb-3"><label class="form-label">Nom</label>
            <input type="text" name="name" value="{{ old('name', $cat->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control">{{ old('description', $cat->description) }}</textarea></div>
        <button class="btn btn-primary px-4">Enregistrer</button>
    </div>
</form>
</div></div>
@endsection
