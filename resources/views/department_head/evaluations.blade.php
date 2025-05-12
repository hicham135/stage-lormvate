<!-- resources/views/department_head/evaluations.blade.php -->
@extends('layouts.app')

@section('title', 'Évaluations des employés')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Évaluations des employés</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportEvaluations">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createEvaluationModal">
                <i class="fas fa-plus me-1"></i> Nouvelle évaluation
            </button>
        </div>
    </div>

    <!-- Vue d'ensemble -->
    <div class="row mb-4">
        <!-- Statistiques des évaluations -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Vue d'ensemble</h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-warning">
                                <span>{{ count($pendingEvaluations) }}</span>
                            </div>
                            <h6 class="mt-2">Brouillons</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-success">
                                <span>{{ count($completedEvaluations) }}</span>
                            </div>
                            <h6 class="mt-2">Complétées</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-info">
                                <span>{{ $employees->count() }}</span>
                            </div>
                            <h6 class="mt-2">Employés</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-primary">
                                <span>4.2</span>
                            </div>
                            <h6 class="mt-2">Score moyen</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphique scores moyens -->
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Scores moyens par critère</h5>
                </div>
                <div class="card-body">
                    <canvas id="evaluationScoresChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Évaluations en cours (brouillons) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Évaluations en cours (brouillons)</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchDraftEvaluation" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchDraftBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="draftEvaluationsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Employé</th>
                            <th>Période</th>
                            <th>Date de création</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($pendingEvaluations) > 0)
                            @foreach($pendingEvaluations as $evaluation)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light me-2">
                                            @if($evaluation->evaluatee->profile_image)
                                                <img src="{{ asset('storage/' . $evaluation->evaluatee->profile_image) }}" alt="{{ $evaluation->evaluatee->name }}" class="avatar-img">
                                            @else
                                                <span class="avatar-text">{{ strtoupper(substr($evaluation->evaluatee->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $evaluation->evaluatee->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $evaluation->period }}</td>
                                <td>{{ date('d/m/Y', strtotime($evaluation->created_at)) }}</td>
                                <td>
                                    <span class="badge bg-secondary">Brouillon</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('department_head.edit_evaluation', ['id' => $evaluation->id]) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i> Compléter
                                        </a>
                                        <button type="button" class="btn btn-outline-danger delete-evaluation-btn"
                                                data-evaluation-id="{{ $evaluation->id }}"
                                                data-evaluation-employee="{{ $evaluation->evaluatee->name }}"
                                                data-evaluation-period="{{ $evaluation->period }}"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Aucune évaluation en cours</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Évaluations complétées -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Évaluations complétées</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchCompletedEvaluation" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchCompletedBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="completedEvaluationsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Employé</th>
                            <th>Période</th>
                            <th>Performance</th>
                            <th>Ponctualité</th>
                            <th>Travail d'équipe</th>
                            <th>Initiative</th>
                            <th>Score global</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($completedEvaluations) > 0)
                            @foreach($completedEvaluations as $evaluation)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light me-2">
                                            @if($evaluation->evaluatee->profile_image)
                                                <img src="{{ asset('storage/' . $evaluation->evaluatee->profile_image) }}" alt="{{ $evaluation->evaluatee->name }}" class="avatar-img">
                                            @else
                                                <span class="avatar-text">{{ strtoupper(substr($evaluation->evaluatee->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $evaluation->evaluatee->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $evaluation->period }}</td>
                                <td>
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $evaluation->performance_score)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $evaluation->punctuality_score)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $evaluation->teamwork_score)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $evaluation->initiative_score)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $avgScore = ($evaluation->performance_score + $evaluation->punctuality_score + $evaluation->teamwork_score + $evaluation->initiative_score) / 4;
                                        $avgScoreRounded = round($avgScore, 1);
                                        
                                        if ($avgScoreRounded >= 4.5) {
                                            $scoreClass = 'bg-success';
                                        } elseif ($avgScoreRounded >= 3.5) {
                                            $scoreClass = 'bg-info';
                                        } elseif ($avgScoreRounded >= 2.5) {
                                            $scoreClass = 'bg-warning';
                                        } else {
                                            $scoreClass = 'bg-danger';
                                        }
                                    @endphp
                                    <span class="badge {{ $scoreClass }}">{{ $avgScoreRounded }}/5</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info view-evaluation-btn"
                                                data-evaluation-id="{{ $evaluation->id }}"
                                                data-evaluation-employee="{{ $evaluation->evaluatee->name }}"
                                                data-evaluation-period="{{ $evaluation->period }}"
                                                data-evaluation-performance="{{ $evaluation->performance_score }}"
                                                data-evaluation-punctuality="{{ $evaluation->punctuality_score }}"
                                                data-evaluation-teamwork="{{ $evaluation->teamwork_score }}"
                                                data-evaluation-initiative="{{ $evaluation->initiative_score }}"
                                                data-evaluation-comments="{{ $evaluation->comments }}"
                                                data-evaluation-goals="{{ $evaluation->goals }}"
                                                data-evaluation-created="{{ date('d/m/Y', strtotime($evaluation->created_at)) }}"
                                                data-evaluation-updated="{{ date('d/m/Y', strtotime($evaluation->updated_at)) }}"
                                                data-evaluation-status="{{ $evaluation->status }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($evaluation->status === 'submitted')
                                            <a href="{{ route('department_head.edit_evaluation', ['id' => $evaluation->id]) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-outline-secondary print-evaluation-btn"
                                                data-evaluation-id="{{ $evaluation->id }}">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted">Aucune évaluation complétée</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-outline-primary" id="loadMoreCompleted">
                    <i class="fas fa-sync-alt me-1"></i> Charger plus
                </button>
            </div>
        </div>
    </div>

    <!-- Performance des employés -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Performance des employés</h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePerformance" aria-expanded="true" aria-controls="collapsePerformance">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="collapsePerformance">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Performance par employé</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="employeePerformanceChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Tendances par période</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="performanceTrendsChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour créer une évaluation -->
