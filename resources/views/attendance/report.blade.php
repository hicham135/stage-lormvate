<!-- resources/views/attendance/report.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Rapport de Présence</h1>
        <div>
            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <button type="button" class="btn btn-primary ms-2">
                <i class="fas fa-download me-2"></i>Exporter
            </button>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-light">
            <form action="{{ route('attendance.report') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Du</span>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Au</span>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Générer le rapport</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Rapport détaillé par employé</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employé</th>
                                    <th>Jours travaillés</th>
                                    <th>Absences</th>
                                    <th>Retards</th>
                                    <th>Taux de présence</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceReport as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->attendances->where('status', 'present')->count() }}</td>
                                    <td>{{ $employee->attendances->where('status', 'absent')->count() }}</td>
                                    <td>{{ $employee->attendances->where('status', 'late')->count() }}</td>
                                    <td>
                                        @php
                                            $totalDays = $employee->attendances->count();
                                            $presentDays = $employee->attendances->where('status', 'present')->count();
                                            $presentPercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $presentPercentage > 80 ? 'bg-success' : ($presentPercentage > 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                role="progressbar" style="width: {{ $presentPercentage }}%;" 
                                                aria-valuenow="{{ $presentPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $presentPercentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#employeeDetailModal{{ $employee->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <!-- Modal détaillé -->
                                        <div class="modal fade" id="employeeDetailModal{{ $employee->id }}" tabindex="-1" aria-labelledby="employeeDetailModalLabel{{ $employee->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="employeeDetailModalLabel{{ $employee->id }}">Détail de présence - {{ $employee->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Arrivée</th>
                                                                        <th>Départ</th>
                                                                        <th>Statut</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($employee->attendances->sortByDesc('date') as $att)
                                                                    <tr>
                                                                        <td>{{ $att->date->format('d/m/Y') }}</td>
                                                                        <td>{{ $att->check_in ? $att->check_in->format('H:i') : '-' }}</td>
                                                                        <td>{{ $att->check_out ? $att->check_out->format('H:i') : '-' }}</td>
                                                                        <td>
                                                                            @if($att->status == 'present')
                                                                                <span class="badge bg-success">Présent</span>
                                                                            @elseif($att->status == 'absent')
                                                                                <span class="badge bg-danger">Absent</span>
                                                                            @elseif($att->status == 'late')
                                                                                <span class="badge bg-warning">En retard</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">{{ $att->status }}</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Résumé de présence</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceSummaryChart"></canvas>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tendance sur la période</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Taux moyen de présence
                            <span class="badge bg-primary rounded-pill">85%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Jours travaillés (total)
                            <span class="badge bg-success rounded-pill">425</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Absences (total)
                            <span class="badge bg-danger rounded-pill">48</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Retards (total)
                            <span class="badge bg-warning rounded-pill">27</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique récapitulatif de présence
    const summaryCtx = document.getElementById('attendanceSummaryChart').getContext('2d');
    
    // Calculer les totaux
    let totalPresent = 0;
    let totalAbsent = 0;
    let totalLate = 0;
    
    @foreach($attendanceReport as $employee)
        totalPresent += {{ $employee->attendances->where('status', 'present')->count() }};
        totalAbsent += {{ $employee->attendances->where('status', 'absent')->count() }};
        totalLate += {{ $employee->attendances->where('status', 'late')->count() }};
    @endforeach
    
    const summaryChart = new Chart(summaryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Présent', 'Absent', 'En retard'],
            datasets: [{
                data: [totalPresent, totalAbsent, totalLate],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
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
    
    // Graphique de tendance
    const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
    
    // Simuler des données pour la tendance (à remplacer par des données réelles)
    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'],
            datasets: [{
                label: 'Taux de présence',
                data: [88, 82, 87, 90],
                borderColor: 'rgba(40, 167, 69, 0.8)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false,
                    min: 70,
                    max: 100
                }
            }
        }
    });
</script>
@endsection
