@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Demandes</h1>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('requests.index') }}" method="GET" class="d-flex">
                        <select name="type" class="form-select me-2" style="width: auto;">
                            <option value="">Tous les types</option>
                            <option value="leave" {{ request('type') == 'leave' ? 'selected' : '' }}>Congé</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Remboursement</option>
                            <option value="equipment" {{ request('type') == 'equipment' ? 'selected' : '' }}>Équipement</option>
                            <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                        <select name="status" class="form-select me-2" style="width: auto;">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Rechercher..." id="request-search">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Demandeur</th>
                            <th>Date de demande</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>{{ $request->title }}</td>
                            <td>
                                @if($request->type == 'leave')
                                    <span class="badge bg-info">Congé</span>
                                @elseif($request->type == 'expense')
                                    <span class="badge bg-warning">Remboursement</span>
                                @elseif($request->type == 'equipment')
                                    <span class="badge bg-primary">Équipement</span>
                                @else
                                    <span class="badge bg-secondary">Autre</span>
                                @endif
                            </td>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning">En attente</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success">Approuvé</span>
                                @else
                                    <span class="badge bg-danger">Rejeté</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('requests.show', $request->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($request->status == 'pending')
                                    <form action="{{ route('requests.approve', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('requests.reject', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
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
                    <h5 class="mb-0">Demandes par type</h5>
                </div>
                <div class="card-body">
                    <canvas id="requestTypeChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Demandes par statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="requestStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique des demandes par type
    const typeCtx = document.getElementById('requestTypeChart').getContext('2d');
    const leaveCount = {{ $requests->where('type', 'leave')->count() }};
    const expenseCount = {{ $requests->where('type', 'expense')->count() }};
    const equipmentCount = {{ $requests->where('type', 'equipment')->count() }};
    const otherCount = {{ $requests->where('type', 'other')->count() }};
    
    const typeChart = new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: ['Congé', 'Remboursement', 'Équipement', 'Autre'],
            datasets: [{
                data: [leaveCount, expenseCount, equipmentCount, otherCount],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
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
    
    // Graphique des demandes par statut
    const statusCtx = document.getElementById('requestStatusChart').getContext('2d');
    const pendingCount = {{ $requests->where('status', 'pending')->count() }};
    const approvedCount = {{ $requests->where('status', 'approved')->count() }};
    const rejectedCount = {{ $requests->where('status', 'rejected')->count() }};
    
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['En attente', 'Approuvé', 'Rejeté'],
            datasets: [{
                data: [pendingCount, approvedCount, rejectedCount],
                backgroundColor: [
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
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