<div class="modal fade" id="createEvaluationModal" tabindex="-1" aria-labelledby="createEvaluationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createEvaluationModalLabel">Nouvelle évaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department_head.create_evaluation') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="evaluatee_id" class="form-label">Employé <span class="text-danger">*</span></label>
                        <select class="form-select" id="evaluatee_id" name="evaluatee_id" required>
                            <option value="">Sélectionner un employé</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="period" class="form-label">Période d'évaluation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="period" name="period" placeholder="Ex: Mai 2025" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour afficher les détails d'une évaluation -->
<div class="modal fade" id="viewEvaluationModal" tabindex="-1" aria-labelledby="viewEvaluationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEvaluationModalLabel">Détails de l'évaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Informations générales</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Employé:</th>
                                <td id="viewEvaluationEmployee"></td>
                            </tr>
                            <tr>
                                <th>Période:</th>
                                <td id="viewEvaluationPeriod"></td>
                            </tr>
                            <tr>
                                <th>Date de création:</th>
                                <td id="viewEvaluationCreated"></td>
                            </tr>
                            <tr>
                                <th>Date de soumission:</th>
                                <td id="viewEvaluationUpdated"></td>
                            </tr>
                            <tr>
                                <th>Statut:</th>
                                <td id="viewEvaluationStatus"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Scores</h6>
                        <div class="score-card mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Performance:</span>
                                <div class="rating-display" id="viewEvaluationPerformance"></div>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-primary" id="viewEvaluationPerformanceBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="5" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="score-card mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Ponctualité:</span>
                                <div class="rating-display" id="viewEvaluationPunctuality"></div>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-primary" id="viewEvaluationPunctualityBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="5" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="score-card mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Travail d'équipe:</span>
                                <div class="rating-display" id="viewEvaluationTeamwork"></div>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-primary" id="viewEvaluationTeamworkBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="5" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="score-card mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Initiative:</span>
                                <div class="rating-display" id="viewEvaluationInitiative"></div>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-primary" id="viewEvaluationInitiativeBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="5" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="score-card mt-4">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Score global:</span>
                                <span class="fw-bold" id="viewEvaluationAvgScore"></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" id="viewEvaluationAvgScoreBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="5" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Commentaires</h6>
                        <div class="p-3 bg-light rounded" id="viewEvaluationComments"></div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Objectifs et axes d'amélioration</h6>
                        <div class="p-3 bg-light rounded" id="viewEvaluationGoals"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" class="btn btn-primary" id="editEvaluationLink">Modifier</a>
                <button type="button" class="btn btn-outline-secondary" id="printEvaluationBtn">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour confirmer la suppression -->
