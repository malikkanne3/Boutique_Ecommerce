@extends('layouts.dashboard')
@section('title', 'Catégories')

@section('content')
<x-page-head title="Catégories" subtitle="Classement des produits de toute la plateforme.">
    <x-slot:actions><a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouvelle catégorie</a></x-slot:actions>
</x-page-head>

<div class="ui-card rise"><div class="table-responsive"><table class="table table-ui table-hover">
    <thead><tr><th>Nom</th><th>Description</th><th>Produits</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    @forelse($categories as $c)
        <tr>
            <td class="fw-bold">{{ $c->name }}<div class="small text-muted mono">{{ $c->slug }}</div></td>
            <td class="text-muted small">{{ \Illuminate\Support\Str::limit($c->description, 70) ?: '—' }}</td>
            <td><span class="pill pill-role-client">{{ $c->products_count }}</span></td>
            <td class="text-end text-nowrap">
                <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-soft btn-sm"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" class="d-inline" data-confirm="Supprimer la catégorie « {{ $c->name }} » ?">@csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash3"></i></button></form>
            </td>
        </tr>
    @empty<tr><td colspan="4"><x-empty-state icon="tags" title="Aucune catégorie" /></td></tr>@endforelse
    </tbody>
</table></div></div>
<div class="mt-4 d-flex justify-content-center">{{ $categories->links() }}</div>
@endsection
