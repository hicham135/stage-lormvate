<!-- resources/views/evaluations/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nouvelle Évaluation</h1>
        <a href="{{ route('evaluations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('evaluations.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="evaluated_user_id" class="form-label">Employé <span class="text-danger">*</span></label>
                        <select class="form-select @error('evaluated_user_id') is-invalid @enderror" id="evaluated_user_id" name="evaluated_user_id" required>
                            <option value="">Sélectionner un employé</option>
                            @foreach($team as $member)
                                <option value="{{ $member->id }}" {{ old('evaluated_user_id') == $member->id || (isset($user) && $user->id == $member->id) ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('evaluated_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="period" class="form-label">Période <span class="text-danger">*</span></label>
                        <select class="form-select @error('period') is-invalid @enderror" id="period" name="period" required>
                            <option value="">Sélectionner une période</option>
                            <option value="Q1 2024" {{ old('period') == 'Q1 2024' ? 'selected' : '' }}>Q1 2024</option>
                            <option value="Q2 2024" {{ old('period') == 'Q2 2024' ? 'selected' : '' }}>Q2 2024</option>
                            <option value="Q3 2024" {{ old('period') == 'Q3 2024' ? 'selected' : '' }}>Q3 2024</option>
                            <option value="Q4 2024" {{ old('period') == 'Q4 2024' ? 'selected' : '' }}>Q4 2024</option>
                            <option value="Annuel 2024" {{ old('period') == 'Annuel 2024' ? 'selected' : '' }}>Annuel 2024</option>
                        </select>
                        @error('period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <h5 class="mt-4 mb-3">Évaluation des compétences</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="performance_score" class="form-label">Performance professionnelle <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center">
                            <input type="range" class="form-range me-2" id="performance_score" name="performance_score" min="1" max="10" value="{{ old('performance_score', 5) }}" oninput="document.getElementById('performance_value').innerText = this.value">
                            <span id="performance_value" class="badge bg-primary" style="width: 40px;">5</span>
                        </div>
                        <small class="text-muted d-flex justify-content-between">
                            <span>Faible</span>
                            <span>Excellent</span>
                        </small>
                        @error('performance_score')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="communication_score" class="form-label">Communication <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center">
                            <input type="range" class="form-range me-2" id="communication_score" name="communication_score" min="1" max="10" value="{{ old('communication_score', 5) }}" oninput="document.getElementById('communication_value').innerText = this.value">
                            <span id="communication_value" class="badge bg-primary" style="width: 40px;">5</span>
                        </div>
                        <small class="text-muted d-flex justify-content-between">
                            <span>Faible</span>
                            <span>Excellent</span>
                        </small>
                        @error('communication_score')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="teamwork_score" class="form-label">Travail d'équipe <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center">
                            <input type="range" class="form-range me-2" id="teamwork_score" name="teamwork_score" min="1" max="10" value="{{ old('teamwork_score', 5) }}" oninput="document.getElementById('teamwork_value').innerText = this.value">
                            <span id="teamwork_value" class="badge bg-primary" style="width: 40px;">5</span>
                        </div>
                        <small class="text-muted d-flex justify-content-between">
                            <span>Faible</span>
                            <span>Excellent</span>
                        </small>
                        @error('teamwork_score')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="innovation_score" class="form-label">Innovation et créativité <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center">
                            <input type="range" class="form-range me-2" id="innovation_score" name="innovation_score" min="1" max="10" value="{{ old('innovation_score', 5) }}" oninput="document.getElementById('innovation_value').innerText = this.value">
                            <span id="innovation_value" class="badge bg-primary" style="width: 40px;">5</span>
                        </div>
                        <small class="text-muted d-flex justify-content-between">
                            <span>Faible</span>
                            <span>Excellent</span>
                        </small>
                        @error('innovation_score')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="comments" class="form-label">Commentaires et observations <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('comments') is-invalid @enderror" id="comments" name="comments" rows="5" required>{{ old('comments') }}</textarea>
                    @error('comments')
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
                    <button type="submit" class="btn btn-primary">Sauvegarder l'évaluation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialiser les valeurs affichées
    document.getElementById('performance_value').innerText = document.getElementById('performance_score').value;
    document.getElementById('communication_value').innerText = document.getElementById('communication_score').value;
    document.getElementById('teamwork_value').innerText = document.getElementById('teamwork_score').value;
    document.getElementById('innovation_value').innerText = document.getElementById('innovation_score').value;
</script>
@endsection