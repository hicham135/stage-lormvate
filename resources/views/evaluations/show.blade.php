<!-- resources/views/evaluations/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails de l'Évaluation</h1>
        <div>
            <a href="{{ route('evaluations.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Évaluation - {{ $evaluation->period }}</h5>
            <span class="badge {{ $evaluation->status == 'published' ? 'bg-success' : 'bg-secondary' }}">
                {{ $evaluation->status == 'published' ? 'Publié' : 'Brouillon' }}
            </span>
        </div>
        <div class="card-body">
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
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><i class="fas fa-calendar me-2"></i> Période d'évaluation</span>
                            <span>{{ $evaluation->period }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><i class="fas fa-user me-2"></i> Évaluateur</span>
                            <span>{{ $evaluation->evaluator->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><i class="fas fa-calendar-alt me-2"></i> Date de création</span>
                            <span>{{ $evaluation->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="mb-3">Score global</h5>
                            @php
                                $average = ($evaluation->performance_score + $evaluation->communication_score + $evaluation->teamwork_score + $evaluation->innovation_score) / 4;
                            @endphp
                            
                            <div class="text-center mb-3">
                                <div class="position-relative d-inline-block">
                                    <div class="score-circle d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; border-radius: 50%; background-color: {{ $average >= 8 ? '#28a745' : ($average >= 6 ? '#ffc107' : '#dc3545') }}; color: white; font-size: 2.5rem; font-weight: bold;">
                                        {{ number_format($average, 1) }}
                                    </div>
                                    <span class="position-absolute bottom-0 end-0 badge bg-white text-dark border" style="font-size: 1rem;">/10</span>
                                </div>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="p-2 rounded" style="background-color: rgba(0, 123, 255, 0.1);">
                                        <div class="small text-muted">Performance</div>
                                        <div class="fw-bold">{{ $evaluation->performance_score }}/10</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="p-2 rounded" style="background-color: rgba(40, 167, 69, 0.1);">
                                        <div class="small text-muted">Communication</div>
                                        <div class="fw-bold">{{ $evaluation->communication_score }}/10</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="p-2 rounded" style="background-color: rgba(255, 193, 7, 0.1);">
                                        <div class="small text-muted">Travail d'équipe</div>
                                        <div class="fw-bold">{{ $evaluation->teamwork_score }}/10</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="p-2 rounded" style="background-color: rgba(220, 53, 69, 0.1);">
                                        <div class="small text-muted">Innovation</div>
                                        <div class="fw-bold">{{ $evaluation->innovation_score }}/10</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Commentaires et observations</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $evaluation->comments }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Évolution de performance</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceHistoryChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($evaluation->status == 'draft')
                                    <form action="{{ route('evaluations.update', $evaluation->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <input type="hidden" name="period" value="{{ $evaluation->period }}">
                                        <input type="hidden" name="performance_score" value="{{ $evaluation->performance_score }}">
                                        <input type="hidden" name="communication_score" value="{{ $evaluation->communication_score }}">
                                        <input type="hidden" name="teamwork_score" value="{{ $evaluation->teamwork_score }}">
                                        <input type="hidden" name="innovation_score" value="{{ $evaluation->innovation_score }}">
                                        <input type="hidden" name="comments" value="{{ $evaluation->comments }}">
                                        <input type="hidden" name="status" value="published">
                                        
                                        <button type="submit" class="btn btn-success w-100 mb-2">
                                            <i class="fas fa-check-circle me-2"></i>Publier l'évaluation
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="#" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </a>
                                <a href="#" class="btn btn-info w-100 mb-2">
                                    <i class="fas fa-envelope me-2"></i>Envoyer par email
                                </a>
                                <a href="{{ route('evaluations.create', ['user_id' => $evaluation->evaluated_user_id]) }}" class="btn btn-warning w-100">
                                    <i class="fas fa-plus me-2"></i>Nouvelle évaluation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique d'évolution de performance
    const historyCtx = document.getElementById('performanceHistoryChart').getContext('2d');
    
    // Simuler des données historiques (à remplacer par des données réelles)
    const historyChart = new Chart(historyCtx, {
        type: 'line',
        data: {
            labels: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024', '{{ $evaluation->period }}'],
            datasets: [{
                label: 'Performance',
                data: [7.0, 7.2, 7.5, 7.8, 8.0, {{ $evaluation->performance_score }}],
                borderColor: 'rgba(0, 123, 255, 0.8)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: false
            }, {
                label: 'Communication',
                data: [6.5, 7.0, 7.3, 7.5, 7.8, {{ $evaluation->communication_score }}],
                borderColor: 'rgba(40, 167, 69, 0.8)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: false
            }, {
                label: 'Travail d\'équipe',
                data: [7.5, 7.8, 8.0, 8.2, 8.5, {{ $evaluation->teamwork_score }}],
                borderColor: 'rgba(255, 193, 7, 0.8)',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                fill: false
            }, {
                label: 'Innovation',
                data: [6.8, 7.0, 7.2, 7.5, 7.8, {{ $evaluation->innovation_score }}],
                borderColor: 'rgba(220, 53, 69, 0.8)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false,
                    min: 5,
                    max: 10
                }
            }
        }
    });
</script>
@endsection