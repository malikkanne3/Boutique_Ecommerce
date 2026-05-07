@extends('layouts.app')
@section('title', 'Accès interdit')

@section('content')
<div class="container text-center py-5">
    <div style="font-size:5rem">🚫</div>
    <h1 class="fw-bold mt-3" style="font-size:4rem;color:#e2e8f0">403</h1>
    <h4 class="fw-semibold mb-2">Accès interdit</h4>
    <p class="text-muted mb-4">Vous n'avez pas les droits pour accéder à cette page.</p>
    <a href="{{ route('home') }}" class="btn btn-primary px-5">
        <i class="bi bi-house me-2"></i>Retour à l'accueil
    </a>
</div>
@endsection
