@extends('layouts.auth')
@section('title', 'Choisissez votre mot de passe')

@section('content')
    <div class="portal-icon" style="background:linear-gradient(135deg,#f59e0b,#f97316)"><i class="bi bi-key-fill"></i></div>
    <h1 class="fw-bold mb-1" style="letter-spacing:-.03em">Choisissez votre mot de passe</h1>
    <p class="text-muted mb-4">Vous êtes connecté avec un mot de passe provisoire. Pour votre sécurité, définissez maintenant le vôtre.</p>

    <form method="POST" action="{{ route('password.force.update') }}">
        @csrf
        <div class="mb-3"><label class="form-label">Nouveau mot de passe</label>
            <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required autofocus autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-4"><label class="form-label">Confirmation</label>
            <input type="password" name="password_confirmation" class="form-control form-control-lg" required autocomplete="new-password"></div>
        <button class="btn btn-primary btn-lg w-100">Enregistrer et continuer</button>
    </form>
    <form action="{{ route('logout') }}" method="POST" class="text-center mt-3" data-no-loading>@csrf
        <button class="btn btn-link text-muted">Se déconnecter</button></form>
@endsection
