@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Présences</h1>
        <div>
            <a href="{{ route('attendance.report') }}" class="btn btn-info">
                <i class="fas fa-chart-bar me-2"></i>Rapport de présence
            </a>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-light">
            <form action="{{ route('attendance.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Date</span>
                        <input type="date" class="form-control" name="date" value="{{ $date }}" onchange="this.form.submit()">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Heure d'arrivée</th>
                            <th>Heure de départ</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->user->name }}</td>
                            <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</td>
                            <td>
                                @if($attendance->status == 'present')
                                    <span class="badge bg-success">Présent</span>
                                @elseif($attendance->status == 'absent')
                                    <span class="badge bg-danger">Absent</span>
                                @elseif($attendance->status == 'late')
                                    <span class="badge bg-warning">En retard</span>
                                @else
                                    <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAttendanceModal{{ $attendance->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <!-- Modal d'édition -->
                                <div class="modal fade" id="editAttendanceModal{{ $attendance->id }}" tabindex="-1" aria-labelledby="editAttendanceModalLabel{{ $attendance->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editAttendanceModalLabel{{ $attendance->id }}">Modifier la présence</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="#" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="employee" class="form-label">Employé</label>
                                                        <input type="text" class="form-control" value="{{ $attendance->user->name }}" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="check_in" class="form-label">Heure d'arrivée</label>
                                                        <input type="time" class="form-control" id="check_in" name="check_in" value="{{ $attendance->check_in ? $attendance->check_in->format('H:i') : '' }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="check_out" class="form-label">Heure de départ</label>
                                                        <input type="time" class="form-control" id="check_out" name="check_out" value="{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '' }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="status" class="form-label">Statut</label>
                                                        <select class="form-select" id="status" name="status">
                                                            <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Présent</option>
                                                            <option value="absent" {{ $attendance->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                                            <option value="late" {{ $attendance->status == 'late' ? 'selected' : '' }}>En retard</option>
                                                            <option value="half_day" {{ $attendance->status == 'half_day' ? 'selected' : '' }}>Demi-journée</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        @foreach($teamWithoutAttendance as $member)
                        <tr class="table-secondary">
                            <td>{{ $member->name }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td><span class="badge bg-secondary">Non enregistré</span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addAttendanceModal{{ $member->id }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                                
                                <!-- Modal d'ajout -->
                                <div class="modal fade" id="addAttendanceModal{{ $member->id }}" tabindex="-1" aria-labelledby="addAttendanceModalLabel{{ $member->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addAttendanceModalLabel{{ $member->id }}">Ajouter une présence</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="#" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="employee" class="form-label">Employé</label>
                                                        <input type="text" class="form-control" value="{{ $member->name }}" disabled>
                                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                        <input type="hidden" name="date" value="{{ $date }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="check_in" class="form-label">Heure d'arrivée</label>
                                                        <input type="time" class="form-control" id="check_in" name="check_in">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="check_out" class="form-label">Heure de départ</label>
                                                        <input type="time" class="form-control" id="check_out" name="check_out">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="status" class="form-label">Statut</label>
                                                        <select class="form-select" id="status" name="status">
                                                            <option value="present">Présent</option>
                                                            <option value="absent">Absent</option>
                                                            <option value="late">En retard</option>
                                                            <option value="half_day">Demi-journée</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-success">Ajouter</button>
                                                </div>
                                            </form>
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
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Résumé de la présence du jour</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyAttendanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-outline-success btn-lg">
                            <i class="fas fa-user-check me-2"></i>Marquer tous présents
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-file-export me-2"></i>Exporter les données
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-lg">
                            <i class="fas fa-envelope me-2"></i>Envoyer rappel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique de présence quotidienne
    const dailyCtx = document.getElementById('dailyAttendanceChart').getContext('2d');
    const presentCount = {{ $attendances->where('status', 'present')->count() }};
    const absentCount = {{ $attendances->where('status', 'absent')->count() }};
    const lateCount = {{ $attendances->where('status', 'late')->count() }};
    const notRecordedCount = {{ $teamWithoutAttendance->count() }};
    
    const dailyChart = new Chart(dailyCtx, {
        type: 'pie',
        data: {
            labels: ['Présent', 'Absent', 'En retard', 'Non enregistré'],
            datasets: [{
                data: [presentCount, absentCount, lateCount, notRecordedCount],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
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
</script>
@endsection