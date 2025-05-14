@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Modifier mon profil</h1>
        <a href="{{ route('employee.profile.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('employee.profile.update') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <fieldset disabled>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="department" class="form-label">Département</label>
                            <input type="text" class="form-control" id="department" value="{{ $employee->department->name }}">
                            <small class="form-text text-muted">Le département ne peut pas être modifié par l'employé.</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="role" class="form-label">Rôle</label>
                            <input type="text" class="form-control" id="role" value="{{ $employee->role == 'employee' ? 'Employé' : $employee->role }}">
                            <small class="form-text text-muted">Le rôle ne peut pas être modifié par l'employé.</small>
                        </div>
                    </div>
                </fieldset>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', '06 12 34 56 78') }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="address" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', '123 Rue Exemple, Ville, Pays') }}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', 'Brève description professionnelle...') }}</textarea>
                </div>
                
                <hr class="my-4">
                
                <h5 class="mb-3">Modifier le mot de passe</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmation du mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Laissez les champs de mot de passe vides si vous ne souhaitez pas le modifier.
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Mettre à jour mon profil</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
