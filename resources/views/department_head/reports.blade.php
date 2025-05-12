<!-- resources/views/department_head/reports.blade.php -->
@extends('layouts.app')

@section('title', 'Rapports départementaux')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Rapports départementaux</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportReports">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                <i class="fas fa-plus me-1"></i> Nouveau rapport
            </button>
        </div>
    </div>

    <!-- Statistiques des rapports -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-primary text-white mb-3">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="card-title">Total Rapports</h5>
                    <h2 class="mb-0">{{ count($reports) }}</h2>
                    <p class="text-muted">Rapports générés</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-success text-white mb-3">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h5 class="card-title">Présence</h5>
                    <h2 class="mb-0">{{ $reports->where('type', 'attendance')->count() }}</h2>
                    <p class="text-muted">Rapports de présence</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-info text-white mb-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 class="card-title">Tâches</h5>
                    <h2 class="mb-0">{{ $reports->where('type', 'tasks')->count() }}</h2>
                    <p class="text-muted">Rapports de tâches</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-warning text-white mb-3">
                        <i class="fas fa-star"></i>
                    </div>
                    <h5 class="card-title">Performance</h5>
                    <h2 class="mb-0">{{ $reports->where('type', 'performance')->count() }}</h2>
                    <p class="text-muted">Rapports de performance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rapports récents -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Rapports récents</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchReport" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchReportBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="reportsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du rapport</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Date de génération</th>
                            <th>Généré par</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($reports) > 0)
                            @foreach($reports as $report)
                            <tr>
                                <td>{{ $report->name }}</td>
                                <td>
                                    @php
                                        $typeClass = 'bg-secondary';
                                        $typeText = 'Général';
                                        $typeIcon = 'file-alt';
                                        
                                        if ($report->type === 'attendance') {
                                            $typeClass = 'bg-success';
                                            $typeText = 'Présence';
                                            $typeIcon = 'user-check';
                                        } elseif ($report->type === 'performance') {
                                            $typeClass = 'bg-warning';
                                            $typeText = 'Performance';
                                            $typeIcon = 'star';
                                        } elseif ($report->type === 'tasks') {
                                            $typeClass = 'bg-info';
                                            $typeText = 'Tâches';
                                            $typeIcon = 'tasks';
                                        }
                                    @endphp
                                    <span class="badge {{ $typeClass }}">
                                        <i class="fas fa-{{ $typeIcon }} me-1"></i> {{ $typeText }}
                                    </span>
                                </td>
                                <td>{{ date('d/m/Y', strtotime($report->period_start)) }} - {{ date('d/m/Y', strtotime($report->period_end)) }}</td>
                                <td>{{ date('d/m/Y', strtotime($report->created_at)) }}</td>
                                <td>{{ $report->generator->name }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('department_head.view_report', ['id' => $report->id]) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary export-report-btn" data-report-id="{{ $report->id }}" title="Exporter">
                                            <i class="fas fa-file-export"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info regenerate-report-btn" 
                                                data-report-id="{{ $report->id }}"
                                                data-report-name="{{ $report->name }}"
                                                data-report-type="{{ $report->type }}"
                                                title="Régénérer">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Aucun rapport généré</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Affichage de {{ $reports->firstItem() ?? 0 }} à {{ $reports->lastItem() ?? 0 }} sur {{ $reports->total() ?? 0 }} rapports
                </div>
                <div>
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques et graphiques -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Types de rapports générés</h5>
                </div>
                <div class="card-body">
                    <canvas id="reportTypesChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Évolution des rapports générés</h5>
                </div>
                <div class="card-body">
                    <canvas id="reportTrendsChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour générer un rapport -->
<div class="modal fade" id="generateReportModal" tabindex="-1" aria-labelledby="generateReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateReportModalLabel">Générer un nouveau rapport</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department_head.generate_report') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reportName" class="form-label">Nom du rapport <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reportName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportType" class="form-label">Type de rapport <span class="text-danger">*</span></label>
                        <select class="form-select" id="reportType" name="type" required>
                            <option value="">Sélectionner un type</option>
                            <option value="attendance">Rapport de présence</option>
                            <option value="performance">Rapport de performance</option>
                            <option value="tasks">Rapport de tâches</option>
                            <option value="general">Rapport général</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="periodStart" class="form-label">Période - début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="periodStart" name="period_start" required>
                        </div>
                        <div class="col-md-6">
                            <label for="periodEnd" class="form-label">Période - fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="periodEnd" name="period_end" required>
                        </div>
                    </div>
                    
                    <!-- Options spécifiques aux types de rapports -->
                    <div id="attendanceOptions" class="report-options" style="display: none;">
                        <h6 class="mb-3">Options pour le rapport de présence</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeBreakdown" name="include_breakdown" checked>
                                <label class="form-check-label" for="includeBreakdown">
                                    Inclure la répartition par jour
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeAbsences" name="include_absences" checked>
                                <label class="form-check-label" for="includeAbsences">
                                    Inclure les absences
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeOvertimes" name="include_overtimes" checked>
                                <label class="form-check-label" for="includeOvertimes">
                                    Inclure les heures supplémentaires
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="performanceOptions" class="report-options" style="display: none;">
                        <h6 class="mb-3">Options pour le rapport de performance</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeEvaluations" name="include_evaluations" checked>
                                <label class="form-check-label" for="includeEvaluations">
                                    Inclure les détails des évaluations
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeComments" name="include_comments" checked>
                                <label class="form-check-label" for="includeComments">
                                    Inclure les commentaires
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeTrends" name="include_trends" checked>
                                <label class="form-check-label" for="includeTrends">
                                    Inclure les tendances
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="tasksOptions" class="report-options" style="display: none;">
                        <h6 class="mb-3">Options pour le rapport de tâches</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeCompletedTasks" name="include_completed" checked>
                                <label class="form-check-label" for="includeCompletedTasks">
                                    Inclure les tâches terminées
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includePendingTasks" name="include_pending" checked>
                                <label class="form-check-label" for="includePendingTasks">
                                    Inclure les tâches en cours et en attente
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeDelayedTasks" name="include_delayed" checked>
                                <label class="form-check-label" for="includeDelayedTasks">
                                    Inclure les tâches retardées
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeTaskBreakdown" name="include_task_breakdown" checked>
                                <label class="form-check-label" for="includeTaskBreakdown">
                                    Inclure la répartition par employé
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="generalOptions" class="report-options" style="display: none;">
                        <h6 class="mb-3">Options pour le rapport général</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeAttendanceSummary" name="include_attendance_summary" checked>
                                <label class="form-check-label" for="includeAttendanceSummary">
                                    Inclure le résumé des présences
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includePerformanceSummary" name="include_performance_summary" checked>
                                <label class="form-check-label" for="includePerformanceSummary">
                                    Inclure le résumé des performances
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeTasksSummary" name="include_tasks_summary" checked>
                                <label class="form-check-label" for="includeTasksSummary">
                                    Inclure le résumé des tâches
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="includeRecommendations" name="include_recommendations" checked>
                                <label class="form-check-label" for="includeRecommendations">
                                    Inclure des recommandations
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reportFormat" class="form-label">Format de sortie</label>
                        <select class="form-select" id="reportFormat" name="format">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="html">HTML</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Générer le rapport</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour régénérer un rapport -->
<div class="modal fade" id="regenerateReportModal" tabindex="-1" aria-labelledby="regenerateReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateReportModalLabel">Régénérer un rapport</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="regenerateReportForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Vous êtes sur le point de régénérer le rapport suivant :</p>
                    <p class="fw-bold" id="regenerateReportName"></p>
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
    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 28px;
    }
    .pagination {
        margin-bottom: 0;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default dates for report generation
        // Start date: First day of current month
        const now = new Date();
        const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        document.getElementById('periodStart').valueAsDate = firstDayOfMonth;
        
        // End date: Current day
        document.getElementById('periodEnd').valueAsDate = now;
        
        // Initialize charts
        initReportTypesChart();
        initReportTrendsChart();
        
        // Show/hide report options based on type selection
        document.getElementById('reportType').addEventListener('change', function() {
            // Hide all option sections
            document.querySelectorAll('.report-options').forEach(function(el) {
                el.style.display = 'none';
            });
            
            // Show the selected option section
            const selectedType = this.value;
            if (selectedType) {
                document.getElementById(selectedType + 'Options').style.display = 'block';
            }
        });
        
        // Export report button
        document.querySelectorAll('.export-report-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const reportId = this.getAttribute('data-report-id');
                // Implement export functionality
                alert('Fonctionnalité d\'exportation en cours de développement pour le rapport #' + reportId);
            });
        });
        
        // Regenerate report button
        document.querySelectorAll('.regenerate-report-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const reportId = this.getAttribute('data-report-id');
                const reportName = this.getAttribute('data-report-name');
                
                // Set values in modal
                document.getElementById('regenerateReportName').textContent = reportName;
                
                // Set form action (you need to implement the regenerate route)
                document.getElementById('regenerateReportForm').action = ''; // Add your regenerate report route here
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('regenerateReportModal'));
                modal.show();
            });
        });
        
        // Search reports
        document.getElementById('searchReportBtn').addEventListener('click', function() {
            searchReports();
        });
        
        document.getElementById('searchReport').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchReports();
            }
        });
        
        // Export reports button in toolbar
        document.getElementById('exportReports').addEventListener('click', function() {
            alert('Fonctionnalité d\'exportation en cours de développement...');
        });
    });

    function searchReports() {
        const searchValue = document.getElementById('searchReport').value.toLowerCase();
        const table = document.getElementById('reportsTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const reportName = rows[i].cells[0].textContent.toLowerCase();
            const reportType = rows[i].cells[1].textContent.toLowerCase();
            const reportPeriod = rows[i].cells[2].textContent.toLowerCase();
            
            if (reportName.includes(searchValue) || reportType.includes(searchValue) || reportPeriod.includes(searchValue)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function initReportTypesChart() {
        var ctx = document.getElementById('reportTypesChart').getContext('2d');
        
        // Sample data for report types
        // In a real application, this would come from the backend
        var data = {
            labels: ['Présence', 'Performance', 'Tâches', 'Général'],
            datasets: [{
                data: [12, 8, 10, 5],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(13, 202, 240, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(13, 202, 240, 1)',
                    'rgba(108, 117, 125, 1)'
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
    
    function initReportTrendsChart() {
        var ctx = document.getElementById('reportTrendsChart').getContext('2d');
        
        // Sample data for report trends
        // In a real application, this would come from the backend
        var months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        var currentMonth = new Date().getMonth();
        var labels = [];
        
        // Get labels for the last 6 months
        for (var i = 5; i >= 0; i--) {
            var monthIndex = (currentMonth - i + 12) % 12; // This ensures we wrap around correctly
            labels.push(months[monthIndex]);
        }
        
        var data = {
            labels: labels,
            datasets: [
                {
                    label: 'Rapports générés',
                    data: [4, 5, 7, 6, 8, 10],
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
                        ticks: {
                            stepSize: 2
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
</script>
@endsection