<!-- resources/views/department_head/edit_evaluation.blade.php -->
@extends('layouts.app')

@section('title', 'Édition de l\'évaluation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Édition de l'évaluation</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('department_head.evaluations') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimer
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-circle-lg bg-light me-3">
                            @if($evaluatee->profile_image)
                                <img src="{{ asset('storage/' . $evaluatee->profile_image) }}" alt="{{ $evaluatee->name }}" class="avatar-img-lg">
                            @else
                                <span class="avatar-text-lg">{{ strtoupper(substr($evaluatee->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $evaluatee->name }}</h4>
                            <p class="text-muted mb-0">{{ $evaluatee->email }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Période d'évaluation:</label>
                        <p class="mb-0">{{ $evaluation->period }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date de création:</label>
                        <p class="mb-0">{{ date('d/m/Y', strtotime($evaluation->created_at)) }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Statut:</label>
                        @if($evaluation->status === 'draft')
                            <span class="badge bg-secondary">Brouillon</span>
                        @elseif($evaluation->status === 'submitted')
                            <span class="badge bg-success">Soumis</span>
                        @elseif($evaluation->status === 'acknowledged')
                            <span class="badge bg-info">Reconnu</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Formulaire d'évaluation</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('department_head.update_evaluation', ['id' => $evaluation->id]) }}" method="POST" id="evaluationForm">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="performance_score" class="form-label">Performance <span class="text-danger">*</span></label>
                                <div class="star-rating">
                                    <div class="rating-group">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" class="rating-input" id="performance-{{ $i }}" name="performance_score" value="{{ $i }}" {{ $evaluation->performance_score == $i ? 'checked' : '' }} required>
                                            <label for="performance-{{ $i }}" class="rating-label">
                                                <i class="rating-icon fas fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="form-text">Évaluez la qualité et l'efficacité du travail de l'employé.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="punctuality_score" class="form-label">Ponctualité <span class="text-danger">*</span></label>
                                <div class="star-rating">
                                    <div class="rating-group">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" class="rating-input" id="punctuality-{{ $i }}" name="punctuality_score" value="{{ $i }}" {{ $evaluation->punctuality_score == $i ? 'checked' : '' }} required>
                                            <label for="punctuality-{{ $i }}" class="rating-label">
                                                <i class="rating-icon fas fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="form-text">Évaluez le respect des horaires et des délais.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="teamwork_score" class="form-label">Travail d'équipe <span class="text-danger">*</span></label>
                                <div class="star-rating">
                                    <div class="rating-group">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" class="rating-input" id="teamwork-{{ $i }}" name="teamwork_score" value="{{ $i }}" {{ $evaluation->teamwork_score == $i ? 'checked' : '' }} required>
                                            <label for="teamwork-{{ $i }}" class="rating-label">
                                                <i class="rating-icon fas fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="form-text">Évaluez la collaboration avec les collègues et la communication.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="initiative_score" class="form-label">Initiative <span class="text-danger">*</span></label>
                                <div class="star-rating">
                                    <div class="rating-group">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" class="rating-input" id="initiative-{{ $i }}" name="initiative_score" value="{{ $i }}" {{ $evaluation->initiative_score == $i ? 'checked' : '' }} required>
                                            <label for="initiative-{{ $i }}" class="rating-label">
                                                <i class="rating-icon fas fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="form-text">Évaluez la prise d'initiative et l'autonomie.</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="comments" class="form-label">Commentaires <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="comments" name="comments" rows="4" required>{{ $evaluation->comments }}</textarea>
                            <div class="form-text">Ajoutez des commentaires détaillés sur la performance de l'employé.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="goals" class="form-label">Objectifs et axes d'amélioration <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="goals" name="goals" rows="4" required>{{ $evaluation->goals }}</textarea>
                            <div class="form-text">Définissez les objectifs et les axes d'amélioration pour la prochaine période.</div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary" name="submit" value="1">
                                <i class="fas fa-paper-plane me-1"></i> Soumettre l'évaluation
                            </button>
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-save me-1"></i> Enregistrer comme brouillon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($evaluation->status !== 'draft')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Évolution des évaluations</h5>
        </div>
        <div class="card-body">
            <canvas id="evaluationHistoryChart" height="250"></canvas>
        </div>
    </div>
    @endif
</div>

<!-- Modal de confirmation de soumission -->
<div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-labelledby="submitConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submitConfirmModalLabel">Confirmer la soumission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir soumettre cette évaluation ?</p>
                <p>Une fois soumise, l'employé sera notifié et pourra consulter son évaluation.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Soumettre</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-circle-lg {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .avatar-img-lg {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-text-lg {
        font-size: 30px;
        font-weight: bold;
    }
    
    /* Star Rating Styles */
    .star-rating {
        margin-bottom: 10px;
    }
    .rating-group {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .rating-input {
        position: absolute !important;
        left: -9999px !important;
    }
    .rating-label {
        margin: 0;
        padding: 0 3px;
        font-size: 1.5rem;
        cursor: pointer;
        color: #ddd;
    }
    .rating-label:hover,
    .rating-label:hover ~ .rating-label,
    .rating-input:checked ~ .rating-label {
        color: #ffc107;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent accidental form submission when pressing Enter
        document.getElementById('evaluationForm').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });
        
        // Submit confirmation
        document.querySelector('button[name="submit"]').addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('submitConfirmModal'));
            modal.show();
        });
        
        document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
            const submitButton = document.querySelector('button[name="submit"]');
            // Add a hidden input to the form to indicate submission
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'submit';
            input.value = '1';
            document.getElementById('evaluationForm').appendChild(input);
            // Submit the form
            document.getElementById('evaluationForm').submit();
        });
        
        @if($evaluation->status !== 'draft')
        // Initialize evaluation history chart
        initEvaluationHistoryChart();
        @endif
    });
    
    @if($evaluation->status !== 'draft')
    function initEvaluationHistoryChart() {
        var ctx = document.getElementById('evaluationHistoryChart').getContext('2d');
        
        // Sample data for evaluation history
        // In a real application, this would come from the backend
        var data = {
            labels: ['Janvier 2025', 'Février 2025', 'Mars 2025', 'Avril 2025', 'Mai 2025'],
            datasets: [
                {
                    label: 'Performance',
                    data: [3.5, 3.7, 4.0, 4.2, 4.5],
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'Ponctualité',
                    data: [3.0, 3.2, 3.5, 3.8, 4.0],
                    backgroundColor: 'rgba(13, 202, 240, 0.2)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'Travail d\'équipe',
                    data: [4.0, 4.2, 4.3, 4.4, 4.5],
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'Initiative',
                    data: [3.0, 3.3, 3.5, 3.7, 4.0],
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }
            ]
        };
        
        new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    @endif
</script>
@endsection