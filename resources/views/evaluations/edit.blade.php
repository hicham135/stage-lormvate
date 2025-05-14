<!-- resources/views/evaluations/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Modifier l'Évaluation</h1>
        <div>
            <a href="{{ route('evaluations.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('evaluations.show', $evaluation->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Voir Détails
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                {{ substr($evaluation->evaluatedUser->name, 0, 1) }}
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0">{{ $evaluation->evaluatedUser->name }}</h5>
                                <p class="text-muted mb-0">{{ $evaluation->evaluatedUser->role == 'employee' ? 'Employé' : $evaluation->evaluatedUser->role }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="period" class="form-label">Période <span class="text-danger">*</span></label>
                        <select class="form-select @error('period') is-invalid @enderror" id="period" name="period" required>
                            <option value="">Sélectionner une période</option>
                            <option value="Q1 2024" {{ old('period', $evaluation->period) == 'Q1 2024' ? 'selected' : '' }}>Q1 2024</option>
                            <option value="Q2 2024" {{ old('period', $evaluation->period) == 'Q2 2024' ? 'selected' : '' }}>Q2 2024</option>
                            <option value="Q3 2024" {{ old('period', $evaluation->period) == 'Q3 2024' ? 'selected' : '' }}>Q3 2024</option>
                            <option value="Q4 2024" {{ old('period', $evaluation->period) == 'Q4 2024' ? 'selected' : '' }}>Q4 2024</option>
                            <option value="Annuel 2024" {{ old('period', $evaluation->period) == 'Annuel 2024' ? 'selected' : '' }}>Annuel 2024</option>
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
                            <input type="range" class="form-range me-2" id="performance_score" name="performance_score" min="1" max="10" value="{{ old('performance_score', $evaluation->performance_score) }}" oninput="document.getElementById('performance_value').innerText = this.value">
                            <span id="performance_value" class="badge bg-primary" style="width: 40px;">{{ $evaluation->performance_score }}</span>
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
                            <input type="range" class="form-range me-2" id="communication_score" name="communication_score" min="1" max="10" value="{{ old('communication_score', $evaluation->communication_score) }}" oninput="document.getElementById('communication_value').innerText = this.value">
                            <span id="communication_value" class="badge bg-primary" style="width: 40px;">{{ $evaluation->communication_score }}</span>
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
                            <input type="range" class="form-range me-2" id="teamwork_score" name="teamwork_score" min="1" max="10" value="{{ old('teamwork_score', $evaluation->teamwork_score) }}" oninput="document.getElementById('teamwork_value').innerText = this.value">
                            <span id="teamwork_value" class="badge bg-primary" style="width: 40px;">{{ $evaluation->teamwork_score }}</span>
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
                            <input type="range" class="form-range me-2" id="innovation_score" name="innovation_score" min="1" max="10" value="{{ old('innovation_score', $evaluation->innovation_score) }}" oninput="document.getElementById('innovation_value').innerText = this.value">
                            <span id="innovation_value" class="badge bg-primary" style="width: 40px;">{{ $evaluation->innovation_score }}</span>
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
                    <textarea class="form-control @error('comments') is-invalid @enderror" id="comments" name="comments" rows="5" required>{{ old('comments', $evaluation->comments) }}</textarea>
                    @error('comments')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="draft" {{ old('status', $evaluation->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ old('status', $evaluation->status) == 'published' ? 'selected' : '' }}>Publié</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Mettre à jour l'évaluation</button>
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