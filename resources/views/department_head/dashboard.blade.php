<!-- resources/views/department_head/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tableau de bord</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportBtn">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filtres
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Bienvenue, {{ $user->name }}</h5>
                </div>
                <div class="card-body">
                    <p class="lead">Département : <strong>{{ $department->name }}</strong></p>
                    <p>Accédez rapidement aux informations importantes de votre département et gérez efficacement votre équipe.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-primary text-white mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Employés</h5>
                    <h2 class="mb-0">{{ $employeeCount }}</h2>
                    <p class="text-muted">Total membres</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-success text-white mb-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5 class="card-title">Présences</h5>
                    <h2 class="mb-0">{{ $presentToday }}</h2>
                    <p class="text-muted">Présents aujourd'hui</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-warning text-white mb-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5 class="card-title">Demandes</h5>
                    <h2 class="mb-0">{{ $pendingLeaveRequests }}</h2>
                    <p class="text-muted">Congés en attente</p>
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
                    <h2 class="mb-0">{{ $pendingTasks }}</h2>
                    <p class="text-muted">En cours/Attente</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Graphique de présence -->
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Taux de présence (30 derniers jours)</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="attendanceDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="attendanceDropdown">
                            <li><a class="dropdown-item" href="{{ route('department_head.attendance') }}">Voir détails</a></li>
                            <li><a class="dropdown-item" href="{{ route('department_head.attendance_history') }}">Historique</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="attendanceExport">Exporter</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tâches prochaines échéances -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Prochaines échéances</h5>
                    <a href="{{ route('department_head.tasks') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus"></i> Gérer
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="upcomingTasks">
                        <div class="list-group-item py-3 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Demandes récentes -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Demandes récentes</h5>
                    <a href="{{ route('department_head.leave_requests') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list"></i> Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="recentRequests">
                        <div class="list-group-item py-3 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Annonces départementales -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Annonces départementales</h5>
                    <a href="{{ route('department_head.announcements') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus"></i> Publier
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="departmentAnnouncements">
                        <div class="list-group-item py-3 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filtres -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filtrer le tableau de bord</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="periodFilter" class="form-label">Période</label>
                            <select class="form-select" id="periodFilter">
                                <option value="today">Aujourd'hui</option>
                                <option value="week" selected>Cette semaine</option>
                                <option value="month">Ce mois</option>
                                <option value="quarter">Ce trimestre</option>
                                <option value="year">Cette année</option>
                                <option value="custom">Personnalisé</option>
                            </select>
                        </div>
                        <div class="row mb-3 custom-date-range" style="display: none;">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Date début</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">Date fin</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dataFilter" class="form-label">Données à afficher</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="attendance" id="attendanceCheck" checked>
                                <label class="form-check-label" for="attendanceCheck">
                                    Présences
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="tasks" id="tasksCheck" checked>
                                <label class="form-check-label" for="tasksCheck">
                                    Tâches
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="requests" id="requestsCheck" checked>
                                <label class="form-check-label" for="requestsCheck">
                                    Demandes
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="announcements" id="announcementsCheck" checked>
                                <label class="form-check-label" for="announcementsCheck">
                                    Annonces
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="applyFilters">Appliquer</button>
                </div>
            </div>
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
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .task-badge {
        width: 70px;
    }
    .priority-urgent {
        color: #dc3545;
    }
    .priority-high {
        color: #fd7e14;
    }
    .priority-medium {
        color: #ffc107;
    }
    .priority-low {
        color: #20c997;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle custom date range based on period selection
        document.getElementById('periodFilter').addEventListener('change', function() {
            if (this.value === 'custom') {
                document.querySelector('.custom-date-range').style.display = 'flex';
            } else {
                document.querySelector('.custom-date-range').style.display = 'none';
            }
        });

        // Fetch upcoming tasks
        fetchUpcomingTasks();

        // Fetch recent requests
        fetchRecentRequests();

        // Fetch department announcements
        fetchDepartmentAnnouncements();

        // Initialize attendance chart
        initAttendanceChart();

        // Apply filters
        document.getElementById('applyFilters').addEventListener('click', function() {
            // Refresh data based on filters
            fetchUpcomingTasks();
            fetchRecentRequests();
            fetchDepartmentAnnouncements();
            updateAttendanceChart();

            // Close modal
            var filterModal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
            filterModal.hide();
        });
    });

    // Initialize attendance chart
    function initAttendanceChart() {
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        
        // Sample data - in a real scenario, this would come from an AJAX call
        var labels = [];
        var present = [];
        var absent = [];
        var late = [];
        
        // Generate fake data for the last 30 days
        var today = new Date();
        for (var i = 29; i >= 0; i--) {
            var date = new Date(today);
            date.setDate(date.getDate() - i);
            labels.push(date.getDate() + '/' + (date.getMonth() + 1));
            
            // Random data for demonstration
            var total = {{ $employeeCount }};
            var presentCount = Math.floor(Math.random() * (total - 5)) + 5;
            var lateCount = Math.floor(Math.random() * 5);
            var absentCount = total - presentCount - lateCount;
            
            present.push(presentCount);
            absent.push(absentCount);
            late.push(lateCount);
        }
        
        window.attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Présents',
                    data: present,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }, {
                    label: 'Absents',
                    data: absent,
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }, {
                    label: 'Retards',
                    data: late,
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: {{ $employeeCount }}
                    }
                }
            }
        });
    }

    function updateAttendanceChart() {
        // In a real application, you would fetch data from the server
        // and update the chart data
        // This is just a demo that randomizes the data
        
        var present = [];
        var absent = [];
        var late = [];
        
        for (var i = 0; i < 30; i++) {
            var total = {{ $employeeCount }};
            var presentCount = Math.floor(Math.random() * (total - 5)) + 5;
            var lateCount = Math.floor(Math.random() * 5);
            var absentCount = total - presentCount - lateCount;
            
            present.push(presentCount);
            absent.push(absentCount);
            late.push(lateCount);
        }
        
        window.attendanceChart.data.datasets[0].data = present;
        window.attendanceChart.data.datasets[1].data = absent;
        window.attendanceChart.data.datasets[2].data = late;
        window.attendanceChart.update();
    }

    function fetchUpcomingTasks() {
        // In a real application, this would be an AJAX call to the server
        // For this demo, we'll just simulate loading with a timeout
        setTimeout(function() {
            var tasks = [
                { 
                    id: 1, 
                    title: 'Préparer le rapport mensuel', 
                    due_date: '15/05/2025', 
                    assignee: 'Ahmed Benali', 
                    priority: 'urgent',
                    status: 'in_progress'
                },
                { 
                    id: 2, 
                    title: 'Audit des procédures', 
                    due_date: '20/05/2025', 
                    assignee: 'Samira Alaoui', 
                    priority: 'high',
                    status: 'pending'
                },
                { 
                    id: 3, 
                    title: 'Formation des nouveaux', 
                    due_date: '25/05/2025', 
                    assignee: 'Karim Idrissi', 
                    priority: 'medium',
                    status: 'pending'
                }
            ];
            
            var tasksContainer = document.getElementById('upcomingTasks');
            tasksContainer.innerHTML = '';
            
            if (tasks.length === 0) {
                tasksContainer.innerHTML = '<div class="list-group-item py-3 text-center text-muted">Aucune tâche à venir</div>';
                return;
            }
            
            tasks.forEach(function(task) {
                var priorityClass = 'priority-' + task.priority;
                var statusBadge = '';
                
                switch(task.status) {
                    case 'pending':
                        statusBadge = '<span class="badge bg-secondary task-badge">En attente</span>';
                        break;
                    case 'in_progress':
                        statusBadge = '<span class="badge bg-info task-badge">En cours</span>';
                        break;
                    case 'completed':
                        statusBadge = '<span class="badge bg-success task-badge">Terminée</span>';
                        break;
                    case 'delayed':
                        statusBadge = '<span class="badge bg-danger task-badge">Retardée</span>';
                        break;
                }
                
                tasksContainer.innerHTML += `
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h6 class="mb-1">
                                <i class="fas fa-circle ${priorityClass} me-2" style="font-size: 10px;"></i>
                                ${task.title}
                            </h6>
                            ${statusBadge}
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Assignée à: ${task.assignee}</small>
                            <small class="text-muted">Échéance: ${task.due_date}</small>
                        </div>
                    </div>
                `;
            });
        }, 1000);
    }

    function fetchRecentRequests() {
        // Simulate AJAX call with timeout
        setTimeout(function() {
            var requests = [
                { 
                    id: 1, 
                    type: 'Congé annuel', 
                    employee: 'Nadia Mansouri', 
                    days: '5 jours',
                    date_range: '20/05 - 24/05/2025',
                    status: 'pending'
                },
                { 
                    id: 2, 
                    type: 'Congé maladie', 
                    employee: 'Hassan Tazi', 
                    days: '2 jours',
                    date_range: '13/05 - 14/05/2025',
                    status: 'pending'
                },
                { 
                    id: 3, 
                    type: 'Heures sup.', 
                    employee: 'Fatima Zahra Benjelloun', 
                    hours: '3 heures',
                    date: '10/05/2025',
                    status: 'pending'
                }
            ];
            
            var requestsContainer = document.getElementById('recentRequests');
            requestsContainer.innerHTML = '';
            
            if (requests.length === 0) {
                requestsContainer.innerHTML = '<div class="list-group-item py-3 text-center text-muted">Aucune demande récente</div>';
                return;
            }
            
            requests.forEach(function(request) {
                var requestDetails = '';
                if (request.hasOwnProperty('days')) {
                    requestDetails = `${request.days} (${request.date_range})`;
                } else if (request.hasOwnProperty('hours')) {
                    requestDetails = `${request.hours} (${request.date})`;
                }
                
                requestsContainer.innerHTML += `
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h6 class="mb-1">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                ${request.type}
                            </h6>
                            <div>
                                <a href="{{ route('department_head.leave_requests') }}" class="btn btn-sm btn-outline-success me-1" title="Approuver">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="{{ route('department_head.leave_requests') }}" class="btn btn-sm btn-outline-danger" title="Rejeter">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Demandé par: ${request.employee}</small>
                            <small class="text-muted">${requestDetails}</small>
                        </div>
                    </div>
                `;
            });
        }, 1500);
    }

    function fetchDepartmentAnnouncements() {
        // Simulate AJAX call with timeout
        setTimeout(function() {
            var announcements = [
                { 
                    id: 1, 
                    title: 'Réunion mensuelle', 
                    date: '15/05/2025',
                    author: 'Vous',
                    content: 'La réunion mensuelle du département aura lieu le 15 mai à 10h dans la salle de conférence.'
                },
                { 
                    id: 2, 
                    title: 'Nouvelle procédure qualité', 
                    date: '08/05/2025',
                    author: 'Direction',
                    content: 'Une nouvelle procédure qualité sera mise en place à partir du 20 mai. Formation obligatoire...'
                }
            ];
            
            var announcementsContainer = document.getElementById('departmentAnnouncements');
            announcementsContainer.innerHTML = '';
            
            if (announcements.length === 0) {
                announcementsContainer.innerHTML = '<div class="list-group-item py-3 text-center text-muted">Aucune annonce récente</div>';
                return;
            }
            
            announcements.forEach(function(announcement) {
                announcementsContainer.innerHTML += `
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h6 class="mb-1">
                                <i class="fas fa-bullhorn me-2 text-primary"></i>
                                ${announcement.title}
                            </h6>
                            <small class="text-muted">${announcement.date}</small>
                        </div>
                        <p class="mb-1 small">${announcement.content}</p>
                        <small class="text-muted">Publié par: ${announcement.author}</small>
                    </div>
                `;
            });
        }, 2000);
    }
</script>
@endsection