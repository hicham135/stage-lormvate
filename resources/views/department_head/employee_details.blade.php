<!-- resources/views/department_head/employee_details.blade.php -->
@extends('layouts.app')

@section('title', 'Détails de l\'employé')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Profil de l'employé</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('department_head.team_management') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportBtn">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="actionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog me-1"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown">
                    <li>
                        <a class="dropdown-item create-task" href="#" 
                           data-employee-id="{{ $employee->id }}" 
                           data-employee-name="{{ $employee->name }}">
                            <i class="fas fa-tasks me-2"></i> Assigner une tâche
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item create-evaluation" href="#"
                           data-employee-id="{{ $employee->id }}" 
                           data-employee-name="{{ $employee->name }}">
                            <i class="fas fa-star me-2"></i> Évaluer
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="mailto:{{ $employee->email }}">
                            <i class="fas fa-envelope me-2"></i> Envoyer un email
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Profil de l'employé -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="avatar-profile mb-3 mx-auto">
                        @if($employee->profile_image)
                            <img src="{{ asset('storage/' . $employee->profile_image) }}" alt="{{ $employee->name }}" class="avatar-img-lg">
                        @else
                            <div class="avatar-placeholder bg-light">
                                <span class="avatar-text-lg">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $employee->name }}</h4>
                    <p class="text-muted mb-3">{{ $employee->email }}</p>
                    
                    <div class="border-top pt-3 mt-3">
                        <div class="row">
                            <div class="col-6 text-center border-end">
                                <h6 class="text-muted mb-1">Téléphone</h6>
                                <p class="mb-0">{{ $employee->phone ?? 'Non défini' }}</p>
                            </div>
                            <div class="col-6 text-center">
                                <h6 class="text-muted mb-1">Date d'embauche</h6>
                                <p class="mb-0">{{ $employee->start_date ? date('d/m/Y', strtotime($employee->start_date)) : 'Non définie' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 mt-3">
                        <h6 class="text-muted mb-2">Adresse</h6>
                        <p class="mb-3">{{ $employee->address ?? 'Non définie' }}</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $employee->email }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i> Contacter
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques rapides -->
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body pb-0">
                    <div class="row">
                        <!-- Taux de présence -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">Taux de présence</h6>
                                        <span class="badge bg-primary">30 derniers jours</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">85%</div>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span>Présent: 17 jours</span>
                                        <span>Absent: 3 jours</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Taux d'accomplissement des tâches -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">Tâches accomplies</h6>
                                        <span class="badge bg-primary">Ce mois</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span>Terminées: 9 tâches</span>
                                        <span>En cours: 3 tâches</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Évaluation moyenne -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">Évaluation moyenne</h6>
                                        <span class="badge bg-primary">Globale</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="rating-large mb-2">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        </div>
                                        <h3 class="mb-0">4.5/5</h3>
                                        <small class="text-muted">Basé sur {{ count($evaluations) }} évaluations</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Congés restants -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">Congés</h6>
                                        <span class="badge bg-primary">Cette année</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <div class="progress-circle">
                                                <svg width="100" height="100" viewBox="0 0 36 36">
                                                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                    <path class="circle" stroke-dasharray="75, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                    <text x="18" y="20.35" class="percentage">75%</text>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-muted">15 jours restants / 20 jours totaux</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs avec les détails -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white p-0">
            <ul class="nav nav-tabs" id="employeeTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab" aria-controls="tasks" aria-selected="true">Tâches</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false">Présence</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave" type="button" role="tab" aria-controls="leave" aria-selected="false">Congés</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="evaluations-tab" data-bs-toggle="tab" data-bs-target="#evaluations" type="button" role="tab" aria-controls="evaluations" aria-selected="false">Évaluations</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="employeeTabContent">
                <!-- Tab Tâches -->
                <div class="tab-pane fade show active" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Tâches assignées</h5>
                        <button class="btn btn-sm btn-primary create-task" 
                                data-employee-id="{{ $employee->id }}" 
                                data-employee-name="{{ $employee->name }}">
                            <i class="fas fa-plus me-1"></i> Assigner une tâche
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre</th>
                                    <th>Priorité</th>
                                    <th>Date d'échéance</th>
                                    <th>Statut</th>
                                    <th>Progression</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($tasks) > 0)
                                    @foreach($tasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
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
                                                <button type="button" class="btn btn-outline-primary update-task-btn" 
                                                        data-task-id="{{ $task->id }}"
                                                        data-task-title="{{ $task->title }}"
                                                        data-task-status="{{ $task->status }}"
                                                        data-task-progress="{{ $task->progress }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info view-task-btn"
                                                        data-task-id="{{ $task->id }}"
                                                        data-task-title="{{ $task->title }}"
                                                        data-task-description="{{ $task->description }}"
                                                        data-task-due-date="{{ date('d/m/Y', strtotime($task->due_date)) }}"
                                                        data-task-priority="{{ $task->priority }}"
                                                        data-task-status="{{ $task->status }}"
                                                        data-task-progress="{{ $task->progress }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Aucune tâche assignée</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Tab Présence -->
                <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Historique de présence</h5>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="month" class="form-control form-control-sm" id="attendanceMonth">
                            <button class="btn btn-sm btn-outline-primary" type="button" id="filterAttendance">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Statut</th>
                                    <th>Heures supp.</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($attendances) > 0)
                                    @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{ date('d/m/Y', strtotime($attendance->check_in)) }}</td>
                                        <td>{{ date('H:i', strtotime($attendance->check_in)) }}</td>
                                        <td>{{ $attendance->check_out ? date('H:i', strtotime($attendance->check_out)) : '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = 'bg-success';
                                                $statusText = 'Présent';
                                                
                                                if ($attendance->status === 'late') {
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'En retard';
                                                } elseif ($attendance->status === 'absent') {
                                                    $statusClass = 'bg-danger';
                                                    $statusText = 'Absent';
                                                } elseif ($attendance->status === 'leave') {
                                                    $statusClass = 'bg-info';
                                                    $statusText = 'Congé';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            @if($attendance->overtime_hours > 0)
                                                <span class="badge {{ $attendance->overtime_approved ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $attendance->overtime_hours }} h 
                                                    @if(!$attendance->overtime_approved)
                                                        <i class="fas fa-clock ms-1" title="En attente d'approbation"></i>
                                                    @endif
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $attendance->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Aucun enregistrement de présence</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Tab Congés -->
                <div class="tab-pane fade" id="leave" role="tabpanel" aria-labelledby="leave-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Demandes de congés</h5>
                        <div class="input-group" style="max-width: 300px;">
                            <select class="form-select form-select-sm" id="leaveStatus">
                                <option value="all">Tous les statuts</option>
                                <option value="pending">En attente</option>
                                <option value="approved">Approuvé</option>
                                <option value="rejected">Rejeté</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary" type="button" id="filterLeaves">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Période</th>
                                    <th>Durée</th>
                                    <th>Raison</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($leaveRequests) > 0)
                                    @foreach($leaveRequests as $request)
                                    <tr>
                                        <td>
                                            @php
                                                $typeText = 'Congé annuel';
                                                
                                                if ($request->type === 'sick') {
                                                    $typeText = 'Congé maladie';
                                                } elseif ($request->type === 'personal') {
                                                    $typeText = 'Congé personnel';
                                                } elseif ($request->type === 'other') {
                                                    $typeText = 'Autre congé';
                                                }
                                            @endphp
                                            {{ $typeText }}
                                        </td>
                                        <td>{{ date('d/m/Y', strtotime($request->start_date)) }} - {{ date('d/m/Y', strtotime($request->end_date)) }}</td>
                                        <td>
                                            @php
                                                $start = new DateTime($request->start_date);
                                                $end = new DateTime($request->end_date);
                                                $interval = $start->diff($end);
                                                echo $interval->days + 1 . ' jour(s)';
                                            @endphp
                                        </td>
                                        <td>{{ Str::limit($request->reason, 30) }}</td>
                                        <td>
                                            @php
                                                $statusClass = 'bg-warning';
                                                $statusText = 'En attente';
                                                
                                                if ($request->status === 'approved') {
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Approuvé';
                                                } elseif ($request->status === 'rejected') {
                                                    $statusClass = 'bg-danger';
                                                    $statusText = 'Rejeté';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            @if($request->status === 'pending')
                                                <div class="btn-group btn-group-sm">
                                                    <form action="{{ route('department_head.approve_leave', ['id' => $request->id]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success" title="Approuver">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-outline-danger reject-leave" 
                                                            data-leave-id="{{ $request->id }}" title="Rejeter">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-info view-leave"
                                                            data-leave-id="{{ $request->id }}"
                                                            data-leave-type="{{ $typeText }}"
                                                            data-leave-start="{{ date('d/m/Y', strtotime($request->start_date)) }}"
                                                            data-leave-end="{{ date('d/m/Y', strtotime($request->end_date)) }}"
                                                            data-leave-reason="{{ $request->reason }}"
                                                            data-leave-doc="{{ $request->document_path }}"
                                                            title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-info view-leave"
                                                        data-leave-id="{{ $request->id }}"
                                                        data-leave-type="{{ $typeText }}"
                                                        data-leave-start="{{ date('d/m/Y', strtotime($request->start_date)) }}"
                                                        data-leave-end="{{ date('d/m/Y', strtotime($request->end_date)) }}"
                                                        data-leave-reason="{{ $request->reason }}"
                                                        data-leave-doc="{{ $request->document_path }}"
                                                        title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Aucune demande de congé</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Tab Évaluations -->
                <div class="tab-pane fade" id="evaluations" role="tabpanel" aria-labelledby="evaluations-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Évaluations de performance</h5>
                        <button class="btn btn-sm btn-primary create-evaluation" 
                                data-employee-id="{{ $employee->id }}" 
                                data-employee-name="{{ $employee->name }}">
                            <i class="fas fa-plus me-1"></i> Nouvelle évaluation
                        </button>
                    </div>
                    
                    <div id="evaluation-chart-container" class="mb-4" style="height: 250px;">
                        <canvas id="evaluationChart"></canvas>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Période</th>
                                    <th>Performance</th>
                                    <th>Ponctualité</th>
                                    <th>Travail d'équipe</th>
                                    <th>Initiative</th>
                                    <th>Moyenne</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($evaluations) > 0)
                                    @foreach($evaluations as $evaluation)
                                    <tr>
                                        <td>{{ $evaluation->period }}</td>
                                        <td>{{ $evaluation->performance_score }}/5</td>
                                        <td>{{ $evaluation->punctuality_score }}/5</td>
                                        <td>{{ $evaluation->teamwork_score }}/5</td>
                                        <td>{{ $evaluation->initiative_score }}/5</td>
                                        <td>
                                            @php
                                                $average = ($evaluation->performance_score + $evaluation->punctuality_score + $evaluation->teamwork_score + $evaluation->initiative_score) / 4;
                                                echo number_format($average, 1) . '/5';
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'bg-secondary';
                                                $statusText = 'Brouillon';
                                                
                                                if ($evaluation->status === 'submitted') {
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Soumise';
                                                } elseif ($evaluation->status === 'acknowledged') {
                                                    $statusClass = 'bg-info';
                                                    $statusText = 'Reconnue';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('department_head.edit_evaluation', ['id' => $evaluation->id]) }}" class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-info view-evaluation"
                                                        data-eval-id="{{ $evaluation->id }}"
                                                        data-eval-period="{{ $evaluation->period }}"
                                                        data-eval-perf="{{ $evaluation->performance_score }}"
                                                        data-eval-punct="{{ $evaluation->punctuality_score }}"
                                                        data-eval-team="{{ $evaluation->teamwork_score }}"
                                                        data-eval-init="{{ $evaluation->initiative_score }}"
                                                        data-eval-comments="{{ $evaluation->comments }}"
                                                        data-eval-goals="{{ $evaluation->goals }}"
                                                        title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-3 text-muted">Aucune évaluation</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails d'une tâche -->
<div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTaskModalLabel">Détails de la tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Titre:</label>
                    <p id="viewTaskTitle"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description:</label>
                    <p id="viewTaskDescription"></p>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Date d'échéance:</label>
                        <p id="viewTaskDueDate"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Priorité:</label>
                        <p id="viewTaskPriority"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Statut:</label>
                        <p id="viewTaskStatus"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Progression:</label>
                        <div class="progress" style="height: 20px;">
                            <div id="viewTaskProgressBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="small" id="viewTaskProgressText">0%</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour mettre à jour le statut d'une tâche -->
<div class="modal fade" id="updateTaskModal" tabindex="-1" aria-labelledby="updateTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateTaskModalLabel">Mettre à jour la tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateTaskForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Titre de la tâche</label>
                        <input type="text" class="form-control" id="updateTaskTitle" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="taskStatus" class="form-label">Statut</label>
                        <select class="form-select" id="updateTaskStatus" name="status">
                            <option value="pending">En attente</option>
                            <option value="in_progress">En cours</option>
                            <option value="completed">Terminée</option>
                            <option value="delayed">Retardée</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="taskProgress" class="form-label">Progression: <span id="progressValue">0</span>%</label>
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

<!-- Modal pour voir les détails d'un congé -->
<div class="modal fade" id="viewLeaveModal" tabindex="-1" aria-labelledby="viewLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewLeaveModalLabel">Détails de la demande de congé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Type de congé:</label>
                    <p id="viewLeaveType"></p>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Date de début:</label>
                        <p id="viewLeaveStart"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Date de fin:</label>
                        <p id="viewLeaveEnd"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Raison:</label>
                    <p id="viewLeaveReason"></p>
                </div>
                <div class="mb-3" id="viewLeaveDocContainer">
                    <label class="form-label fw-bold">Document justificatif:</label>
                    <div id="viewLeaveDoc"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour rejeter un congé -->
<div class="modal fade" id="rejectLeaveModal" tabindex="-1" aria-labelledby="rejectLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectLeaveModalLabel">Rejeter la demande de congé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectLeaveForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails d'une évaluation -->
<div class="modal fade" id="viewEvaluationModal" tabindex="-1" aria-labelledby="viewEvaluationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEvaluationModalLabel">Détails de l'évaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Période:</label>
                    <p id="viewEvalPeriod"></p>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title">Performance</h6>
                                <div class="display-5 text-primary mb-2" id="viewEvalPerf">0</div>
                                <div id="viewEvalPerfStars" class="text-warning">
                                    <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title">Ponctualité</h6>
                                <div class="display-5 text-primary mb-2" id="viewEvalPunct">0</div>
                                <div id="viewEvalPunctStars" class="text-warning">
                                    <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title">Travail d'équipe</h6>
                                <div class="display-5 text-primary mb-2" id="viewEvalTeam">0</div>
                                <div id="viewEvalTeamStars" class="text-warning">
                                    <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title">Initiative</h6>
                                <div class="display-5 text-primary mb-2" id="viewEvalInit">0</div>
                                <div id="viewEvalInitStars" class="text-warning">
                                    <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Commentaires:</label>
                    <div class="p-3 bg-light rounded" id="viewEvalComments"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Objectifs:</label>
                    <div class="p-3 bg-light rounded" id="viewEvalGoals"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour créer une tâche -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Assigner une nouvelle tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department_head.create_task') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="assigned_to" id="taskEmployeeId">
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
                            <label for="taskDueDate" class="form-label">Date d'échéance <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="taskDueDate" name="due_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="taskPriority" class="form-label">Priorité <span class="text-danger">*</span></label>
                            <select class="form-select" id="taskPriority" name="priority" required>
                                <option value="low">Basse</option>
                                <option value="medium" selected>Moyenne</option>
                                <option value="high">Haute</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner la tâche</button>
                </div>
            </form>
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
                    <input type="hidden" name="evaluatee_id" id="evaluationEmployeeId">
                    <div class="mb-3">
                        <label for="employeeName" class="form-label">Employé</label>
                        <input type="text" class="form-control" id="employeeName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="evaluationPeriod" class="form-label">Période d'évaluation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="evaluationPeriod" name="period" placeholder="Ex: Mai 2025" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer l'évaluation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-profile {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
    }
    .avatar-img-lg {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .avatar-text-lg {
        font-size: 48px;
        font-weight: bold;
    }
    .rating-large {
        font-size: 24px;
    }
    .progress-circle {
        position: relative;
        width: 100px;
        height: 100px;
    }
    .circle-bg {
        fill: none;
        stroke: #e9ecef;
        stroke-width: 3.8;
    }
    .circle {
        fill: none;
        stroke: #007bff;
        stroke-width: 3.8;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: center;
    }
    .percentage {
        font-family: sans-serif;
        font-size: 0.5em;
        text-anchor: middle;
        font-weight: bold;
        fill: #007bff;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize evaluation chart
        initEvaluationChart();

        // Set the date for attendance month filter to current month
        var now = new Date();
        var month = now.getMonth() + 1;
        var year = now.getFullYear();
        document.getElementById('attendanceMonth').value = year + '-' + (month < 10 ? '0' + month : month);

        // Update task progress value when range is moved
        document.getElementById('updateTaskProgress').addEventListener('input', function() {
            document.getElementById('progressValue').textContent = this.value;
        });

        // View task details
        document.querySelectorAll('.view-task-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var taskId = this.getAttribute('data-task-id');
                var taskTitle = this.getAttribute('data-task-title');
                var taskDescription = this.getAttribute('data-task-description') || 'Aucune description';
                var taskDueDate = this.getAttribute('data-task-due-date');
                var taskPriority = this.getAttribute('data-task-priority');
                var taskStatus = this.getAttribute('data-task-status');
                var taskProgress = this.getAttribute('data-task-progress');
                
                // Set values in modal
                document.getElementById('viewTaskTitle').textContent = taskTitle;
                document.getElementById('viewTaskDescription').textContent = taskDescription;
                document.getElementById('viewTaskDueDate').textContent = taskDueDate;
                
                // Set priority
                var priorityText = 'Basse';
                if (taskPriority === 'medium') priorityText = 'Moyenne';
                else if (taskPriority === 'high') priorityText = 'Haute';
                else if (taskPriority === 'urgent') priorityText = 'Urgente';
                document.getElementById('viewTaskPriority').textContent = priorityText;
                
                // Set status
                var statusText = 'En attente';
                if (taskStatus === 'in_progress') statusText = 'En cours';
                else if (taskStatus === 'completed') statusText = 'Terminée';
                else if (taskStatus === 'delayed') statusText = 'Retardée';
                document.getElementById('viewTaskStatus').textContent = statusText;
                
                // Set progress
                document.getElementById('viewTaskProgressBar').style.width = taskProgress + '%';
                document.getElementById('viewTaskProgressBar').setAttribute('aria-valuenow', taskProgress);
                document.getElementById('viewTaskProgressText').textContent = taskProgress + '%';
                
                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('viewTaskModal'));
                modal.show();
            });
        });

        // Update task
        document.querySelectorAll('.update-task-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var taskId = this.getAttribute('data-task-id');
                var taskTitle = this.getAttribute('data-task-title');
                var taskStatus = this.getAttribute('data-task-status');
                var taskProgress = this.getAttribute('data-task-progress');
                
                // Set values in modal
                document.getElementById('updateTaskTitle').value = taskTitle;
                document.getElementById('updateTaskStatus').value = taskStatus;
                document.getElementById('updateTaskProgress').value = taskProgress;
                document.getElementById('progressValue').textContent = taskProgress;
                
                // Set form action
                document.getElementById('updateTaskForm').action = "{{ route('department_head.update_task', ['id' => '']) }}" + taskId;
                
                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('updateTaskModal'));
                modal.show();
            });
        });

        // View leave details
        document.querySelectorAll('.view-leave').forEach(function(button) {
            button.addEventListener('click', function() {
                var leaveId = this.getAttribute('data-leave-id');
                var leaveType = this.getAttribute('data-leave-type');
                var leaveStart = this.getAttribute('data-leave-start');
                var leaveEnd = this.getAttribute('data-leave-end');
                var leaveReason = this.getAttribute('data-leave-reason') || 'Aucune raison fournie';
                var leaveDoc = this.getAttribute('data-leave-doc');
                
                // Set values in modal
                document.getElementById('viewLeaveType').textContent = leaveType;
                document.getElementById('viewLeaveStart').textContent = leaveStart;
                document.getElementById('viewLeaveEnd').textContent = leaveEnd;
                document.getElementById('viewLeaveReason').textContent = leaveReason;
                
                // Handle document
                var docContainer = document.getElementById('viewLeaveDocContainer');
                var docElement = document.getElementById('viewLeaveDoc');
                
                if (leaveDoc) {
                    docContainer.style.display = 'block';
                    docElement.innerHTML = '<a href="' + "{{ asset('storage') }}/" + leaveDoc + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-alt me-1"></i> Voir le document</a>';
                } else {
                    docContainer.style.display = 'none';
                }
                
                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('viewLeaveModal'));
                modal.show();
            });
        });

        // Reject leave
        document.querySelectorAll('.reject-leave').forEach(function(button) {
            button.addEventListener('click', function() {
                var leaveId = this.getAttribute('data-leave-id');
                
                // Set form action
                document.getElementById('rejectLeaveForm').action = "{{ route('department_head.reject_leave', ['id' => '']) }}" + leaveId;
                
                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('rejectLeaveModal'));
                modal.show();
            });
        });

        // View evaluation details
        document.querySelectorAll('.view-evaluation').forEach(function(button) {
            button.addEventListener('click', function() {
                var evalId = this.getAttribute('data-eval-id');
                var evalPeriod = this.getAttribute('data-eval-period');
                var evalPerf = this.getAttribute('data-eval-perf');
                var evalPunct = this.getAttribute('data-eval-punct');
                var evalTeam = this.getAttribute('data-eval-team');
                var evalInit = this.getAttribute('data-eval-init');
                var evalComments = this.getAttribute('data-eval-comments') || 'Aucun commentaire';
                var evalGoals = this.getAttribute('data-eval-goals') || 'Aucun objectif défini';
                
                // Set values in modal
                document.getElementById('viewEvalPeriod').textContent = evalPeriod;
                document.getElementById('viewEvalPerf').textContent = evalPerf + '/5';
                document.getElementById('viewEvalPunct').textContent = evalPunct + '/5';
                document.getElementById('viewEvalTeam').textContent = evalTeam + '/5';
                document.getElementById('viewEvalInit').textContent = evalInit + '/5';
                document.getElementById('viewEvalComments').textContent = evalComments;
                document.getElementById('viewEvalGoals').textContent = evalGoals;
                
                // Set stars
                setStars('viewEvalPerfStars', evalPerf);
                setStars('viewEvalPunctStars', evalPunct);
                setStars('viewEvalTeamStars', evalTeam);
                setStars('viewEvalInitStars', evalInit);
                
                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('viewEvaluationModal'));
                modal.show();
            });
        });

        // Create task button handlers
        document.querySelectorAll('.create-task').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                var employeeId = this.getAttribute('data-employee-id');
                var employeeName = this.getAttribute('data-employee-name');
                
                document.getElementById('taskEmployeeId').value = employeeId;
                document.getElementById('createTaskModalLabel').textContent = 
                    'Assigner une tâche à ' + employeeName;
                
                // Set due date to tomorrow by default
                var tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('taskDueDate').valueAsDate = tomorrow;
                
                var modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
                modal.show();
            });
        });
        
        // Create evaluation button handlers
        document.querySelectorAll('.create-evaluation').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                var employeeId = this.getAttribute('data-employee-id');
                var employeeName = this.getAttribute('data-employee-name');
                
                document.getElementById('evaluationEmployeeId').value = employeeId;
                document.getElementById('employeeName').value = employeeName;
                
                // Set default period to current month and year
                var now = new Date();
                var months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                document.getElementById('evaluationPeriod').value = months[now.getMonth()] + ' ' + now.getFullYear();
                
                var modal = new bootstrap.Modal(document.getElementById('createEvaluationModal'));
                modal.show();
            });
        });
    });

    function setStars(containerId, rating) {
        var container = document.getElementById(containerId);
        container.innerHTML = '';
        
        for (var i = 1; i <= 5; i++) {
            if (i <= Math.floor(rating)) {
                container.innerHTML += '<i class="fas fa-star"></i>';
            } else if (i - 0.5 <= rating) {
                container.innerHTML += '<i class="fas fa-star-half-alt"></i>';
            } else {
                container.innerHTML += '<i class="far fa-star"></i>';
            }
        }
    }

    function initEvaluationChart() {
        var ctx = document.getElementById('evaluationChart').getContext('2d');
        
        // Get evaluation data (in a real application, this would come from the backend)
        // For demo purposes, we're using sample data
        var evaluations = [
            { period: 'Janv 2025', performance: 4, punctuality: 3, teamwork: 4, initiative: 3 },
            { period: 'Févr 2025', performance: 4, punctuality: 4, teamwork: 4, initiative: 3 },
            { period: 'Mars 2025', performance: 4, punctuality: 4, teamwork: 5, initiative: 4 },
            { period: 'Avr 2025', performance: 5, punctuality: 4, teamwork: 5, initiative: 4 }
        ];
        
        var labels = evaluations.map(function(eval) { return eval.period; });
        var performanceData = evaluations.map(function(eval) { return eval.performance; });
        var punctualityData = evaluations.map(function(eval) { return eval.punctuality; });
        var teamworkData = evaluations.map(function(eval) { return eval.teamwork; });
        var initiativeData = evaluations.map(function(eval) { return eval.initiative; });
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Performance',
                        data: performanceData,
                        borderColor: 'rgba(13, 110, 253, 1)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.3
                    },
                    {
                        label: 'Ponctualité',
                        data: punctualityData,
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.3
                    },
                    {
                        label: 'Travail d\'équipe',
                        data: teamworkData,
                        borderColor: 'rgba(25, 135, 84, 1)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.3
                    },
                    {
                        label: 'Initiative',
                        data: initiativeData,
                        borderColor: 'rgba(255, 193, 7, 1)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.3
                    }
                ]
            },
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