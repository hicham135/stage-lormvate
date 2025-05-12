<!-- resources/views/department_head/leave_requests.blade.php -->
@extends('layouts.app')

@section('title', 'Demandes de congés')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Demandes de congés</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportRequests">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <a href="{{ route('department_head.overtime_requests') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-clock me-1"></i> Heures supplémentaires
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Vue d'ensemble -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Vue d'ensemble</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="stat-circle bg-warning">
                                <span>{{ count($pendingRequests) }}</span>
                            </div>
                            <h6 class="mt-2">En attente</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-circle bg-success">
                                <span>{{ $processsedRequests->where('status', 'approved')->count() }}</span>
                            </div>
                            <h6 class="mt-2">Approuvées</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-circle bg-danger">
                                <span>{{ $processsedRequests->where('status', 'rejected')->count() }}</span>
                            </div>
                            <h6 class="mt-2">Rejetées</h6>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <canvas id="leaveTypesChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendrier des congés -->
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Calendrier des congés</h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="currentMonth">Ce mois</button>
                            <button type="button" class="btn btn-outline-secondary" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="leaveCalendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demandes en attente -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Demandes en attente</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchPending" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchPendingBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="pendingRequestsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Durée</th>
                            <th>Raison</th>
                            <th>Date demande</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($pendingRequests) > 0)
                            @foreach($pendingRequests as $request)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light me-2">
                                            @if($request->user->profile_image)
                                                <img src="{{ asset('storage/' . $request->user->profile_image) }}" alt="{{ $request->user->name }}" class="avatar-img">
                                            @else
                                                <span class="avatar-text">{{ strtoupper(substr($request->user->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $request->user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeText = 'Congé annuel';
                                        $typeIcon = 'umbrella-beach';
                                        
                                        if ($request->type === 'sick') {
                                            $typeText = 'Congé maladie';
                                            $typeIcon = 'user-md';
                                        } elseif ($request->type === 'personal') {
                                            $typeText = 'Congé personnel';
                                            $typeIcon = 'user';
                                        } elseif ($request->type === 'other') {
                                            $typeText = 'Autre congé';
                                            $typeIcon = 'calendar-alt';
                                        }
                                    @endphp
                                    <span class="badge bg-info">
                                        <i class="fas fa-{{ $typeIcon }} me-1"></i> {{ $typeText }}
                                    </span>
                                </td>
                                <td>{{ date('d/m/Y', strtotime($request->start_date)) }} - {{ date('d/m/Y', strtotime($request->end_date)) }}</td>
                                <td>
                                    @php
                                        $start = new DateTime($request->start_date);
                                        $end = new DateTime($request->end_date);
                                        $interval = $start->diff($end);
                                        echo $interval->days + 1 . ' jour(s)';
                                    @endphp
                                </td>
                                <td>{{ Str::limit($request->reason, 30) }}</td>
                                <td>{{ date('d/m/Y', strtotime($request->created_at)) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('department_head.approve_leave', ['id' => $request->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Approuver">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-outline-danger reject-leave-btn" 
                                                data-leave-id="{{ $request->id }}" title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info view-leave-btn"
                                                data-leave-id="{{ $request->id }}"
                                                data-leave-employee="{{ $request->user->name }}"
                                                data-leave-type="{{ $typeText }}"
                                                data-leave-start="{{ date('d/m/Y', strtotime($request->start_date)) }}"
                                                data-leave-end="{{ date('d/m/Y', strtotime($request->end_date)) }}"
                                                data-leave-created="{{ date('d/m/Y', strtotime($request->created_at)) }}"
                                                data-leave-reason="{{ $request->reason }}"
                                                data-leave-document="{{ $request->document_path }}"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">Aucune demande en attente</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Historique des demandes traitées -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Historique des demandes traitées</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchHistory" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchHistoryBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="processedRequestsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Durée</th>
                            <th>Statut</th>
                            <th>Traité par</th>
                            <th>Date traitement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($processsedRequests) > 0)
                            @foreach($processsedRequests as $request)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light me-2">
                                            @if($request->user->profile_image)
                                                <img src="{{ asset('storage/' . $request->user->profile_image) }}" alt="{{ $request->user->name }}" class="avatar-img">
                                            @else
                                                <span class="avatar-text">{{ strtoupper(substr($request->user->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $request->user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeText = 'Congé annuel';
                                        $typeIcon = 'umbrella-beach';
                                        
                                        if ($request->type === 'sick') {
                                            $typeText = 'Congé maladie';
                                            $typeIcon = 'user-md';
                                        } elseif ($request->type === 'personal') {
                                            $typeText = 'Congé personnel';
                                            $typeIcon = 'user';
                                        } elseif ($request->type === 'other') {
                                            $typeText = 'Autre congé';
                                            $typeIcon = 'calendar-alt';
                                        }
                                    @endphp
                                    <span class="badge bg-info">
                                        <i class="fas fa-{{ $typeIcon }} me-1"></i> {{ $typeText }}
                                    </span>
                                </td>
                                <td>{{ date('d/m/Y', strtotime($request->start_date)) }} - {{ date('d/m/Y', strtotime($request->end_date)) }}</td>
                                <td>
                                    @php
                                        $start = new DateTime($request->start_date);
                                        $end = new DateTime($request->end_date);
                                        $interval = $start->diff($end);
                                        echo $interval->days + 1 . ' jour(s)';
                                    @endphp
                                </td>
                                <td>
                                    @if($request->status === 'approved')
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-danger">Rejeté</span>
                                    @endif
                                </td>
                                <td>{{ $request->approver ? $request->approver->name : 'N/A' }}</td>
                                <td>{{ date('d/m/Y', strtotime($request->updated_at)) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info view-leave-btn"
                                            data-leave-id="{{ $request->id }}"
                                            data-leave-employee="{{ $request->user->name }}"
                                            data-leave-type="{{ $typeText }}"
                                            data-leave-start="{{ date('d/m/Y', strtotime($request->start_date)) }}"
                                            data-leave-end="{{ date('d/m/Y', strtotime($request->end_date)) }}"
                                            data-leave-created="{{ date('d/m/Y', strtotime($request->created_at)) }}"
                                            data-leave-reason="{{ $request->reason }}"
                                            data-leave-status="{{ $request->status }}"
                                            data-leave-approver="{{ $request->approver ? $request->approver->name : 'N/A' }}"
                                            data-leave-notes="{{ $request->notes }}"
                                            data-leave-document="{{ $request->document_path }}"
                                            title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted">Aucune demande traitée</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails d'une demande -->
<div class="modal fade" id="viewLeaveModal" tabindex="-1" aria-labelledby="viewLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewLeaveModalLabel">Détails de la demande de congé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Informations de base</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Employé:</th>
                                <td id="leaveEmployee"></td>
                            </tr>
                            <tr>
                                <th>Type de congé:</th>
                                <td id="leaveType"></td>
                            </tr>
                            <tr>
                                <th>Date de demande:</th>
                                <td id="leaveCreated"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Période</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Date de début:</th>
                                <td id="leaveStart"></td>
                            </tr>
                            <tr>
                                <th>Date de fin:</th>
                                <td id="leaveEnd"></td>
                            </tr>
                            <tr>
                                <th>Durée totale:</th>
                                <td id="leaveDuration"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Raison</h6>
                        <div class="p-3 bg-light rounded" id="leaveReason"></div>
                    </div>
                </div>
                
                <div class="row mb-4" id="leaveDocumentContainer">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Document justificatif</h6>
                        <div id="leaveDocument"></div>
                    </div>
                </div>
                
                <div class="row" id="leaveStatusContainer">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Statut de la demande</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Statut:</th>
                                <td id="leaveStatus"></td>
                            </tr>
                            <tr>
                                <th>Traité par:</th>
                                <td id="leaveApprover"></td>
                            </tr>
                            <tr id="leaveNotesRow">
                                <th>Notes:</th>
                                <td id="leaveNotes"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3" id="leaveActionsContainer">
                    <div class="col-md-12 d-flex">
                        <form id="approveLeaveForm" action="" method="POST" class="me-2">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Approuver
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" id="rejectLeaveBtn">
                            <i class="fas fa-times me-1"></i> Rejeter
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour rejeter une demande -->
<div class="modal fade" id="rejectLeaveModal" tabindex="-1" aria-labelledby="rejectLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectLeaveModalLabel">Rejeter la demande de congé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectLeaveForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
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
    .stat-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
    }
    .stat-circle span {
        font-size: 1.5rem;
        font-weight: bold;
    }
    #leaveCalendar {
        width: 100%;
        height: 300px;
        overflow: auto;
    }
    .calendar-day {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 2px;
        border-radius: 50%;
        cursor: pointer;
    }
    .calendar-day.today {
        background-color: #007bff;
        color: white;
    }
    .calendar-day.has-leave {
        background-color: #ffc107;
        color: black;
    }
    .calendar-day.weekend {
        color: #dc3545;
    }
    .calendar-day:hover {
        background-color: #e9ecef;
    }
    .calendar-header {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .calendar-month {
        font-weight: bold;
        margin-bottom: 10px;
        text-align: center;
        font-size: 1.2rem;
    }
    .calendar-row {
        display: flex;
        margin-bottom: 5px;
    }
    .calendar-cell {
        width: 40px;
        height: 40px;
        margin: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        initLeaveTypesChart();
        
        // Initialize calendar
        initLeaveCalendar();
        
        // Search functionality
        document.getElementById('searchPendingBtn').addEventListener('click', function() {
            searchTable('searchPending', 'pendingRequestsTable');
        });
        
        document.getElementById('searchPending').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchTable('searchPending', 'pendingRequestsTable');
            }
        });
        
        document.getElementById('searchHistoryBtn').addEventListener('click', function() {
            searchTable('searchHistory', 'processedRequestsTable');
        });
        
        document.getElementById('searchHistory').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchTable('searchHistory', 'processedRequestsTable');
            }
        });
        
        // Export data
        document.getElementById('exportRequests').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en cours de développement...');
        });
        
        // Calendar navigation
        document.getElementById('prevMonth').addEventListener('click', function() {
            // In a real app, this would navigate to the previous month
            alert('Navigation vers le mois précédent - fonctionnalité à implémenter');
        });
        
        document.getElementById('currentMonth').addEventListener('click', function() {
            // In a real app, this would navigate to the current month
            alert('Navigation vers le mois courant - fonctionnalité à implémenter');
        });
        
        document.getElementById('nextMonth').addEventListener('click', function() {
            // In a real app, this would navigate to the next month
            alert('Navigation vers le mois suivant - fonctionnalité à implémenter');
        });
        
        // View leave request details
        document.querySelectorAll('.view-leave-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const leaveId = this.getAttribute('data-leave-id');
                const employee = this.getAttribute('data-leave-employee');
                const type = this.getAttribute('data-leave-type');
                const startDate = this.getAttribute('data-leave-start');
                const endDate = this.getAttribute('data-leave-end');
                const createdDate = this.getAttribute('data-leave-created');
                const reason = this.getAttribute('data-leave-reason');
                const document = this.getAttribute('data-leave-document');
                const status = this.getAttribute('data-leave-status');
                const approver = this.getAttribute('data-leave-approver');
                const notes = this.getAttribute('data-leave-notes');
                
                // Calculate duration
                const start = new Date(startDate.split('/').reverse().join('-'));
                const end = new Date(endDate.split('/').reverse().join('-'));
                const timeDiff = Math.abs(end - start);
                const durationDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;
                
                // Set values in modal
                document.getElementById('leaveEmployee').textContent = employee;
                document.getElementById('leaveType').textContent = type;
                document.getElementById('leaveCreated').textContent = createdDate;
                document.getElementById('leaveStart').textContent = startDate;
                document.getElementById('leaveEnd').textContent = endDate;
                document.getElementById('leaveDuration').textContent = durationDays + ' jour(s)';
                document.getElementById('leaveReason').textContent = reason || 'Aucune raison fournie';
                
                // Handle document
                const docContainer = document.getElementById('leaveDocumentContainer');
                const docElement = document.getElementById('leaveDocument');
                
                if (document) {
                    docContainer.style.display = 'flex';
                    docElement.innerHTML = '<a href="' + "{{ asset('storage') }}/" + document + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-alt me-1"></i> Voir le document</a>';
                } else {
                    docContainer.style.display = 'none';
                }
                
                // Handle status section
                const statusContainer = document.getElementById('leaveStatusContainer');
                const actionsContainer = document.getElementById('leaveActionsContainer');
                const approveForm = document.getElementById('approveLeaveForm');
                const rejectBtn = document.getElementById('rejectLeaveBtn');
                
                if (status) {
                    // This is a processed request
                    statusContainer.style.display = 'flex';
                    actionsContainer.style.display = 'none';
                    
                    const statusElement = document.getElementById('leaveStatus');
                    statusElement.innerHTML = '';
                    
                    if (status === 'approved') {
                        statusElement.innerHTML = '<span class="badge bg-success">Approuvé</span>';
                    } else {
                        statusElement.innerHTML = '<span class="badge bg-danger">Rejeté</span>';
                    }
                    
                    document.getElementById('leaveApprover').textContent = approver;
                    
                    const notesRow = document.getElementById('leaveNotesRow');
                    const notesElement = document.getElementById('leaveNotes');
                    
                    if (notes) {
                        notesRow.style.display = 'table-row';
                        notesElement.textContent = notes;
                    } else {
                        notesRow.style.display = 'none';
                    }
                } else {
                    // This is a pending request
                    statusContainer.style.display = 'none';
                    actionsContainer.style.display = 'flex';
                    
                    // Set form actions
                    approveForm.action = "{{ route('department_head.approve_leave', ['id' => '']) }}" + leaveId;
                    
                    // Set reject button action
                    rejectBtn.setAttribute('data-leave-id', leaveId);
                }
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('viewLeaveModal'));
                modal.show();
            });
        });
        
        // Reject leave request button
        document.querySelectorAll('.reject-leave-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const leaveId = this.getAttribute('data-leave-id');
                document.getElementById('rejectLeaveForm').action = "{{ route('department_head.reject_leave', ['id' => '']) }}" + leaveId;
                
                const modal = new bootstrap.Modal(document.getElementById('rejectLeaveModal'));
                modal.show();
            });
        });
        
        // Modal reject button
        document.getElementById('rejectLeaveBtn').addEventListener('click', function() {
            const leaveId = this.getAttribute('data-leave-id');
            document.getElementById('rejectLeaveForm').action = "{{ route('department_head.reject_leave', ['id' => '']) }}" + leaveId;
            
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewLeaveModal'));
            viewModal.hide();
            
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectLeaveModal'));
            rejectModal.show();
        });
    });

    function searchTable(inputId, tableId) {
        const searchValue = document.getElementById(inputId).value.toLowerCase();
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const employeeName = rows[i].cells[0].textContent.toLowerCase();
            const leaveType = rows[i].cells[1].textContent.toLowerCase();
            const leavePeriod = rows[i].cells[2].textContent.toLowerCase();
            
            if (employeeName.includes(searchValue) || leaveType.includes(searchValue) || leavePeriod.includes(searchValue)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function initLeaveTypesChart() {
        var ctx = document.getElementById('leaveTypesChart').getContext('2d');
        
        // Sample data for leave types distribution
        // In a real application, this would come from the backend
        var data = {
            labels: ['Congé annuel', 'Congé maladie', 'Congé personnel', 'Autre'],
            datasets: [{
                data: [60, 20, 15, 5],
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                ],
                borderColor: [
                    'rgba(13, 110, 253, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(25, 135, 84, 1)',
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
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 10
                        }
                    },
                    title: {
                        display: true,
                        text: 'Répartition par type de congé'
                    }
                }
            }
        });
    }
    
    function initLeaveCalendar() {
        const calendarEl = document.getElementById('leaveCalendar');
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth();
        
        // Create calendar header
        const calendarHeader = document.createElement('div');
        calendarHeader.className = 'calendar-month';
        calendarHeader.textContent = new Intl.DateTimeFormat('fr-FR', { month: 'long', year: 'numeric' }).format(currentDate);
        calendarEl.appendChild(calendarHeader);
        
        // Create day headers (Mon, Tue, etc)
        const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        const headerRow = document.createElement('div');
        headerRow.className = 'calendar-row';
        
        dayNames.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-cell calendar-header';
            dayHeader.textContent = day;
            headerRow.appendChild(dayHeader);
        });
        
        calendarEl.appendChild(headerRow);
        
        // Get first day of month and last day of month
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        
        // Adjust first day to start on Monday (1) instead of Sunday (0)
        let firstDayOfWeek = firstDay.getDay() || 7; // Sunday is 0, convert to 7
        firstDayOfWeek--; // Adjust to make Monday 0, Sunday 6
        
        // Create calendar days
        let currentRow = document.createElement('div');
        currentRow.className = 'calendar-row';
        
        // Add empty cells for days before first day of month
        for (let i = 0; i < firstDayOfWeek; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'calendar-cell';
            currentRow.appendChild(emptyCell);
        }
        
        // Sample leave data (in a real app, this would come from the backend)
        // Format: [day, type] where type is: 'approved', 'pending', 'multiple'
        const leaveData = [
            [5, 'approved'],
            [6, 'approved'],
            [7, 'approved'],
            [12, 'pending'],
            [15, 'pending'],
            [16, 'pending'],
            [20, 'multiple'],
            [25, 'approved'],
            [26, 'approved']
        ];
        
        // Add day cells
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const date = new Date(currentYear, currentMonth, day);
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;
            const isToday = date.toDateString() === currentDate.toDateString();
            
            // Check if this day has any leave requests
            const leaveInfo = leaveData.find(item => item[0] === day);
            const hasLeave = !!leaveInfo;
            const leaveType = hasLeave ? leaveInfo[1] : null;
            
            const dayCell = document.createElement('div');
            dayCell.className = 'calendar-cell';
            
            const dayElement = document.createElement('div');
            dayElement.textContent = day;
            dayElement.className = 'calendar-day';
            
            if (isToday) {
                dayElement.classList.add('today');
            } else if (hasLeave) {
                dayElement.classList.add('has-leave');
                
                // Add tooltip with leave info
                let tooltipText = '';
                if (leaveType === 'approved') {
                    tooltipText = 'Congé approuvé';
                    dayElement.style.backgroundColor = '#28a745';
                    dayElement.style.color = 'white';
                } else if (leaveType === 'pending') {
                    tooltipText = 'Demande en attente';
                    dayElement.style.backgroundColor = '#ffc107';
                } else if (leaveType === 'multiple') {
                    tooltipText = 'Plusieurs demandes';
                    dayElement.style.backgroundColor = '#17a2b8';
                    dayElement.style.color = 'white';
                }
                
                dayElement.title = tooltipText;
            } else if (isWeekend) {
                dayElement.classList.add('weekend');
            }
            
            dayCell.appendChild(dayElement);
            currentRow.appendChild(dayCell);
            
            // Start a new row after Saturday (or when row is full)
            if (currentRow.children.length === 7) {
                calendarEl.appendChild(currentRow);
                currentRow = document.createElement('div');
                currentRow.className = 'calendar-row';
            }
        }
        
        // Add the last row if it has any days
        if (currentRow.children.length > 0) {
            calendarEl.appendChild(currentRow);
        }
    }
</script>
@endsection