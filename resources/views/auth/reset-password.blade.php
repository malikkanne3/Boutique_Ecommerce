@extends('layouts.auth')
@section('title', 'Nouveau mot de passe')

@section('content')
    <h1 class="fw-bold mb-1" style="letter-spacing:-.03em">Nouveau mot de passe</h1>
    <p class="text-muted mb-4">Choisissez un mot de passe d'au moins 8 caractères.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="mb-3"><label class="form-label">Adresse email</label>
            <input type="email" name="email" value="{{ old('email', $request->email) }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label">Nouveau mot de passe</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autofocus autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-4"><label class="form-label">Confirmation</label>
            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password"></div>
        <button class="btn btn-primary btn-lg w-100">Enregistrer le mot de passe</button>
    </form>
@endsection
