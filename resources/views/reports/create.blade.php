<!-- resources/views/reports/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Créer un Nouveau Rapport</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reports.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Titre du rapport <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="type" class="form-label">Type de rapport <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Sélectionner un type</option>
                            <option value="monthly" {{ old('type') == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                            <option value="quarterly" {{ old('type') == 'quarterly' ? 'selected' : '' }}>Trimestriel</option>
                            <option value="annual" {{ old('type') == 'annual' ? 'selected' : '' }}>Annuel</option>
                            <option value="custom" {{ old('type') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="period_start" class="form-label">Début de période <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('period_start') is-invalid @enderror" id="period_start" name="period_start" value="{{ old('period_start') }}" required>
                        @error('period_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="period_end" class="form-label">Fin de période <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('period_end') is-invalid @enderror" id="period_end" name="period_end" value="{{ old('period_end') }}" required>
                        @error('period_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Contenu du rapport <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Créer le rapport</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection