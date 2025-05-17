<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mt-2 mb-4">Tableau de Bord</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white">Département</h6>
                            <h3 class="text-white">{{ $department->name }}</h3>
                        </div>
                        <i class="fas fa-building fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white">Employés</h6>
                            <h3 class="text-white">{{ $totalEmployees }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white">Tâches en attente</h6>
                            <h3 class="text-white">{{ $pendingTasks }}</h3>
                        </div>
                        <i class="fas fa-tasks fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white">Demandes</h6>
                            <h3 class="text-white">{{ $pendingRequests }}</h3>
                        </div>
                        <i class="fas fa-envelope fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Présences de l'équipe - Ce mois</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Performance des tâches</h5>
                </div>
                <div class="card-body">
                    <canvas id="tasksChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Demandes récentes</h5>
                    <a href="{{ route('requests.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Demandeur</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Congé annuel</td>
                                    <td>Congé</td>
                                    <td>Jean Dupont</td>
                                    <td>{{ date('d/m/Y') }}</td>
                                    <td><span class="badge bg-warning">En attente</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Remboursement frais</td>
                                    <td>Finances</td>
                                    <td>Marie Martin</td>
                                    <td>{{ date('d/m/Y', strtotime('-1 day')) }}</td>
                                    <td><span class="badge bg-warning">En attente</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Données pour le graphique des présences
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Présent', 'Absent', 'En retard'],
            datasets: [{
                data: [75, 15, 10],
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
    
    // Données pour le graphique des tâches
    const tasksCtx = document.getElementById('tasksChart').getContext('2d');
    const tasksChart = new Chart(tasksCtx, {
        type: 'bar',
        data: {
            labels: ['En attente', 'En cours', 'Terminé', 'En retard'],
            datasets: [{
                label: 'Nombre de tâches',
                data: [12, 19, 8, 5],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ]
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