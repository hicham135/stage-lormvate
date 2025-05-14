@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mt-2 mb-4">Tableau de Bord</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white">Bienvenue</h6>
                            <h3 class="text-white">{{ $employee->name }}</h3>
                        </div>
                        <i class="fas fa-user fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white">Tâches en cours</h6>
                            <h3 class="text-white">{{ $pendingTasks }}</h3>
                        </div>
                        <i class="fas fa-tasks fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white">Demandes en attente</h6>
                            <h3 class="text-white">{{ $pendingRequests }}</h3>
                        </div>
                        <i class="fas fa-paper-plane fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $todayAttendance && $todayAttendance->check_in ? 'bg-info' : 'bg-danger' }} text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white">Statut aujourd'hui</h6>
                            <h3 class="text-white">{{ $todayAttendance && $todayAttendance->check_in ? 'Présent' : 'Non pointé' }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
                @if(!$todayAttendance || !$todayAttendance->check_in)
                <div class="card-footer bg-transparent border-top-0">
                    <a href="{{ route('employee.attendance.index') }}" class="btn btn-sm btn-light w-100">
                        <i class="fas fa-arrow-right me-2"></i>Pointer mon arrivée
                    </a>
                </div>
                @elseif(!$todayAttendance->check_out)
                <div class="card-footer bg-transparent border-top-0">
                    <a href="{{ route('employee.attendance.index') }}" class="btn btn-sm btn-light w-100">
                        <i class="fas fa-arrow-right me-2"></i>Pointer mon départ
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes tâches récentes</h5>
                    <a href="{{ route('employee.tasks.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    @php
                        $recentTasks = \App\Models\Task::where('assigned_to', $employee->id)
                                                    ->orderBy('created_at', 'desc')
                                                    ->take(3)
                                                    ->get();
                    @endphp

                    @if($recentTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                    @foreach($recentTasks as $task)
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
                                        <td>{{ $task->due_date->format('d/m/Y') }}</td>
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
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Aucune tâche récente</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes demandes récentes</h5>
                    <a href="{{ route('employee.requests.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    @php
                        $recentRequests = \App\Models\Request::where('user_id', $employee->id)
                                                         ->orderBy('created_at', 'desc')
                                                         ->take(3)
                                                         ->get();
                    @endphp

                    @if($recentRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRequests as $request)
                                    <tr>
                                        <td>{{ $request->title }}</td>
                                        <td>
                                            @if($request->type == 'leave')
                                                <span class="badge bg-info">Congé</span>
                                            @elseif($request->type == 'expense')
                                                <span class="badge bg-warning">Remboursement</span>
                                            @elseif($request->type == 'equipment')
                                                <span class="badge bg-primary">Équipement</span>
                                            @else
                                                <span class="badge bg-secondary">Autre</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                                <span class="badge bg-warning">En attente</span>
                                            @elseif($request->status == 'approved')
                                                <span class="badge bg-success">Approuvé</span>
                                            @else
                                                <span class="badge bg-danger">Rejeté</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('employee.requests.show', $request->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Aucune demande récente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Messages et annonces</h5>
                    <a href="{{ route('employee.messages.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    @if($recentMessages->count() > 0)
                        <div class="list-group">
                            @foreach($recentMessages as $message)
                                <a href="{{ route('employee.messages.show', $message->id) }}" class="list-group-item list-group-item-action {{ $message->is_announcement ? 'bg-light' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $message->title }}</h6>
                                        <small>{{ $message->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($message->content, 100) }}</p>
                                    <small>
                                        @if($message->is_announcement)
                                            <span class="badge bg-info">Annonce</span>
                                        @endif
                                        @if($message->read_at)
                                            <span class="text-muted">Lu le {{ $message->read_at->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-danger">Non lu</span>
                                        @endif
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Aucun message récent</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Statistiques de performance</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique de performance
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    
    const performanceChart = new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [{
                label: 'Tâches complétées',
                data: [5, 7, 6, 8, 10, 12],
                borderColor: 'rgba(40, 167, 69, 0.8)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true
            }, {
                label: 'Taux de présence',
                data: [90, 95, 92, 98, 96, 100],
                borderColor: 'rgba(0, 123, 255, 0.8)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true
            }]
        },
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