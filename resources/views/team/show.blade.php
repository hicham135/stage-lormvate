<!-- resources/views/team/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Profil de l'Employé</h1>
        <div>
            <a href="{{ route('team.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('evaluations.create', ['user_id' => $member->id]) }}" class="btn btn-warning">
                <i class="fas fa-star"></i> Créer Évaluation
            </a>
            <a href="{{ route('tasks.create', ['user_id' => $member->id]) }}" class="btn btn-primary">
                <i class="fas fa-tasks"></i> Assigner Tâche
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <h4 class="mt-3">{{ $member->name }}</h4>
                        <p class="text-muted">{{ $member->role == 'employee' ? 'Employé' : $member->role }}</p>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-envelope me-2"></i> Email</span>
                            <span>{{ $member->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-building me-2"></i> Département</span>
                            <span>{{ $member->department->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar me-2"></i> Date d'embauche</span>
                            <span>{{ $member->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Évaluations</h5>
                    <a href="{{ route('evaluations.create', ['user_id' => $member->id]) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($member->evaluations->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($member->evaluations->sortByDesc('created_at')->take(3) as $evaluation)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-0">{{ $evaluation->period }}</h6>
                                            <p class="text-muted small mb-0">{{ $evaluation->created_at->format('d/m/Y') }}</p>
                                        </div>
                                        <div>
                                            <span class="badge bg-info">Score: {{ ($evaluation->performance_score + $evaluation->communication_score + $evaluation->teamwork_score + $evaluation->innovation_score) / 4 }}/10</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center my-3">Aucune évaluation disponible</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tâches en cours</h5>
                    <a href="{{ route('tasks.create', ['user_id' => $member->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($member->tasks->where('status', '!=', 'completed')->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Priorité</th>
                                        <th>Statut</th>
                                        <th>Échéance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($member->tasks->where('status', '!=', 'completed')->sortBy('due_date') as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>
                                                @if($task->priority == 'low')
                                                    <span class="badge bg-success">Basse</span>
                                                @elseif($task->priority == 'medium')
                                                    <span class="badge bg-warning">Moyenne</span>
                                                @else
                                                    <span class="badge bg-danger">Haute</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->status == 'pending')
                                                    <span class="badge bg-secondary">En attente</span>
                                                @elseif($task->status == 'in_progress')
                                                    <span class="badge bg-primary">En cours</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->due_date->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center my-3">Aucune tâche en cours</p>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Statistiques de présence</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="200"></canvas>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Historique de performance</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique de présence
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'],
            datasets: [{
                label: 'Présent',
                data: [20, 19, 21, 18, 22, 15],
                backgroundColor: 'rgba(40, 167, 69, 0.8)'
            }, {
                label: 'Absent',
                data: [1, 2, 0, 3, 0, 2],
                backgroundColor: 'rgba(220, 53, 69, 0.8)'
            }, {
                label: 'En retard',
                data: [2, 1, 1, 0, 1, 3],
                backgroundColor: 'rgba(255, 193, 7, 0.8)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });
    
    // Graphique de performance
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024', 'Q2 2024'],
            datasets: [{
                label: 'Performance',
                data: [7.5, 8.2, 7.8, 8.5, 8.7, 9.0],
                borderColor: 'rgba(0, 123, 255, 0.8)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true
            }, {
                label: 'Communication',
                data: [7.0, 7.5, 8.0, 8.2, 8.5, 8.8],
                borderColor: 'rgba(40, 167, 69, 0.8)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true
            }, {
                label: 'Travail d\'équipe',
                data: [8.0, 8.3, 8.5, 8.7, 9.0, 9.2],
                borderColor: 'rgba(255, 193, 7, 0.8)',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10
                }
            }
        }
    });
</script>
@endsection