@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mes Tâches</h1>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('employee.tasks.index') }}" method="GET" class="d-flex">
                        <select name="status" class="form-select me-2" style="width: auto;" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                        </select>
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
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Assigné par</th>
                            <th>Échéance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
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
                                @elseif($task->status == 'completed')
                                    <span class="badge bg-success">Terminé</span>
                                @else
                                    <span class="badge bg-danger">Annulé</span>
                                @endif
                            </td>
                            <td>{{ $task->assignedBy->name }}</td>
                            <td class="{{ $task->due_date < now() && $task->status != 'completed' ? 'text-danger' : '' }}">
                                {{ $task->due_date->format('d/m/Y') }}
                                @if($task->due_date < now() && $task->status != 'completed')
                                    <i class="fas fa-exclamation-circle ms-1"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('employee.tasks.show', $task->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($tasks->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucune tâche trouvée avec les critères sélectionnés.
                </div>
            @endif
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Répartition par statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Répartition par priorité</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskPriorityChart" height="250"></canvas>
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
    
    // Graphique des tâches par priorité
    const priorityCtx = document.getElementById('taskPriorityChart').getContext('2d');
    const priorityChart = new Chart(priorityCtx, {
        type: 'doughnut',
        data: {
            labels: ['Haute', 'Moyenne', 'Basse'],
            datasets: [{
                data: [
                    {{ $tasks->where('priority', 'high')->count() }},
                    {{ $tasks->where('priority', 'medium')->count() }},
                    {{ $tasks->where('priority', 'low')->count() }}
                ],
                backgroundColor: [
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 167, 69, 0.8)'
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
</script>
@endsection