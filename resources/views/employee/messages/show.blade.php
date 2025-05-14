@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails du Message</h1>
        <a href="{{ route('employee.messages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-light {{ $message->is_announcement ? 'border-top border-info border-3' : '' }}">
            <div>
                <h5 class="mb-0">{{ $message->title }}</h5>
                @if($message->is_announcement)
                    <span class="badge bg-info">Annonce</span>
                @endif
            </div>
            <small class="text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</small>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.2rem;">
                        {{ substr($message->sender->name, 0, 1) }}
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">{{ $message->sender->name }}</h6>
                        <p class="text-muted small mb-0">{{ $message->sender->role == 'department_head' ? 'Chef de département' : $message->sender->role }}</p>
                    </div>
                </div>
                
                <div class="message-content">
                    <p>{{ $message->content }}</p>
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Détails</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Date d'envoi:</span>
                            <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        @if($message->read_at)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Lu le:</span>
                            <span>{{ $message->read_at->format('d/m/Y H:i') }}</span>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Type:</span>
                            <span>{{ $message->is_announcement ? 'Annonce départementale' : 'Message personnel' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Actions</h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fas fa-reply me-2"></i>Répondre
                        </button>
                        <button type="button" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-2"></i>Imprimer
                        </button>
                        <button type="button" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
            
            @if($message->is_announcement)
            <div class="alert alert-info mt-4">
                <i class="fas fa-bullhorn me-2"></i>Cette annonce a été envoyée à tous les membres du département {{ $message->department->name }}.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

<!-- Code JavaScript pour lier les interfaces du Chef de Département et de l'Employé -->
<script>
// Ce script pourrait être placé dans le fichier main.js pour faciliter l'interaction entre les deux interfaces

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes dans l'interface du chef ou de l'employé
    const isManagerInterface = document.getElementById('sidebar-wrapper')?.classList.contains('bg-primary');
    const isEmployeeInterface = document.getElementById('sidebar-wrapper')?.classList.contains('bg-success');
    
    // Fonction pour basculer entre les interfaces (pourrait être liée à un bouton)
    function switchInterface() {
        if (isManagerInterface) {
            // Redirection vers l'interface employé
            window.location.href = '/employee';
        } else if (isEmployeeInterface) {
            // Redirection vers l'interface chef de département
            window.location.href = '/';
        }
    }
    
    // Synchronisation des données entre les interfaces
    // Par exemple, quand un chef assigne une tâche à un employé, elle apparaît immédiatement dans l'interface de l'employé
    // Ou quand un employé modifie le statut d'une tâche, cela se reflète dans l'interface du chef
    
    // Cela peut être fait soit avec des technologies comme WebSockets,
    // soit simplement en rafraîchissant les données à partir de la base de données à chaque changement de page
});
</script>