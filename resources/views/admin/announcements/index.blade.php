@extends('layouts.dashboard')
@section('title', 'Annonces')

@section('content')
<x-page-head title="Annonces" subtitle="Envoyez un message à tous ou à un groupe. Personne ne peut y répondre." />

<div class="row g-4">
    <div class="col-lg-5">
        <form method="POST" action="{{ route('admin.announcements.store') }}" class="ui-card rise" data-confirm="Envoyer cette annonce maintenant ?">
            @csrf
            <div class="ui-card-head">Nouvelle annonce</div>
            <div class="ui-card-body">
                <div class="mb-3"><label class="form-label">Destinataires</label>
                    <select name="audience" class="form-select">
                        @foreach(\App\Models\Announcement::AUDIENCES as $k => $l)<option value="{{ $k }}" @selected(old('audience', 'all') === $k)>{{ $l }}</option>@endforeach
                    </select></div>
                <div class="mb-3"><label class="form-label">Titre</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" maxlength="150" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Message</label>
                    <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" maxlength="3000" required>{{ old('body') }}</textarea>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <button class="btn btn-primary w-100"><i class="bi bi-send me-1"></i>Envoyer l'annonce</button>
                <small class="text-muted d-block mt-2"><i class="bi bi-lock me-1"></i>Les destinataires reçoivent une notification et ne peuvent pas répondre.</small>
            </div>
        </form>
    </div>

    <div class="col-lg-7">
        <div class="ui-card rise">
            <div class="ui-card-head">Historique</div>
            @forelse($announcements as $a)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between gap-2"><b>{{ $a->title }}</b>
                        <form action="{{ route('admin.announcements.destroy', $a) }}" method="POST" data-confirm="Supprimer cette annonce ?">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button></form></div>
                    <div class="small text-muted mb-1">{{ $a->created_at->format('d/m/Y H:i') }} · <span class="pill pill-role-client">{{ $a->audience_label }}</span></div>
                    <div class="small" style="white-space:pre-line">{{ \Illuminate\Support\Str::limit($a->body, 220) }}</div>
                </div>
            @empty
                <x-empty-state icon="megaphone" title="Aucune annonce envoyée" />
            @endforelse
        </div>
        <div class="mt-3 d-flex justify-content-center">{{ $announcements->links() }}</div>
    </div>
</div>
@endsection
