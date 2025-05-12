<!-- resources/views/department_head/view_report.blade.php -->
@extends('layouts.app')

@section('title', 'Visualisation du rapport')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Rapport: {{ $report->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('department_head.reports') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" id="exportPDF">Format PDF</a></li>
                    <li><a class="dropdown-item" href="#" id="exportExcel">Format Excel</a></li>
                    <li><a class="dropdown-item" href="#" id="exportCSV">Format CSV</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="regenerateReportBtn" data-report-id="{{ $report->id }}">
                <i class="fas fa-sync-alt me-1"></i> Régénérer
            </button>
        </div>
    </div>

    <!-- Informations du rapport -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Informations du rapport</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted mb-2">Détails généraux</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Type de rapport:</th>
                            <td>
                                @php
                                    $typeText = 'Général';
                                    $typeBadge = 'bg-secondary';
                                    
                                    if ($report->type === 'attendance') {
                                        $typeText = 'Présence';
                                        $typeBadge = 'bg-success';
                                    } elseif ($report->type === 'performance') {
                                        $typeText = 'Performance';
                                        $typeBadge = 'bg-warning';
                                    } elseif ($report->type === 'tasks') {
                                        $typeText = 'Tâches';
                                        $typeBadge = 'bg-info';
                                    }
                                @endphp
                                <span class="badge {{ $typeBadge }}">{{ $typeText }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Département:</th>
                            <td>{{ $department->name }}</td>
                        </tr>
                        <tr>
                            <th>Généré par:</th>
                            <td>{{ $report->generator->name }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted mb-2">Période</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Date de début:</th>
                            <td>{{ date('d/m/Y', strtotime($report->period_start)) }}</td>
                        </tr>
                        <tr>
                            <th>Date de fin:</th>
                            <td>{{ date('d/m/Y', strtotime($report->period_end)) }}</td>
                        </tr>
                        <tr>
                            <th>Durée:</th>
                            <td>
                                @php
                                    $start = new DateTime($report->period_start);
                                    $end = new DateTime($report->period_end);
                                    $interval = $start->diff($end);
                                    echo $interval->days + 1 . ' jour(s)';
                                @endphp
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted mb-2">Génération</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Date de génération:</th>
                            <td>{{ date('d/m/Y à H:i', strtotime($report->created_at)) }}</td>
                        </tr>
                        <tr>
                            <th>Format:</th>
                            <td>
                                @php
                                    $format = "HTML";
                                    if (strpos($report->file_path, '.pdf') !== false) {
                                        $format = "PDF";
                                    } elseif (strpos($report->file_path, '.xlsx') !== false) {
                                        $format = "Excel";
                                    }
                                @endphp
                                {{ $format }}
                            </td>
                        </tr>
                        <tr>
                            <th>Fichier:</th>
                            <td>
                                <a href="#" class="text-primary">
                                    <i class="fas fa-file-download me-1"></i> Télécharger
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu du rapport -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                @if($report->type === 'attendance')
                    Rapport de présence
                @elseif($report->type === 'performance')
                    Rapport de performance
                @elseif($report->type === 'tasks')
                    Rapport de tâches
                @else
                    Rapport général
                @endif
            </h5>
        </div>
        <div class="card-body" id="report-content">
            <!-- Affichage conditionnel basé sur le type de rapport -->
            @if($report->type === 'attendance')
                @include('department_head.report_templates.attendance')
            @elseif($report->type === 'performance')
                @include('department_head.report_templates.performance')
            @elseif($report->type === 'tasks')
                @include('department_head.report_templates.tasks')
            @else
                @include('department_head.report_templates.general')
            @endif
        </div>
    </div>
</div>

<!-- Modal pour régénérer un rapport -->
<div class="modal fade" id="regenerateReportModal" tabindex="-1" aria-labelledby="regenerateReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateReportModalLabel">Régénérer ce rapport</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="regenerateReportForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Vous êtes sur le point de régénérer le rapport suivant :</p>
                    <p class="fw-bold">{{ $report->name }}</p>
                    <p>Le rapport sera mis à jour avec les données actuelles pour la même période.</p>
                    <div class="mb-3">
                        <label for="regenerateReportFormat" class="form-label">Format de sortie</label>
                        <select class="form-select" id="regenerateReportFormat" name="format">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="html">HTML</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Régénérer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    #report-content {
        padding: 20px;
    }
    .report-section {
        margin-bottom: 30px;
    }
    .report-section-title {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .report-table {
        width: 100%;
        border-collapse: collapse;
    }
    .report-table th, .report-table td {
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    .report-table th {
        background-color: #f8f9fa;
    }
    .chart-container {
        height: 300px;
        margin-bottom: 20px;
    }
    .summary-box {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .employee-card {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .metric-box {
        text-align: center;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .metric-value {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .metric-label {
        color: #6c757d;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-header {
            background-color: white !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export buttons
        document.getElementById('exportPDF').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en PDF en cours de développement...');
        });
        
        document.getElementById('exportExcel').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en Excel en cours de développement...');
        });
        
        document.getElementById('exportCSV').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en CSV en cours de développement...');
        });
        
        // Regenerate report button
        document.getElementById('regenerateReportBtn').addEventListener('click', function() {
            const reportId = this.getAttribute('data-report-id');
            
            // Set form action (you need to implement the regenerate route)
            document.getElementById('regenerateReportForm').action = ''; // Add your regenerate report route here
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('regenerateReportModal'));
            modal.show();
        });
        
        // Initialize charts if needed based on report type
        @if($report->type === 'attendance' || $report->type === 'general')
        initAttendanceChart();
        @endif
        
        @if($report->type === 'performance' || $report->type === 'general')
        initPerformanceChart();
        @endif
        
        @if($report->type === 'tasks' || $report->type === 'general')
        initTasksChart();
        @endif
    });
    
    @if($report->type === 'attendance' || $report->type === 'general')
    function initAttendanceChart() {
        // Find canvas elements for attendance charts
        var presentCanvas = document.getElementById('attendancePresentChart');
        var typeCanvas = document.getElementById('attendanceTypeChart');
        
        if (presentCanvas) {
            var ctx = presentCanvas.getContext('2d');
            
            // Sample data for attendance by day
            // In a real application, this would come from the backend
            var data = {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven'],
                datasets: [
                    {
                        label: 'Présents',
                        data: [18, 17, 19, 16, 15, 17, 18, 16, 19, 18],
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Absents',
                        data: [2, 3, 1, 4, 5, 3, 2, 4, 1, 2],
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    }
                ]
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
                            max: 20
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
        
        if (typeCanvas) {
            var ctx = typeCanvas.getContext('2d');
            
            // Sample data for attendance by type
            // In a real application, this would come from the backend
            var data = {
                labels: ['Présent', 'En retard', 'Absent', 'Congé'],
                datasets: [{
                    data: [75, 10, 8, 7],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(13, 110, 253, 0.7)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(13, 110, 253, 1)'
                    ],
                    borderWidth: 1
                }]
            };
            
            new Chart(ctx, {
                type: 'pie',
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
    }
    @endif
    
    @if($report->type === 'performance' || $report->type === 'general')
    function initPerformanceChart() {
        // Find canvas elements for performance charts
        var scoresCanvas = document.getElementById('performanceScoresChart');
        var trendsCanvas = document.getElementById('performanceTrendsChart');
        
        if (scoresCanvas) {
            var ctx = scoresCanvas.getContext('2d');
            
            // Sample data for performance scores
            // In a real application, this would come from the backend
            var data = {
                labels: ['Ahmed', 'Fatima', 'Omar', 'Samira', 'Karim'],
                datasets: [
                    {
                        label: 'Performance',
                        data: [4.5, 3.8, 4.2, 4.7, 3.5],
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    },
                    {
                        label: 'Ponctualité',
                        data: [4.2, 4.5, 3.7, 4.0, 4.8],
                        backgroundColor: 'rgba(13, 202, 240, 0.7)',
                    },
                    {
                        label: 'Travail d\'équipe',
                        data: [4.8, 4.0, 4.5, 4.3, 3.9],
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    },
                    {
                        label: 'Initiative',
                        data: [3.5, 3.6, 4.0, 4.5, 3.2],
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
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
        
        if (trendsCanvas) {
            var ctx = trendsCanvas.getContext('2d');
            
            // Sample data for performance trends
            // In a real application, this would come from the backend
            var data = {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai'],
                datasets: [
                    {
                        label: 'Score moyen',
                        data: [3.8, 4.0, 4.1, 4.3, 4.5],
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    }
                ]
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
    }
    @endif
    
    @if($report->type === 'tasks' || $report->type === 'general')
    function initTasksChart() {
        // Find canvas elements for tasks charts
        var statusCanvas = document.getElementById('tasksStatusChart');
        var priorityCanvas = document.getElementById('tasksPriorityChart');
        
        if (statusCanvas) {
            var ctx = statusCanvas.getContext('2d');
            
            // Sample data for tasks by status
            // In a real application, this would come from the backend
            var data = {
                labels: ['En attente', 'En cours', 'Terminées', 'Retardées'],
                datasets: [{
                    data: [12, 19, 25, 5],
                    backgroundColor: [
                        'rgba(108, 117, 125, 0.7)',
                        'rgba(13, 202, 240, 0.7)',
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(108, 117, 125, 1)',
                        'rgba(13, 202, 240, 1)',
                        'rgba(25, 135, 84, 1)',
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
        
        if (priorityCanvas) {
            var ctx = priorityCanvas.getContext('2d');
            
            // Sample data for tasks by priority
            // In a real application, this would come from the backend
            var data = {
                labels: ['Basse', 'Moyenne', 'Haute', 'Urgente'],
                datasets: [
                    {
                        label: 'Nombre de tâches',
                        data: [8, 22, 17, 14],
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
                        y: {
                            beginAtZero: true
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
    }
    @endif
</script>
@endsection