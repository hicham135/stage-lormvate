<!-- resources/views/department_head/attendance_history.blade.php -->
@extends('layouts.app')

@section('title', 'Historique des présences')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Historique des présences</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportAttendance">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <a href="{{ route('department_head.attendance') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-calendar-day me-1"></i> Présences du jour
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('department_head.attendance_history') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="employee_id" class="form-label">Employé</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">Tous les employés</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('department_head.attendance_history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tendances des présences</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceStatsChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Répartition des statuts</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendancePieChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des présences -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Enregistrements de présence</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchAttendance" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="attendanceTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Employé</th>
                            <th>Arrivée</th>
                            <th>Départ</th>
                            <th>Statut</th>
                            <th>Heures</th>
                            <th>Heures Supp.</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($attendances) > 0)
                            @foreach($attendances as $attendance)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($attendance->check_in)) }}</td>
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
                                </td>
                                <td>
                                    @if($attendance->check_out)
                                        @php
                                            $checkIn = new DateTime($attendance->check_in);
                                            $checkOut = new DateTime($attendance->check_out);
                                            $hours = $checkIn->diff($checkOut)->format('%H:%I');
                                            echo $hours;
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->overtime_hours > 0)
                                        <span class="badge {{ $attendance->overtime_approved ? 'bg-success' : 'bg-warning' }}">
                                            {{ $attendance->overtime_hours }} h 
                                            @if(!$attendance->overtime_approved)
                                                <a href="#" class="text-white ms-1 approve-overtime" data-attendance-id="{{ $attendance->id }}" title="Approuver">
                                                    <i class="fas fa-check-circle"></i>
                                                </a>
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
                                <td colspan="8" class="text-center py-3 text-muted">Aucun enregistrement de présence pour cette période</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Affichage de {{ $attendances->firstItem() ?? 0 }} à {{ $attendances->lastItem() ?? 0 }} sur {{ $attendances->total() ?? 0 }} enregistrements
                </div>
                <div>
                    {{ $attendances->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour approuver les heures supplémentaires -->
<div class="modal fade" id="approveOvertimeModal" tabindex="-1" aria-labelledby="approveOvertimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveOvertimeModalLabel">Approuver les heures supplémentaires</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveOvertimeForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir approuver ces heures supplémentaires ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Approuver</button>
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
    .pagination {
        margin-bottom: 0;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        initAttendanceStatsChart();
        initAttendancePieChart();
        
        // Search functionality
        document.getElementById('searchBtn').addEventListener('click', searchAttendance);
        document.getElementById('searchAttendance').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchAttendance();
            }
        });
        
        // Export attendance data
        document.getElementById('exportAttendance').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en cours de développement...');
        });
        
        // Handle overtime approval clicks
        document.querySelectorAll('.approve-overtime').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const attendanceId = this.getAttribute('data-attendance-id');
                
                // Set form action
                document.getElementById('approveOvertimeForm').action = "{{ route('department_head.approve_overtime', ['id' => '']) }}" + attendanceId;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('approveOvertimeModal'));
                modal.show();
            });
        });
    });

    function searchAttendance() {
        const searchValue = document.getElementById('searchAttendance').value.toLowerCase();
        const table = document.getElementById('attendanceTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const employeeName = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
            const date = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
            
            if (employeeName.includes(searchValue) || date.includes(searchValue)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function initAttendanceStatsChart() {
        var ctx = document.getElementById('attendanceStatsChart').getContext('2d');
        
        // Sample data for attendance stats
        // In a real application, this would come from the backend
        var labels = [];
        var presentData = [];
        var lateData = [];
        var absentData = [];
        var leaveData = [];
        
        // Generate dates for the period between start and end dates
        var startDate = new Date("{{ $startDate }}");
        var endDate = new Date("{{ $endDate }}");
        var currentDate = new Date(startDate);
        
        while (currentDate <= endDate) {
            labels.push(currentDate.getDate() + '/' + (currentDate.getMonth() + 1));
            
            // Generate random data for demo purposes
            // In a real application, this would come from the database
            presentData.push(Math.floor(Math.random() * 10) + 10); // Between 10-20
            lateData.push(Math.floor(Math.random() * 5)); // Between 0-5
            absentData.push(Math.floor(Math.random() * 3)); // Between 0-3
            leaveData.push(Math.floor(Math.random() * 2)); // Between 0-2
            
            // Move to next day
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Présents',
                        data: presentData,
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'En retard',
                        data: lateData,
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Absents',
                        data: absentData,
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'En congé',
                        data: leaveData,
                        backgroundColor: 'rgba(13, 202, 240, 0.2)',
                        borderColor: 'rgba(13, 202, 240, 1)',
                        borderWidth: 2,
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
                        ticks: {
                            stepSize: 5
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
    
    function initAttendancePieChart() {
        var ctx = document.getElementById('attendancePieChart').getContext('2d');
        
        // Sample data for attendance status distribution
        // In a real application, this would come from the backend
        var data = {
            labels: ['Présent', 'En retard', 'Absent', 'Congé'],
            datasets: [{
                data: [70, 15, 10, 5],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(13, 202, 240, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(13, 202, 240, 1)'
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
</script>
@endsection