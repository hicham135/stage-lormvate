<!-- resources/views/department_head/team_management.blade.php -->
@extends('layouts.app')

@section('title', 'Gestion d\'équipe')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestion de l'équipe</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="input-group me-2">
                <input type="text" class="form-control form-control-sm" id="searchEmployee" placeholder="Rechercher un employé...">
                <button class="btn btn-sm btn-outline-secondary" type="button" id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="exportTeamData">
                <i class="fas fa-file-export me-1"></i> Exporter
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Département: {{ $department->name }}</h5>
                <span class="badge bg-primary">{{ count($employees) + 1 }} membres</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="employeesTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Employé</th>
                            <th scope="col">Email</th>
                            <th scope="col">Date d'embauche</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $index => $employee)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-light me-3">
                                        @if($employee->profile_image)
                                            <img src="{{ asset('storage/' . $employee->profile_image) }}" alt="{{ $employee->name }}" class="avatar-img">
                                        @else
                                            <span class="avatar-text">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $employee->name }}</h6>
                                        <small class="text-muted">{{ $employee->phone ?? 'Aucun téléphone' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->start_date ? date('d/m/Y', strtotime($employee->start_date)) : 'Non définie' }}</td>
                            <td>
                                <span class="badge bg-success">Actif</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('department_head.employee_details', ['id' => $employee->id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Détails
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Plus</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item create-task" href="#" data-employee-id="{{ $employee->id }}" data-employee-name="{{ $employee->name }}">
                                                <i class="fas fa-tasks me-2"></i> Assigner une tâche
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item create-evaluation" href="#" data-employee-id="{{ $employee->id }}" data-employee-name="{{ $employee->name }}">
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
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques d'équipe -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Taux de présence de l'équipe</h5>
                </div>
                <div class="card-body">
                    <canvas id="teamAttendanceChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Évaluations de l'équipe</h5>
                </div>
                <div class="card-body">
                    <canvas id="teamEvaluationChart" height="300"></canvas>
                </div>
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
    .avatar-circle {
        width: 40px;
        height: 40px;
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
        font-size: 14px;
        font-weight: bold;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        document.getElementById('searchBtn').addEventListener('click', searchEmployees);
        document.getElementById('searchEmployee').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchEmployees();
            }
        });

        // Initialize team attendance chart
        initTeamAttendanceChart();
        
        // Initialize team evaluation chart
        initTeamEvaluationChart();
        
        // Handle create task clicks
        document.querySelectorAll('.create-task').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const employeeId = this.getAttribute('data-employee-id');
                const employeeName = this.getAttribute('data-employee-name');
                
                document.getElementById('taskEmployeeId').value = employeeId;
                document.getElementById('createTaskModalLabel').textContent = 
                    'Assigner une tâche à ' + employeeName;
                
                // Set due date to tomorrow by default
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('taskDueDate').valueAsDate = tomorrow;
                
                const modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
                modal.show();
            });
        });
        
        // Handle create evaluation clicks
        document.querySelectorAll('.create-evaluation').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const employeeId = this.getAttribute('data-employee-id');
                const employeeName = this.getAttribute('data-employee-name');
                
                document.getElementById('evaluationEmployeeId').value = employeeId;
                document.getElementById('employeeName').value = employeeName;
                
                // Set default period to current month and year
                const now = new Date();
                const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                document.getElementById('evaluationPeriod').value = months[now.getMonth()] + ' ' + now.getFullYear();
                
                const modal = new bootstrap.Modal(document.getElementById('createEvaluationModal'));
                modal.show();
            });
        });
        
        // Export team data
        document.getElementById('exportTeamData').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en cours de développement...');
        });
    });

    function searchEmployees() {
        const searchValue = document.getElementById('searchEmployee').value.toLowerCase();
        const table = document.getElementById('employeesTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const employeeName = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
            const employeeEmail = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
            
            if (employeeName.includes(searchValue) || employeeEmail.includes(searchValue)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function initTeamAttendanceChart() {
        var ctx = document.getElementById('teamAttendanceChart').getContext('2d');
        
        // Sample data
        var data = {
            labels: ['Présent', 'En retard', 'Absent', 'Congé'],
            datasets: [{
                label: 'Taux de présence (dernier mois)',
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
    
    function initTeamEvaluationChart() {
        var ctx = document.getElementById('teamEvaluationChart').getContext('2d');
        
        // Sample data for employee evaluations
        var data = {
            labels: ['Performance', 'Ponctualité', 'Travail d\'équipe', 'Initiative'],
            datasets: [{
                label: 'Moyenne du département',
                data: [4.2, 3.9, 4.5, 3.8],
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(13, 110, 253, 1)'
            }]
        };
        
        new Chart(ctx, {
            type: 'radar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 5
                    }
                }
            }
        });
    }
</script>
@endsection