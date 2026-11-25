@extends('layouts.dashboard')
@section('title', 'Avis')

@section('content')
<x-page-head title="Modération des avis" subtitle="Masquez ou supprimez les avis abusifs." />

<div class="chips mb-3">
    <a href="{{ route('admin.reviews.index') }}" class="chip {{ request()->query() ? '' : 'active' }}">Tous</a>
    <a href="{{ route('admin.reviews.index', ['state' => 'hidden']) }}" class="chip {{ request('state') === 'hidden' ? 'active' : '' }}"><i class="bi bi-eye-slash"></i> Masqués <em>{{ $hiddenCount }}</em></a>
    @foreach([1, 2, 3, 4, 5] as $n)<a href="{{ route('admin.reviews.index', ['rating' => $n]) }}" class="chip {{ (int) request('rating') === $n ? 'active' : '' }}">{{ $n }} <i class="bi bi-star-fill"></i></a>@endforeach
</div>

<div class="ui-card rise"><div class="table-responsive"><table class="table table-ui table-hover">
    <thead><tr><th>Auteur</th><th>Concerne</th><th>Note</th><th>Commentaire</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    @forelse($reviews as $r)
        <tr>
            <td><div class="d-flex align-items-center gap-2"><x-avatar :user="$r->user" size="xs" /><div><b class="small">{{ $r->user ? $r->user->name : 'Supprimé' }}</b><div class="small text-muted">{{ $r->created_at->format('d/m/Y') }}</div></div></div></td>
            <td class="small">@if($r->product)<i class="bi bi-box-seam me-1"></i>{{ \Illuminate\Support\Str::limit($r->product->name, 34) }}@else<i class="bi bi-shop me-1"></i>Vendeur : {{ $r->seller ? $r->seller->display_name : '—' }}@endif</td>
            <td><x-stars :value="$r->rating" size=".85rem" /></td>
            <td class="small" style="max-width:320px">{{ $r->comment ? \Illuminate\Support\Str::limit($r->comment, 140) : '—' }}@if($r->reply)<div class="text-muted mt-1"><i class="bi bi-reply"></i> {{ \Illuminate\Support\Str::limit($r->reply, 80) }}</div>@endif</td>
            <td><span class="pill {{ $r->is_hidden ? 'pill-off' : 'pill-ok' }}">{{ $r->is_hidden ? 'Masqué' : 'Visible' }}</span></td>
            <td class="text-end text-nowrap">
                <form action="{{ route('admin.reviews.toggle', $r) }}" method="POST" class="d-inline">@csrf @method('PATCH')<button class="btn btn-ghost btn-sm" title="{{ $r->is_hidden ? 'Afficher' : 'Masquer' }}"><i class="bi bi-{{ $r->is_hidden ? 'eye' : 'eye-slash' }}"></i></button></form>
                <form action="{{ route('admin.reviews.destroy', $r) }}" method="POST" class="d-inline" data-confirm="Supprimer cet avis définitivement ?">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash3"></i></button></form>
            </td>
        </tr>
    @empty<tr><td colspan="6"><x-empty-state icon="star" title="Aucun avis" text="Les avis des clients apparaîtront ici." /></td></tr>@endforelse
    </tbody>
</table></div></div>
<div class="mt-4 d-flex justify-content-center">{{ $reviews->links() }}</div>
@endsection
