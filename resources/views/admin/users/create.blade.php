@extends('layouts.admin')
@section('title', 'Nouveau livreur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
        <h5 class="fw-bold mb-0">Créer un compte livreur</h5>
        <p class="text-muted mb-0 small">Un email avec les identifiants sera automatiquement envoyé.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-person-plus me-2 text-warning"></i>Informations du livreur
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
                </div>
                @endif
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom complet *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required placeholder="Ex: Amadou Diallo">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required placeholder="livreur@email.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+221 77 000 00 00">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Zone de livraison</label>
                        <input type="text" name="zone" class="form-control" value="{{ old('zone') }}" placeholder="Ex: Dakar Centre, Pikine...">
                    </div>
                    <div class="alert alert-info" style="font-size:.85rem">
                        <i class="bi bi-info-circle me-2"></i>
                        Un mot de passe <strong>aléatoire sécurisé</strong> sera généré et envoyé par email au livreur.
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        <i class="bi bi-send me-2"></i>Créer le compte et envoyer l'email
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection