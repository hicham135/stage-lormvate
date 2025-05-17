@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails de la Tâche</h1>
        <div>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $task->title }}</h5>
                    <span class="ms-auto">
                        @if($task->priority == 'low')
                            <span class="badge bg-success">Priorité: Basse</span>
                        @elseif($task->priority == 'medium')
                            <span class="badge bg-warning">Priorité: Moyenne</span>
                        @else
                            <span class="badge bg-danger">Priorité: Haute</span>
                        @endif
                        
                        @if($task->status == 'pending')
                            <span class="badge bg-secondary">En attente</span>
                        @elseif($task->status == 'in_progress')
                            <span class="badge bg-primary">En cours</span>
                        @elseif($task->status == 'completed')
                            <span class="badge bg-success">Terminé</span>
                        @else
                            <span class="badge bg-danger">Annulé</span>
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Description</h6>
                    <p>{{ $task->description }}</p>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Détails de la tâche</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Assigné à:</span>
                                    <span class="text-primary">{{ $task->assignedTo->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Date de création:</span>
                                    <span>{{ $task->created_at->format('d/m/Y') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Date d'échéance:</span>
                                    <span class="{{ $task->due_date < now() && $task->status != 'completed' ? 'text-danger' : '' }}">
                                        {{ $task->due_date->format('d/m/Y') }}
                                        @if($task->due_date < now() && $task->status != 'completed')
                                            <i class="fas fa-exclamation-circle ms-1"></i>
                                        @endif
                                    </span>
                                </li>
                                @if($task->completed_at)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Date de complétion:</span>
                                    <span class="text-success">{{ $task->completed_at->format('d/m/Y') }}</span>
                                </li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Information</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Créée par:</span>
                                    <span>{{ $task->assignedBy->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Département:</span>
                                    <span>{{ $task->department->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Temps restant:</span>
                                    @if($task->status == 'completed')
                                        <span class="text-success">Terminé</span>
                                    @elseif($task->due_date < now())
                                        <span class="text-danger">En retard</span>
                                    @else
                                        <span>{{ now()->diffInDays($task->due_date) }} jours</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    @if($task->status != 'completed' && $task->status != 'cancelled')
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" name="title" value="{{ $task->title }}">
                        <input type="hidden" name="description" value="{{ $task->description }}">
                        <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                        <input type="hidden" name="priority" value="{{ $task->priority }}">
                        <input type="hidden" name="due_date" value="{{ $task->due_date->format('Y-m-d') }}">
                        
                        <div class="btn-group w-100">
                            @if($task->status == 'pending')
                                <button type="submit" name="status" value="in_progress" class="btn btn-primary">Marquer En Cours</button>
                            @endif
                            <button type="submit" name="status" value="completed" class="btn btn-success">Marquer Terminé</button>
                            <button type="submit" name="status" value="cancelled" class="btn btn-danger">Annuler</button>
                        </div>
                    </form>
                    @elseif($task->status == 'completed')
                        <div class="text-center text-success">
                            <i class="fas fa-check-circle me-1"></i> Cette tâche a été marquée comme terminée le {{ $task->completed_at->format('d/m/Y') }}
                        </div>
                    @else
                        <div class="text-center text-danger">
                            <i class="fas fa-times-circle me-1"></i> Cette tâche a été annulée
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Employé assigné</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($task->assignedTo->name, 0, 1) }}
                        </div>
                        <h5 class="mt-3">{{ $task->assignedTo->name }}</h5>
                        <p class="text-muted">{{ $task->assignedTo->role == 'employee' ? 'Employé' : $task->assignedTo->role }}</p>
                    </div>
                    
                    <hr>
                    
                    <h6>Autres tâches assignées</h6>
                    <ul class="list-group list-group-flush">
                        @php
                            $otherTasks = $task->assignedTo->tasks
                                ->where('id', '!=', $task->id)
                                ->where('status', '!=', 'completed')
                                ->where('status', '!=', 'cancelled')
                                ->take(3);
                        @endphp
                        
                        @if($otherTasks->count() > 0)
                            @foreach($otherTasks as $otherTask)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <a href="{{ route('tasks.show', $otherTask->id) }}">{{ $otherTask->title }}</a>
                                            <p class="text-muted small mb-0">Échéance: {{ $otherTask->due_date->format('d/m/Y') }}</p>
                                        </div>
                                        @if($otherTask->priority == 'high')
                                            <span class="badge bg-danger align-self-start">Haute</span>
                                        @elseif($otherTask->priority == 'medium')
                                            <span class="badge bg-warning align-self-start">Moyenne</span>
                                        @else
                                            <span class="badge bg-success align-self-start">Basse</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item px-0">Aucune autre tâche en cours</li>
                        @endif
                    </ul>
                    
                    <div class="mt-3">
                        <a href="{{ route('team.show', $task->assignedTo->id) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user me-2"></i>Voir le profil
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Progression des tâches</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskProgressChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique de progression des tâches
    const progressCtx = document.getElementById('taskProgressChart').getContext('2d');
    const progressChart = new Chart(progressCtx, {
        type: 'doughnut',
        data: {
            labels: ['Complété', 'En cours', 'En attente'],
            datasets: [{
                data: [
                    {{ $task->assignedTo->tasks->where('status', 'completed')->count() }},
                    {{ $task->assignedTo->tasks->where('status', 'in_progress')->count() }},
                    {{ $task->assignedTo->tasks->where('status', 'pending')->count() }}
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
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