@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails de la Demande</h1>
        <a href="{{ route('employee.requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="mb-0">{{ $employeeRequest->title }}</h5>
            <span class="ms-auto">
                @if($employeeRequest->type == 'leave')
                    <span class="badge bg-info">Congé</span>
                @elseif($employeeRequest->type == 'expense')
                    <span class="badge bg-warning">Remboursement</span>
                @elseif($employeeRequest->type == 'equipment')
                    <span class="badge bg-primary">Équipement</span>
                @else
                    <span class="badge bg-secondary">Autre</span>
                @endif
                
                @if($employeeRequest->status == 'pending')
                    <span class="badge bg-warning">En attente</span>
                @elseif($employeeRequest->status == 'approved')
                    <span class="badge bg-success">Approuvé</span>
                @else
                    <span class="badge bg-danger">Rejeté</span>
                @endif
            </span>
        </div>
        <div class="card-body">
            <h6 class="mb-3">Description de la demande</h6>
            <p>{{ $employeeRequest->description }}</p>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Détails de la demande</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Date de soumission:</span>
                            <span>{{ $employeeRequest->created_at->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Département:</span>
                            <span>{{ $employeeRequest->department->name }}</span>
                        </li>
                        @if($employeeRequest->approved_at)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Date de réponse:</span>
                            <span>{{ $employeeRequest->approved_at->format('d/m/Y') }}</span>
                        </li>
                        @endif
                        @if($employeeRequest->approver)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Traité par:</span>
                            <span>{{ $employeeRequest->approver->name }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Fichiers joints</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <div>
                                    <div>Justificatif.pdf</div>
                                    <small class="text-muted">Ajouté le {{ $employeeRequest->created_at->format('d/m/Y') }}</small>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary ms-auto">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </li>
                        
                        @if($employeeRequest->type == 'expense')
                        <li class="list-group-item px-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-image text-primary me-2"></i>
                                <div>
                                    <div>Facture.jpg</div>
                                    <small class="text-muted">Ajouté le {{ $employeeRequest->created_at->format('d/m/Y') }}</small>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary ms-auto">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            @if($employeeRequest->status == 'pending')
                <div class="alert alert-warning">
                    <i class="fas fa-clock me-2"></i>Votre demande est en cours d'examen par votre responsable.
                </div>
            @elseif($employeeRequest->status == 'approved')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>Votre demande a été approuvée le {{ $employeeRequest->approved_at->format('d/m/Y') }} par {{ $employeeRequest->approver->name }}.
                </div>
            @else
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i>Votre demande a été rejetée le {{ $employeeRequest->approved_at->format('d/m/Y') }} par {{ $employeeRequest->approver->name }}.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection