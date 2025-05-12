<!-- resources/views/department_head/tasks.blade.php -->
@extends('layouts.app')

@section('title', 'Gestion des tâches')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestion des tâches</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportTasks">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                <i class="fas fa-plus me-1"></i> Nouvelle tâche
            </button>
        </div>
    </div>

    <!-- Vue d'ensemble -->
    <div class="row mb-4">
        <!-- Statistiques des tâches -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Vue d'ensemble</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-warning">
                                <span>{{ $pendingTasks->where('status', 'pending')->count() }}</span>
                            </div>
                            <h6 class="mt-2">En attente</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-info">
                                <span>{{ $pendingTasks->where('status', 'in_progress')->count() }}</span>
                            </div>
                            <h6 class="mt-2">En cours</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-success">
                                <span>{{ $completedTasks->count() }}</span>
                            </div>
                            <h6 class="mt-2">Terminées</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-danger">
                                <span>{{ $pendingTasks->where('status', 'delayed')->count() }}</span>
                            </div>
                            <h6 class="mt-2">Retardées</h6>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <canvas id="taskCompletionChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tâches par priorité -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tâches par priorité</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskPriorityChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tâches par personne -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tâches par personne</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskPerEmployeeChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tâches en cours et en attente -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tâches en cours et en attente</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchPendingTask" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchPendingBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="pendingTasksTable">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Assignée à</th>
                            <th>Priorité</th>
                            <th>Date d'échéance</th>
                            <th>Statut</th>
                            <th>Progression</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($pendingTasks) > 0)
                            @foreach($pendingTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light me-2">
                                            @if($task->assignee->profile_image)
                                                <img src="{{ asset('storage/' . $task->assignee->profile_image) }}" alt="{{ $task->assignee->name }}" class="avatar-img">
                                            @else
                                                <span class="avatar-text">{{ strtoupper(substr($task->assignee->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $task->assignee->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $priorityClass = 'bg-success';
                                        $priorityText = 'Basse';
                                        
                                        if ($task->priority === 'medium') {
                                            $priorityClass = 'bg-info';
                                            $priorityText = 'Moyenne';
                                        } elseif ($task->priority === 'high') {
                                            $priorityClass = 'bg-warning';
                                            $priorityText = 'Haute';
                                        } elseif ($task->priority === 'urgent') {
                                            $priorityClass = 'bg-danger';
                                            $priorityText = 'Urgente';
                                        }
                                    @endphp
                                    <span class="badge {{ $priorityClass }}">{{ $priorityText }}</span>
                                </td>
                                <td>
                                    @php
                                        $dueDate = new DateTime($task->due_date);
                                        $today = new DateTime();
                                        $interval = $today->diff($dueDate);
                                        $isLate = $dueDate < $today && $task->status !== 'completed';
                                        
                                        echo date('d/m/Y', strtotime($task->due_date));
                                        
                                        if ($isLate) {
                                            echo ' <span class="badge bg-danger">En retard</span>';
                                        } elseif ($interval->days <= 2 && $dueDate >= $today) {
                                            echo ' <span class="badge bg-warning">Bientôt</span>';
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'bg-secondary';
                                        $statusText = 'En attente';
                                        
                                        if ($task->status === 'in_progress') {
                                            $statusClass = 'bg-info';
                                            $statusText = 'En cours';
                                        } elseif ($task->status === 'completed') {
                                            $statusClass = 'bg-success';
                                            $statusText = 'Terminée';
                                        } elseif ($task->status === 'delayed') {
                                            $statusClass = 'bg-danger';
                                            $statusText = 'Retardée';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $task->progress }}%;" aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="small">{{ $task->progress }}%</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary edit-task-btn" 
                                                data-task-id="{{ $task->id }}"
                                                data-task-title="{{ $task->title }}"
                                                data-task-description="{{ $task->description }}"
                                                data-task-assignee="{{ $task->assigned_to }}"
                                                data-task-due-date="{{ $task->due_date }}"
                                                data-task-priority="{{ $task->priority }}"
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info view-task-btn"
                                                data-task-id="{{ $task->id }}"
                                                data-task-title="{{ $task->title }}"
                                                data-task-description="{{ $task->description }}"
                                                data-task-assignee-name="{{ $task->assignee->name }}"
                                                data-task-assigner-name="{{ $task->assigner->name }}"
                                                data-task-due-date="{{ date('d/m/Y', strtotime($task->due_date)) }}"
                                                data-task-priority="{{ $task->priority }}"
                                                data-task-status="{{ $task->status }}"
                                                data-task-progress="{{ $task->progress }}"
                                                data-task-created="{{ date('d/m/Y', strtotime($task->created_at)) }}"
                                                data-task-notes="{{ $task->notes }}"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success update-status-btn"
                                                data-task-id="{{ $task->id }}"
                                                data-task-title="{{ $task->title }}"
                                                data-task-status="{{ $task->status }}"
                                                data-task-progress="{{ $task->progress }}"
                                                title="Mettre à jour le statut">
                                            <i class="fas fa-tasks"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">Aucune tâche en cours ou en attente</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tâches terminées -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tâches terminées</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchCompletedTask" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchCompletedBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="completedTasksTable">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Assignée à</th>
                            <th>Priorité</th>
                            <th>Date d'échéance</th>
                            <th>Date d'achèvement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($completedTasks) > 0)
                            @foreach($completedTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light me-2">
                                            @if($task->assignee->profile_image)
                                                <img src="{{ asset('storage/' . $task->assignee->profile_image) }}" alt="{{ $task->assignee->name }}" class="avatar-img">
                                            @else
                                                <span class="avatar-text">{{ strtoupper(substr($task->assignee->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $task->assignee->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $priorityClass = 'bg-success';
                                        $priorityText = 'Basse';
                                        
                                        if ($task->priority === 'medium') {
                                            $priorityClass = 'bg-info';
                                            $priorityText = 'Moyenne';
                                        } elseif ($task->priority === 'high') {
                                            $priorityClass = 'bg-warning';
                                            $priorityText = 'Haute';
                                        } elseif ($task->priority === 'urgent') {
                                            $priorityClass = 'bg-danger';
                                            $priorityText = 'Urgente';
                                        }
                                    @endphp
                                    <span class="badge {{ $priorityClass }}">{{ $priorityText }}</span>
                                </td>
                                <td>{{ date('d/m/Y', strtotime($task->due_date)) }}</td>
                                <td>{{ date('d/m/Y', strtotime($task->updated_at)) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info view-task-btn"
                                            data-task-id="{{ $task->id }}"
                                            data-task-title="{{ $task->title }}"
                                            data-task-description="{{ $task->description }}"
                                            data-task-assignee-name="{{ $task->assignee->name }}"
                                            data-task-assigner-name="{{ $task->assigner->name }}"
                                            data-task-due-date="{{ date('d/m/Y', strtotime($task->due_date)) }}"
                                            data-task-priority="{{ $task->priority }}"
                                            data-task-status="{{ $task->status }}"
                                            data-task-progress="{{ $task->progress }}"
                                            data-task-created="{{ date('d/m/Y', strtotime($task->created_at)) }}"
                                            data-task-completed="{{ date('d/m/Y', strtotime($task->updated_at)) }}"
                                            data-task-notes="{{ $task->notes }}">
                                        <i class="fas fa-eye"></i> Détails
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Aucune tâche terminée</td>
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
</div>

<!-- Modal pour créer une tâche -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Créer une nouvelle tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department_head.create_task') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Titre de la tâche <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="taskTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="taskDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="taskAssignee" class="form-label">Assignée à <span class="text-danger">*</span></label>
                            <select class="form-select" id="taskAssignee" name="assigned_to" required>
                                <option value="">Sélectionner un employé</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="taskDueDate" class="form-label">Date d'échéance <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="taskDueDate" name="due_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="taskPriority" class="form-label">Priorité <span class="text-danger">*</span></label>
                            <select class="form-select" id="taskPriority" name="priority" required>
                                <option value="low">Basse</option>
                                <option value="medium" selected>Moyenne</option>
                                <option value="high">Haute</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="taskNotes" class="form-label">Notes additionnelles</label>
                            <input type="text" class="form-control" id="taskNotes" name="notes">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer la tâche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier une tâche -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Modifier la tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTaskForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editTaskTitle" class="form-label">Titre de la tâche <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editTaskTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editTaskDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editTaskAssignee" class="form-label">Assignée à <span class="text-danger">*</span></label>
                            <select class="form-select" id="editTaskAssignee" name="assigned_to" required>
                                <option value="">Sélectionner un employé</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editTaskDueDate" class="form-label">Date d'échéance <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editTaskDueDate" name="due_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editTaskPriority" class="form-label">Priorité <span class="text-danger">*</span></label>
                            <select class="form-select" id="editTaskPriority" name="priority" required>
                                <option value="low">Basse</option>
                                <option value="medium">Moyenne</option>
                                <option value="high">Haute</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editTaskNotes" class="form-label">Notes additionnelles</label>
                            <input type="text" class="form-control" id="editTaskNotes" name="notes">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails d'une tâche -->
<div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTaskModalLabel">Détails de la tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Informations générales</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Titre:</th>
                                <td id="viewTaskTitle"></td>
                            </tr>
                            <tr>
                                <th>Assignée à:</th>
                                <td id="viewTaskAssignee"></td>
                            </tr>
                            <tr>
                                <th>Créée par:</th>
                                <td id="viewTaskAssigner"></td>
                            </tr>
                            <tr>
                                <th>Date de création:</th>
                                <td id="viewTaskCreated"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Délais et priorité</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Date d'échéance:</th>
                                <td id="viewTaskDueDate"></td>
                            </tr>
                            <tr>
                                <th>Priorité:</th>
                                <td id="viewTaskPriority"></td>
                            </tr>
                            <tr>
                                <th>Statut:</th>
                                <td id="viewTaskStatus"></td>
                            </tr>
                            <tr id="viewTaskCompletedRow">
                                <th>Date d'achèvement:</th>
                                <td id="viewTaskCompleted"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Description</h6>
                        <div class="p-3 bg-light rounded" id="viewTaskDescription"></div>
                    </div>
                </div>
                
                <div class="row mb-4" id="viewTaskProgressContainer">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Progression</h6>
                        <div class="progress" style="height: 20px;">
                            <div id="viewTaskProgressBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-center mt-2">
                            <span id="viewTaskProgressText">0%</span>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="viewTaskNotesContainer">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Notes additionnelles</h6>
                        <div class="p-3 bg-light rounded" id="viewTaskNotes"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="editTaskBtn">Modifier</button>
                <button type="button" class="btn btn-success" id="updateStatusBtn">Mettre à jour le statut</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour mettre à jour le statut d'une tâche -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Mettre à jour le statut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="updateTaskTitle" class="form-label">Tâche</label>
                        <input type="text" class="form-control" id="updateTaskTitle" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="updateTaskStatus" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select" id="updateTaskStatus" name="status" required>
                            <option value="pending">En attente</option>
                            <option value="in_progress">En cours</option>
                            <option value="completed">Terminée</option>
                            <option value="delayed">Retardée</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="updateTaskProgress" class="form-label">Progression: <span id="progressValue">0</span>%</label>
                        <input type="range" class="form-range" id="updateTaskProgress" name="progress" min="0" max="100" step="5" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
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
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default due date to tomorrow for new tasks
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('taskDueDate').valueAsDate = tomorrow;
        
        // Initialize charts
        initTaskCompletionChart();
        initTaskPriorityChart();
        initTaskPerEmployeeChart();
        
        // Search functionality
        document.getElementById('searchPendingBtn').addEventListener('click', function() {
            searchTable('searchPendingTask', 'pendingTasksTable');
        });
        
        document.getElementById('searchPendingTask').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchTable('searchPendingTask', 'pendingTasksTable');
            }
        });
        
        document.getElementById('searchCompletedBtn').addEventListener('click', function() {
            searchTable('searchCompletedTask', 'completedTasksTable');
        });
        
        document.getElementById('searchCompletedTask').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchTable('searchCompletedTask', 'completedTasksTable');
            }
        });
        
        // Export data
        document.getElementById('exportTasks').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en cours de développement...');
        });
        
        // Load more completed tasks
        document.getElementById('loadMoreCompleted').addEventListener('click', function() {
            alert('Fonctionnalité en cours de développement...');
        });
        
        // Update progress value when range is moved
        document.getElementById('updateTaskProgress').addEventListener('input', function() {
            document.getElementById('progressValue').textContent = this.value;
        });
        
        // Edit task button
        document.querySelectorAll('.edit-task-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const taskId = this.getAttribute('data-task-id');
                const taskTitle = this.getAttribute('data-task-title');
                const taskDescription = this.getAttribute('data-task-description');
                const taskAssignee = this.getAttribute('data-task-assignee');
                const taskDueDate = this.getAttribute('data-task-due-date');
                const taskPriority = this.getAttribute('data-task-priority');
                
                // Set values in modal
                document.getElementById('editTaskTitle').value = taskTitle;
                document.getElementById('editTaskDescription').value = taskDescription;
                document.getElementById('editTaskAssignee').value = taskAssignee;
                document.getElementById('editTaskDueDate').value = taskDueDate;
                document.getElementById('editTaskPriority').value = taskPriority;
                
                // Set form action
                document.getElementById('editTaskForm').action = ''; // Add your edit task route here
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                modal.show();
            });
        });
        
        // View task details
        document.querySelectorAll('.view-task-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const taskId = this.getAttribute('data-task-id');
                const taskTitle = this.getAttribute('data-task-title');
                const taskDescription = this.getAttribute('data-task-description') || 'Aucune description fournie';
                const taskAssigneeName = this.getAttribute('data-task-assignee-name');
                const taskAssignerName = this.getAttribute('data-task-assigner-name');
                const taskDueDate = this.getAttribute('data-task-due-date');
                const taskPriority = this.getAttribute('data-task-priority');
                const taskStatus = this.getAttribute('data-task-status');
                const taskProgress = this.getAttribute('data-task-progress');
                const taskCreated = this.getAttribute('data-task-created');
                const taskCompleted = this.getAttribute('data-task-completed');
                const taskNotes = this.getAttribute('data-task-notes');
                
                // Set values in modal
                document.getElementById('viewTaskTitle').textContent = taskTitle;
                document.getElementById('viewTaskAssignee').textContent = taskAssigneeName;
                document.getElementById('viewTaskAssigner').textContent = taskAssignerName;
                document.getElementById('viewTaskCreated').textContent = taskCreated;
                document.getElementById('viewTaskDueDate').textContent = taskDueDate;
                document.getElementById('viewTaskDescription').textContent = taskDescription;
                
                // Set priority with badge
                const priorityElement = document.getElementById('viewTaskPriority');
                priorityElement.innerHTML = '';
                
                let priorityClass = 'bg-success';
                let priorityText = 'Basse';
                
                if (taskPriority === 'medium') {
                    priorityClass = 'bg-info';
                    priorityText = 'Moyenne';
                } else if (taskPriority === 'high') {
                    priorityClass = 'bg-warning';
                    priorityText = 'Haute';
                } else if (taskPriority === 'urgent') {
                    priorityClass = 'bg-danger';
                    priorityText = 'Urgente';
                }
                
                priorityElement.innerHTML = `<span class="badge ${priorityClass}">${priorityText}</span>`;
                
                // Set status with badge
                const statusElement = document.getElementById('viewTaskStatus');
                statusElement.innerHTML = '';
                
                let statusClass = 'bg-secondary';
                let statusText = 'En attente';
                
                if (taskStatus === 'in_progress') {
                    statusClass = 'bg-info';
                    statusText = 'En cours';
                } else if (taskStatus === 'completed') {
                    statusClass = 'bg-success';
                    statusText = 'Terminée';
                } else if (taskStatus === 'delayed') {
                    statusClass = 'bg-danger';
                    statusText = 'Retardée';
                }
                
                statusElement.innerHTML = `<span class="badge ${statusClass}">${statusText}</span>`;
                
                // Show/hide completed date
                const completedRow = document.getElementById('viewTaskCompletedRow');
                const completedElement = document.getElementById('viewTaskCompleted');
                
                if (taskCompleted && taskStatus === 'completed') {
                    completedRow.style.display = 'table-row';
                    completedElement.textContent = taskCompleted;
                } else {
                    completedRow.style.display = 'none';
                }
                
                // Set progress bar
                const progressContainer = document.getElementById('viewTaskProgressContainer');
                const progressBar = document.getElementById('viewTaskProgressBar');
                const progressText = document.getElementById('viewTaskProgressText');
                
                if (taskStatus === 'completed') {
                    progressContainer.style.display = 'none';
                } else {
                    progressContainer.style.display = 'block';
                    progressBar.style.width = taskProgress + '%';
                    progressBar.setAttribute('aria-valuenow', taskProgress);
                    progressText.textContent = taskProgress + '%';
                }
                
                // Set notes
                const notesContainer = document.getElementById('viewTaskNotesContainer');
                const notesElement = document.getElementById('viewTaskNotes');
                
                if (taskNotes) {
                    notesContainer.style.display = 'flex';
                    notesElement.textContent = taskNotes;
                } else {
                    notesContainer.style.display = 'none';
                }
                
                // Set button actions
                document.getElementById('editTaskBtn').setAttribute('data-task-id', taskId);
                document.getElementById('updateStatusBtn').setAttribute('data-task-id', taskId);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('viewTaskModal'));
                modal.show();
            });
        });
        
        // Update task status button
        document.querySelectorAll('.update-status-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const taskId = this.getAttribute('data-task-id');
                const taskTitle = this.getAttribute('data-task-title');
                const taskStatus = this.getAttribute('data-task-status');
                const taskProgress = this.getAttribute('data-task-progress');
                
                // Set values in modal
                document.getElementById('updateTaskTitle').value = taskTitle;
                document.getElementById('updateTaskStatus').value = taskStatus;
                document.getElementById('updateTaskProgress').value = taskProgress;
                document.getElementById('progressValue').textContent = taskProgress;
                
                // Set form action
                document.getElementById('updateStatusForm').action = "{{ route('department_head.update_task', ['id' => '']) }}" + taskId;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                modal.show();
            });
        });
        
        // Modal buttons for view task
        document.getElementById('editTaskBtn').addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            // Implement edit task functionality
            alert('Fonctionnalité d\'édition en développement pour la tâche #' + taskId);
        });
        
        document.getElementById('updateStatusBtn').addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            
            // Find the update-status-btn with this task-id and trigger a click
            document.querySelector(`.update-status-btn[data-task-id="${taskId}"]`).click();
            
            // Close the view modal
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewTaskModal'));
            viewModal.hide();
        });
    });

    function searchTable(inputId, tableId) {
        const searchValue = document.getElementById(inputId).value.toLowerCase();
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const taskTitle = rows[i].cells[0].textContent.toLowerCase();
            const employeeName = rows[i].cells[1].textContent.toLowerCase();
            
            if (taskTitle.includes(searchValue) || employeeName.includes(searchValue)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function initTaskCompletionChart() {
        var ctx = document.getElementById('taskCompletionChart').getContext('2d');
        
        // Sample data for task completion rate
        // In a real application, this would come from the backend
        var data = {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai'],
            datasets: [{
                label: 'Taux d\'achèvement (%)',
                data: [65, 72, 78, 82, 85],
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                tension: 0.3
            }]
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
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    function initTaskPriorityChart() {
        var ctx = document.getElementById('taskPriorityChart').getContext('2d');
        
        // Sample data for task priority distribution
        // In a real application, this would come from the backend
        var data = {
            labels: ['Basse', 'Moyenne', 'Haute', 'Urgente'],
            datasets: [{
                data: [15, 45, 30, 10],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(13, 202, 240, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(25, 135, 84, 1)',
                    'rgba(13, 202, 240, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        };
        
        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    function initTaskPerEmployeeChart() {
        var ctx = document.getElementById('taskPerEmployeeChart').getContext('2d');
        
        // Sample data for tasks per employee
        // In a real application, this would come from the backend
        var data = {
            labels: ['Ahmed', 'Fatima', 'Omar', 'Samira', 'Karim'],
            datasets: [
                {
                    label: 'Terminées',
                    data: [3, 5, 2, 4, 3],
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                },
                {
                    label: 'En cours',
                    data: [2, 1, 3, 2, 1],
                    backgroundColor: 'rgba(13, 202, 240, 0.7)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 1
                },
                {
                    label: 'En attente',
                    data: [1, 2, 1, 1, 2],
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
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
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
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