<div class="modal fade" id="deleteEvaluationModal" tabindex="-1" aria-labelledby="deleteEvaluationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEvaluationModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette évaluation ?</p>
                <p><strong>Employé:</strong> <span id="deleteEvaluationEmployee"></span></p>
                <p><strong>Période:</strong> <span id="deleteEvaluationPeriod"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteEvaluationForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-text {
        font-size: 12px;
        font-weight: bold;
    }
    .stat-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
    }
    .stat-circle span {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .rating {
        font-size: 0.9rem;
    }
    .rating-display {
        color: #ffc107;
    }
    .score-card {
        padding: 10px 0;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default period to current month and year
        const now = new Date();
        const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        document.getElementById('period').value = months[now.getMonth()] + ' ' + now.getFullYear();
        
        // Initialize charts
        initEvaluationScoresChart();
        initEmployeePerformanceChart();
        initPerformanceTrendsChart();
        
        // Search functionality
        document.getElementById('searchDraftBtn').addEventListener('click', function() {
            searchTable('searchDraftEvaluation', 'draftEvaluationsTable');
        });
        
        document.getElementById('searchDraftEvaluation').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchTable('searchDraftEvaluation', 'draftEvaluationsTable');
            }
        });
        
        document.getElementById('searchCompletedBtn').addEventListener('click', function() {
            searchTable('searchCompletedEvaluation', 'completedEvaluationsTable');
        });
        
        document.getElementById('searchCompletedEvaluation').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchTable('searchCompletedEvaluation', 'completedEvaluationsTable');
            }
        });
        
        // Export data
        document.getElementById('exportEvaluations').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en cours de développement...');
        });
        
        // Load more completed evaluations
        document.getElementById('loadMoreCompleted').addEventListener('click', function() {
            alert('Fonctionnalité en cours de développement...');
        });
        
        // View evaluation details
        document.querySelectorAll('.view-evaluation-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const evaluationId = this.getAttribute('data-evaluation-id');
                const employee = this.getAttribute('data-evaluation-employee');
                const period = this.getAttribute('data-evaluation-period');
                const performanceScore = parseInt(this.getAttribute('data-evaluation-performance'));
                const punctualityScore = parseInt(this.getAttribute('data-evaluation-punctuality'));
                const teamworkScore = parseInt(this.getAttribute('data-evaluation-teamwork'));
                const initiativeScore = parseInt(this.getAttribute('data-evaluation-initiative'));
                const comments = this.getAttribute('data-evaluation-comments');
                const goals = this.getAttribute('data-evaluation-goals');
                const created = this.getAttribute('data-evaluation-created');
                const updated = this.getAttribute('data-evaluation-updated');
                const status = this.getAttribute('data-evaluation-status');
                
                // Set basic information
                document.getElementById('viewEvaluationEmployee').textContent = employee;
                document.getElementById('viewEvaluationPeriod').textContent = period;
                document.getElementById('viewEvaluationCreated').textContent = created;
                document.getElementById('viewEvaluationUpdated').textContent = updated;
                
                // Set status with badge
                const statusElement = document.getElementById('viewEvaluationStatus');
                statusElement.innerHTML = '';
                
                if (status === 'draft') {
                    statusElement.innerHTML = '<span class="badge bg-secondary">Brouillon</span>';
                } else if (status === 'submitted') {
                    statusElement.innerHTML = '<span class="badge bg-success">Soumis</span>';
                } else if (status === 'acknowledged') {
                    statusElement.innerHTML = '<span class="badge bg-info">Reconnu</span>';
                }
                
                // Set scores and progress bars
                setRatingStars('viewEvaluationPerformance', performanceScore);
                setRatingStars('viewEvaluationPunctuality', punctualityScore);
                setRatingStars('viewEvaluationTeamwork', teamworkScore);
                setRatingStars('viewEvaluationInitiative', initiativeScore);
                
                document.getElementById('viewEvaluationPerformanceBar').style.width = (performanceScore / 5 * 100) + '%';
                document.getElementById('viewEvaluationPerformanceBar').setAttribute('aria-valuenow', performanceScore);
                
                document.getElementById('viewEvaluationPunctualityBar').style.width = (punctualityScore / 5 * 100) + '%';
                document.getElementById('viewEvaluationPunctualityBar').setAttribute('aria-valuenow', punctualityScore);
                
                document.getElementById('viewEvaluationTeamworkBar').style.width = (teamworkScore / 5 * 100) + '%';
                document.getElementById('viewEvaluationTeamworkBar').setAttribute('aria-valuenow', teamworkScore);
                
                document.getElementById('viewEvaluationInitiativeBar').style.width = (initiativeScore / 5 * 100) + '%';
                document.getElementById('viewEvaluationInitiativeBar').setAttribute('aria-valuenow', initiativeScore);
                
                // Calculate and set average score
                const avgScore = (performanceScore + punctualityScore + teamworkScore + initiativeScore) / 4;
                const avgScoreRounded = Math.round(avgScore * 10) / 10; // Round to 1 decimal
                
                document.getElementById('viewEvaluationAvgScore').textContent = avgScoreRounded + '/5';
                document.getElementById('viewEvaluationAvgScoreBar').style.width = (avgScoreRounded / 5 * 100) + '%';
                document.getElementById('viewEvaluationAvgScoreBar').setAttribute('aria-valuenow', avgScoreRounded);
                
                // Set progress bar color based on average score
                const avgScoreBar = document.getElementById('viewEvaluationAvgScoreBar');
                avgScoreBar.classList.remove('bg-success', 'bg-info', 'bg-warning', 'bg-danger');
                
                if (avgScoreRounded >= 4.5) {
                    avgScoreBar.classList.add('bg-success');
                } else if (avgScoreRounded >= 3.5) {
                    avgScoreBar.classList.add('bg-info');
                } else if (avgScoreRounded >= 2.5) {
                    avgScoreBar.classList.add('bg-warning');
                } else {
                    avgScoreBar.classList.add('bg-danger');
                }
                
                // Set comments and goals
                document.getElementById('viewEvaluationComments').textContent = comments || 'Aucun commentaire';
                document.getElementById('viewEvaluationGoals').textContent = goals || 'Aucun objectif défini';
                
                // Set edit link and show/hide based on status
                const editLink = document.getElementById('editEvaluationLink');
                editLink.href = "{{ route('department_head.edit_evaluation', ['id' => '']) }}" + evaluationId;
                
                if (status === 'submitted') {
                    editLink.style.display = 'inline-block';
                } else {
                    editLink.style.display = 'none';
                }
                
                // Set print button action
                document.getElementById('printEvaluationBtn').setAttribute('data-evaluation-id', evaluationId);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('viewEvaluationModal'));
                modal.show();
            });
        });
        
        // Delete evaluation button
        document.querySelectorAll('.delete-evaluation-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const evaluationId = this.getAttribute('data-evaluation-id');
                const employee = this.getAttribute('data-evaluation-employee');
                const period = this.getAttribute('data-evaluation-period');
                
                // Set confirmation modal content
                document.getElementById('deleteEvaluationEmployee').textContent = employee;
                document.getElementById('deleteEvaluationPeriod').textContent = period;
                
                // Set form action (you need to implement the delete route)
                document.getElementById('deleteEvaluationForm').action = ''; // Add your delete evaluation route here
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('deleteEvaluationModal'));
                modal.show();
            });
        });
        
        // Print evaluation
        document.getElementById('printEvaluationBtn').addEventListener('click', function() {
            const evaluationId = this.getAttribute('data-evaluation-id');
            // Implement print functionality
            alert('Fonctionnalité d\'impression en cours de développement...');
        });
        
        document.querySelectorAll('.print-evaluation-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const evaluationId = this.getAttribute('data-evaluation-id');
                // Implement print functionality
                alert('Fonctionnalité d\'impression en cours de développement...');
            });
        });
    });

    function searchTable(inputId, tableId) {
        const searchValue = document.getElementById(inputId).value.toLowerCase();
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const employeeName = rows[i].cells[0].textContent.toLowerCase();
            const period = rows[i].cells[1].textContent.toLowerCase();
            
            if (employeeName.includes(searchValue) || period.includes(searchValue)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
    
    function setRatingStars(elementId, rating) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                container.innerHTML += '<i class="fas fa-star"></i>';
            } else {
                container.innerHTML += '<i class="far fa-star"></i>';
            }
        }
        
        container.innerHTML += ' <span>' + rating + '/5</span>';
    }

    function initEvaluationScoresChart() {
        var ctx = document.getElementById('evaluationScoresChart').getContext('2d');
        
        // Sample data for evaluation scores
        // In a real application, this would come from the backend
        var data = {
            labels: ['Performance', 'Ponctualité', 'Travail d\'équipe', 'Initiative'],
            datasets: [{
                label: 'Score moyen',
                data: [4.2, 3.9, 4.5, 3.8],
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                fill: true
            }]
        };
        
        new Chart(ctx, {
            type: 'radar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 5,
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
    
    function initEmployeePerformanceChart() {
        var ctx = document.getElementById('employeePerformanceChart').getContext('2d');
        
        // Sample data for employee performance
        // In a real application, this would come from the backend
        var data = {
            labels: ['Ahmed', 'Fatima', 'Omar', 'Samira', 'Karim'],
            datasets: [
                {
                    label: 'Performance',
                    data: [4.5, 3.8, 4.2, 4.7, 3.5],
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Ponctualité',
                    data: [4.2, 4.5, 3.7, 4.0, 4.8],
                    backgroundColor: 'rgba(13, 202, 240, 0.2)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Travail d\'équipe',
                    data: [4.8, 4.0, 4.5, 4.3, 3.9],
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Initiative',
                    data: [3.5, 3.6, 4.0, 4.5, 3.2],
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2
                }
            ]
        };
        
        new Chart(ctx, {
            type: 'bar',
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
    
    function initPerformanceTrendsChart() {
        var ctx = document.getElementById('performanceTrendsChart').getContext('2d');
        
        // Sample data for performance trends
        // In a real application, this would come from the backend
        var data = {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai'],
            datasets: [
                {
                    label: 'Performance',
                    data: [3.8, 4.0, 4.1, 4.3, 4.5],
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'Ponctualité',
                    data: [3.6, 3.8, 4.0, 4.1, 4.2],
                    backgroundColor: 'rgba(13, 202, 240, 0.2)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'Travail d\'équipe',
                    data: [4.1, 4.2, 4.3, 4.4, 4.5],
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'Initiative',
                    data: [3.5, 3.6, 3.7, 3.8, 4.0],
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
</script>
@endsection