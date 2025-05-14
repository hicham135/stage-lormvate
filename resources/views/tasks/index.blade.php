<!-- resources/views/tasks/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Tâches</h1>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Tâche
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('tasks.index') }}" method="GET" class="d-flex">
                        <select name="status" class="form-select me-2" style="width: auto;">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        <select name="priority" class="form-select me-2" style="width: auto;">
                            <option value="">Toutes les priorités</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Basse</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Haute</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Rechercher..." id="task-search">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Assigné à</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Échéance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->assignedTo->name }}</td>
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
                                @elseif($task->status == 'completed')
                                    <span class="badge bg-success">Terminé</span>
                                @else
                                    <span class="badge bg-danger">Annulé</span>
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
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tâches par statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tâches par employé</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskEmployeeChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique des tâches par statut
    const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['En attente', 'En cours', 'Terminé', 'Annulé'],
            datasets: [{
                data: [
                    {{ $tasks->where('status', 'pending')->count() }},
                    {{ $tasks->where('status', 'in_progress')->count() }},
                    {{ $tasks->where('status', 'completed')->count() }},
                    {{ $tasks->where('status', 'cancelled')->count() }}
                ],
                backgroundColor: [
                    'rgba(108, 117, 125, 0.8)',
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Données pour le graphique des tâches par employé
    const employeeData = {
        labels: [
            @foreach($tasks->groupBy('assigned_to') as $userId => $userTasks)
                "{{ $userTasks->first()->assignedTo->name }}",
            @endforeach
        ],
        datasets: [{
            label: 'Tâches assignées',
            data: [
                @foreach($tasks->groupBy('assigned_to') as $userTasks)
                    {{ $userTasks->count() }},
                @endforeach
            ],
            backgroundColor: 'rgba(0, 123, 255, 0.8)'
        }]
    };
    
    // Graphique des tâches par employé
    const employeeCtx = document.getElementById('taskEmployeeChart').getContext('2d');
    const employeeChart = new Chart(employeeCtx, {
        type: 'bar',
        data: employeeData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection