@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails de la Tâche</h1>
        <a href="{{ route('employee.tasks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
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
                            <span>Assigné par:</span>
                            <span class="text-primary">{{ $task->assignedBy->name }}</span>
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
                    
                    @if($task->status != 'completed' && $task->status != 'cancelled')
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>N'oubliez pas de mettre à jour le statut de la tâche une fois que vous avez commencé ou terminé.
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            @if($task->status != 'completed' && $task->status != 'cancelled')
            <form action="{{ route('employee.tasks.status', $task->id) }}" method="POST">
                @csrf
                
                <div class="d-flex justify-content-around">
                    @if($task->status == 'pending')
                        <button type="submit" name="status" value="in_progress" class="btn btn-primary">
                            <i class="fas fa-play me-2"></i>Marquer comme "En cours"
                        </button>
                    @endif
                    <button type="submit" name="status" value="completed" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Marquer comme "Terminé"
                    </button>
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
@endsection