@extends('layouts.dashboard')
@section('title', 'Annonces')

@section('content')
<x-page-head title="Annonces de l'administration" subtitle="Informations importantes de la plateforme (lecture seule)." />

<div class="row g-3">
    @forelse($announcements as $a)
        <div class="col-12 rise">
            <div class="ui-card"><div class="ui-card-body d-flex gap-3">
                <span class="notif-ico" style="background:rgba(245,158,11,.16);color:#f59e0b"><i class="bi bi-megaphone-fill"></i></span>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between flex-wrap gap-2"><h5 class="fw-bold mb-1">{{ $a->title }}</h5><small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $a->created_at->format('d/m/Y à H:i') }}</small></div>
                    <p class="mb-2" style="white-space:pre-line">{{ $a->body }}</p>
                    <span class="pill pill-off"><i class="bi bi-lock"></i> Message de l'administration — vous ne pouvez pas y répondre</span>
                </div>
            </div></div>
        </div>
    @empty
        <div class="col-12 ui-card"><x-empty-state icon="megaphone" title="Aucune annonce" text="Les messages de l'administration apparaîtront ici." /></div>
    @endforelse
</div>
<div class="mt-4 d-flex justify-content-center">{{ $announcements->links() }}</div>
@endsection
