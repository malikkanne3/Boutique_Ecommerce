@extends('layouts.dashboard')
@section('title', 'Nouveau groupe')

@section('content')
<x-page-head title="Nouveau groupe" subtitle="Réunissez plusieurs personnes dans une même discussion.">
    <x-slot:actions><a href="{{ route('messages.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Messagerie</a></x-slot:actions>
</x-page-head>

<div class="row"><div class="col-lg-7">
<form method="POST" action="{{ route('groups.store') }}" class="ui-card rise">
    @csrf
    <div class="ui-card-body">
        <div class="mb-3"><label class="form-label">Nom du groupe</label>
            <input type="text" name="name" value="{{ old('name') }}" maxlength="100" class="form-control @error('name') is-invalid @enderror" placeholder="Ex : Livraisons de la semaine" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

        <label class="form-label">Membres <small class="text-muted">(parmi les personnes que vous pouvez contacter)</small></label>
        <input type="search" id="member-search" class="form-control form-control-sm mb-2" placeholder="Rechercher…">
        <div style="max-height:320px;overflow:auto" class="border rounded-3">
            @forelse($contacts as $u)
                <label class="d-flex align-items-center gap-3 p-2 border-bottom member-row" style="cursor:pointer;margin:0">
                    <input type="checkbox" name="members[]" value="{{ $u->id }}" class="form-check-input m-0" @checked(in_array($u->id, old('members', [])))>
                    <x-avatar :user="$u" size="sm" />
                    <span class="flex-grow-1"><b class="small">{{ $u->display_name }}</b><br><small class="text-muted">{{ $u->role_label }}</small></span>
                </label>
            @empty
                <div class="p-3 text-muted small">Aucun contact disponible pour l'instant.</div>
            @endforelse
        </div>
        @error('members')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Vous serez le créateur : vous pourrez ajouter ou retirer des membres et supprimer le groupe.</div>
        <button class="btn btn-primary btn-lg mt-4"><i class="bi bi-people me-1"></i>Créer le groupe</button>
    </div>
</form>
</div></div>
@endsection

@push('scripts')
<script>
document.getElementById('member-search').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('.member-row').forEach(function (r) { r.style.display = r.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none'; });
});
</script>
@endpush
