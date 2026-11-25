@extends('layouts.auth')
@section('title', 'Mot de passe oublié')

@section('content')
    <h1 class="fw-bold mb-1" style="letter-spacing:-.03em">Mot de passe oublié ?</h1>
    <p class="text-muted mb-4">Entrez l'adresse e-mail de votre compte, nous vous envoyons un lien pour en choisir un nouveau. Valable pour tous les types de compte.</p>

    @if(session('status'))
        <div class="alert alert-success d-flex gap-2"><i class="bi bi-envelope-check fs-5"></i><div>{{ session('status') }}</div></div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary btn-lg w-100"><i class="bi bi-send me-1"></i>Envoyer le lien</button>
    </form>
    <p class="text-center mt-4 mb-0"><a href="javascript:history.back()" class="fw-bold">← Retour</a></p>
@endsection
