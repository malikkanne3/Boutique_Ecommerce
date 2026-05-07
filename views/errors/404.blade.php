@extends('layouts.app')
@section('title', 'Page introuvable')

@section('content')
<div class="container text-center py-5">
    <div style="font-size:5rem">🔍</div>
    <h1 class="fw-bold mt-3" style="font-size:4rem;color:#e2e8f0">404</h1>
    <h4 class="fw-semibold mb-2">Page introuvable</h4>
    <p class="text-muted mb-4">La page que vous cherchez n'existe pas ou a été déplacée.</p>
    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('home') }}" class="btn btn-primary px-4">
            <i class="bi bi-house me-2"></i>Accueil
        </a>
        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary px-4">
            <i class="bi bi-shop me-2"></i>Boutique
        </a>
    </div>
</div>
@endsection
