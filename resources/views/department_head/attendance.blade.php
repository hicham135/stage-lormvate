<!-- resources/views/department_head/attendance.blade.php -->
@extends('layouts.app')

@section('title', 'Gestion des présences')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Présences du département</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportAttendance">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <a href="{{ route('department_head.attendance_history') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-history me-1"></i> Historique
            </a>
        </div>
    </div>

    <!-- Vue d'ensemble -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Vue d'ensemble pour {{ $today->format('d/m/Y') }}</h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" id="previousDay">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="today">Aujourd'hui</button>
                    <button class="btn btn-sm btn-outline-secondary" id="nextDay">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <!-- Total Employés -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-primary text-white mb-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">Total Employés</h5>
                            <h2 class="mb-0">{{ count($employees) }}</h2>
                        </div>
                    </div>
                </div>
                <!-- Présents -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-success text-white mb-3">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5 class="card-title">Présents</h5>
                            <h2 class="mb-0">{{ $attendances->where('status', 'present')->count() }}</h2>
                        </div>
                    </div>
                </div>
                <!-- En retard -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-warning text-white mb-3">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <h5 class="card-title">En retard</h5>
                            <h2 class="mb-0">{{ $attendances->where('status', 'late')->count() }}</h2>
                        </div>
                    </div>
                </div>
                <!-- Absents -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-danger text-white mb-3">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <h5 class="card-title">Absents</h5>
                            <h2 class="mb-0">{{ count($absentEmployees) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique de présence -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Tendance des présences (7 derniers jours)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des employés présents -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Employés présents aujourd'hui</h5>
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" class="form-control" id="searchPresentEmployee" placeholder="Rechercher...">
                            <button class="btn btn-outline-secondary" type="button" id="searchPresentBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="presentEmployeesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Employé</th>
                                    <th>Heure d'arrivée</th>
                                    <th>Heure de départ</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($attendances) > 0)
                                    @foreach($attendances as $attendance)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-light me-2">
                                                    @if($attendance->user->profile_image)
                                                        <img src="{{ asset('storage/' . $attendance->user->profile_image) }}" alt="{{ $attendance->user->name }}" class="avatar-img">
                                                    @else
                                                        <span class="avatar-text">{{ strtoupper(substr($attendance->user->name, 0, 2)) }}</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $attendance->user->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
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
                                            @if($attendance->overtime_hours > 0)
                                                <span class="badge {{ $attendance->overtime_approved ? 'bg-success' : 'bg-warning' }} ms-1">
                                                    {{ $attendance->overtime_hours }} h supp.
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('department_head.employee_details', ['id' => $attendance->user->id]) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-user me-1"></i> Profil
                                                </a>
                                                @if($attendance->overtime_hours > 0 && !$attendance->overtime_approved)
                                                    <form action="{{ route('department_head.approve_overtime', ['id' => $attendance->id]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success">
                                                            <i class="fas fa-check me-1"></i> Approuver heures
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">Aucun employé présent aujourd'hui</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des employés absents -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Employés absents</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="absencesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="absencesDropdown">
                                <li><a class="dropdown-item" href="#" data-filter="all">Tous</a></li>
                                <li><a class="dropdown-item" href="#" data-filter="congé">En congé</a></li>
                                <li><a class="dropdown-item" href="#" data-filter="sans justification">Sans justification</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="absentEmployeesList">
                        @if(count($absentEmployees) > 0)
                            @foreach($absentEmployees as $employee)
                                @php
                                    // Vérifier si l'employé est en congé approuvé
                                    $onLeave = false;
                                    $leaveColor = 'danger';
                                    $leaveIcon = 'user-times';
                                    $leaveText = 'Absent sans justification';
                                    
                                    // Dans une vraie application, on vérifierait si l'employé a un congé approuvé pour aujourd'hui
                                    // Pour cet exemple, nous allons simuler cela aléatoirement
                                    if (rand(0, 2) === 1) {
                                        $onLeave = true;
                                        $leaveColor = 'info';
                                        $leaveIcon = 'calendar-check';
                                        $leaveText = 'En congé approuvé';
                                    }
                                @endphp
                                <div class="list-group-item list-group-item-action" data-absence-type="{{ $onLeave ? 'congé' : 'sans justification' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
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
                                                <span class="badge bg-{{ $leaveColor }}">
                                                    <i class="fas fa-{{ $leaveIcon }} me-1"></i> {{ $leaveText }}
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ route('department_head.employee_details', ['id' => $employee->id]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="list-group-item py-3 text-center text-muted">Tous les employés sont présents</div>
                        @endif
                    </div>
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
        // Initialize attendance chart
        initAttendanceChart();
        
        // Search functionality for present employees
        document.getElementById('searchPresentBtn').addEventListener('click', searchPresentEmployees);
        document.getElementById('searchPresentEmployee').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchPresentEmployees();
            }
        });
        
        // Date navigation buttons
        document.getElementById('previousDay').addEventListener('click', function() {
            // In a real app, this would navigate to the previous day
            alert('Navigation vers le jour précédent - fonctionnalité en développement');
        });
        
        document.getElementById('today').addEventListener('click', function() {
            // In a real app, this would reload the current day
            window.location.reload();
        });
        
        document.getElementById('nextDay').addEventListener('click', function() {
            // In a real app, this would navigate to the next day
            alert('Navigation vers le jour suivant - fonctionnalité en développement');
        });
        
        // Export attendance data
        document.getElementById('exportAttendance').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en cours de développement...');
        });
        
        // Filter absences
        document.querySelectorAll('#absencesDropdown .dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                filterAbsences(filter);
            });
        });
    });

    function searchPresentEmployees() {
        const searchValue = document.getElementById('searchPresentEmployee').value.toLowerCase();
        const table = document.getElementById('presentEmployeesTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const employeeName = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
            
            if (employeeName.includes(searchValue)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
    
    function filterAbsences(filter) {
        const listItems = document.querySelectorAll('#absentEmployeesList .list-group-item');
        
        listItems.forEach(function(item) {
            if (filter === 'all' || item.getAttribute('data-absence-type') === filter) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function initAttendanceChart() {
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        
        // Sample data for the last 7 days
        var labels = [];
        var presentData = [];
        var lateData = [];
        var absentData = [];
        var leaveData = [];
        
        // Generate dates for the last 7 days
        for (var i = 6; i >= 0; i--) {
            var date = new Date();
            date.setDate(date.getDate() - i);
            labels.push(date.getDate() + '/' + (date.getMonth() + 1));
            
            // Generate random data for demo purposes
            // In a real application, this would come from the database
            presentData.push(Math.floor(Math.random() * 10) + 10); // Between 10-20
            lateData.push(Math.floor(Math.random() * 5)); // Between 0-5
            absentData.push(Math.floor(Math.random() * 3)); // Between 0-3
            leaveData.push(Math.floor(Math.random() * 2)); // Between 0-2
        }
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Présents',
                        data: presentData,
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'En retard',
                        data: lateData,
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Absents',
                        data: absentData,
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'En congé',
                        data: leaveData,
                        backgroundColor: 'rgba(13, 202, 240, 0.7)',
                        borderColor: 'rgba(13, 202, 240, 1)',
                        borderWidth: 1
                    }
                ]
            },
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