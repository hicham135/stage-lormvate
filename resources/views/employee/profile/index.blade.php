@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mon Profil</h1>
        <a href="{{ route('employee.profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Modifier mon profil
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-placeholder bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ substr($employee->name, 0, 1) }}
                        </div>
                        <h4 class="mt-3">{{ $employee->name }}</h4>
                        <p class="text-muted">{{ $employee->role == 'employee' ? 'Employé' : $employee->role }}</p>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-envelope me-2"></i> Email</span>
                            <span>{{ $employee->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-building me-2"></i> Département</span>
                            <span>{{ $employee->department->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-tie me-2"></i> Responsable</span>
                            <span>{{ $employee->department->head->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar me-2"></i> Date d'embauche</span>
                            <span>{{ $employee->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('employee.attendance.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-clock me-2"></i>Pointage de présence
                        </a>
                        <a href="{{ route('employee.requests.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-paper-plane me-2"></i>Nouvelle demande
                        </a>
                        <a href="{{ route('employee.tasks.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-tasks me-2"></i>Mes tâches
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Mes évaluations</h5>
                </div>
                <div class="card-body">
                    @if($evaluations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Période</th>
                                        <th>Performance</th>
                                        <th>Communication</th>
                                        <th>Travail d'équipe</th>
                                        <th>Innovation</th>
                                        <th>Moyenne</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evaluations as $evaluation)
                                    <tr>
                                        <td>{{ $evaluation->period }}</td>
                                        <td>{{ $evaluation->performance_score }}/10</td>
                                        <td>{{ $evaluation->communication_score }}/10</td>
                                        <td>{{ $evaluation->teamwork_score }}/10</td>
                                        <td>{{ $evaluation->innovation_score }}/10</td>
                                        <td>
                                            @php
                                                $average = ($evaluation->performance_score + $evaluation->communication_score + $evaluation->teamwork_score + $evaluation->innovation_score) / 4;
                                            @endphp
                                            <span class="badge {{ $average >= 8 ? 'bg-success' : ($average >= 6 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($average, 1) }}/10
                                            </span>
                                        </td>
                                        <td>{{ $evaluation->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune évaluation disponible pour le moment.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Performance et progression</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Performance par compétence</h6>
                    <canvas id="skillsChart" height="200"></canvas>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Évolution générale</h6>
                    <canvas id="progressChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique des compétences
    const skillsCtx = document.getElementById('skillsChart').getContext('2d');
    
    // Utiliser la dernière évaluation si disponible
    @if($evaluations->count() > 0)
        @php
            $latestEval = $evaluations->first();
        @endphp
        
        const skillsChart = new Chart(skillsCtx, {
            type: 'radar',
            data: {
                labels: ['Performance', 'Communication', 'Travail d\'équipe', 'Innovation'],
                datasets: [{
                    label: '{{ $latestEval->period }}',
                    data: [
                        {{ $latestEval->performance_score }},
                        {{ $latestEval->communication_score }},
                        {{ $latestEval->teamwork_score }},
                        {{ $latestEval->innovation_score }}
                    ],
                    borderColor: 'rgba(40, 167, 69, 0.8)',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(40, 167, 69, 1)'
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 10
                    }
                }
            }
        });
    @else
        // Données factices si aucune évaluation n'est disponible
        const skillsChart = new Chart(skillsCtx, {
            type: 'radar',
            data: {
                labels: ['Performance', 'Communication', 'Travail d\'équipe', 'Innovation'],
                datasets: [{
                    label: 'Non évalué',
                    data: [0, 0, 0, 0],
                    borderColor: 'rgba(108, 117, 125, 0.8)',
                    backgroundColor: 'rgba(108, 117, 125, 0.2)',
                    pointBackgroundColor: 'rgba(108, 117, 125, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(108, 117, 125, 1)'
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 10
                    }
                }
            }
        });
    @endif
    
    // Graphique de progression
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    
    // Préparer les données pour le graphique de progression
    @php
        $periods = [];
        $averages = [];
        
        foreach($evaluations as $eval) {
            $periods[] = $eval->period;
            $averages[] = ($eval->performance_score + $eval->communication_score + $eval->teamwork_score + $eval->innovation_score) / 4;
        }
        
        // Inverser les tableaux pour avoir l'ordre chronologique
        $periods = array_reverse($periods);
        $averages = array_reverse($averages);
    @endphp
    
    const progressChart = new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($periods) !!},
            datasets: [{
                label: 'Score moyen',
                data: {!! json_encode($averages) !!},
                borderColor: 'rgba(0, 123, 255, 0.8)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true
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