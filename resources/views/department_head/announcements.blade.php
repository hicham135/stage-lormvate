<!-- resources/views/department_head/announcements.blade.php -->
@extends('layouts.app')

@section('title', 'Annonces départementales')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Annonces départementales</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportAnnouncements">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                <i class="fas fa-plus me-1"></i> Nouvelle annonce
            </button>
        </div>
    </div>

    <!-- Vue d'ensemble -->
    <div class="row mb-4">
        <!-- Statistiques des annonces -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Vue d'ensemble</h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-primary">
                                <span>{{ count($activeAnnouncements->where('is_pinned', true)) }}</span>
                            </div>
                            <h6 class="mt-2">Épinglées</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-success">
                                <span>{{ count($activeAnnouncements) }}</span>
                            </div>
                            <h6 class="mt-2">Actives</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-secondary">
                                <span>{{ count($expiredAnnouncements) }}</span>
                            </div>
                            <h6 class="mt-2">Expirées</h6>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-circle bg-info">
                                <span>{{ count($activeAnnouncements->where('is_global', true)) }}</span>
                            </div>
                            <h6 class="mt-2">Globales</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphique activité des annonces -->
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Activité des annonces</h5>
                </div>
                <div class="card-body">
                    <canvas id="announcementActivityChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Annonces actives -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Annonces actives</h5>
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchActiveAnnouncement" placeholder="Rechercher...">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="searchActiveBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="activeAnnouncementsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Publié par</th>
                            <th>Date de publication</th>
                            <th>Expire le</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($activeAnnouncements) > 0)
                            @foreach($activeAnnouncements as $announcement)
                            <tr>
                                <td>
                                    @if($announcement->is_pinned)
                                        <i class="fas fa-thumbtack text-primary me-1" title="Épinglée"></i>
                                    @endif
                                    {{ $announcement->title }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light me-2">
                                            @if($announcement->user->profile_image)
                                                <img src="{{ asset('storage/' . $announcement->user->profile_image) }}" alt="{{ $announcement->user->name }}" class="avatar-img">
                                            @else
                                                <span class="avatar-text">{{ strtoupper(substr($announcement->user->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $announcement->user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ date('d/m/Y', strtotime($announcement->created_at)) }}</td>
                                <td>{{ $announcement->expires_at ? date('d/m/Y', strtotime($announcement->expires_at)) : 'Jamais' }}</td>
                                <td>
                                    @if($announcement->is_global)
                                        <span class="badge bg-info">Globale</span>
                                    @else
                                        <span class="badge bg-success">Départementale</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info view-announcement-btn"
                                                data-announcement-id="{{ $announcement->id }}"
                                                data-announcement-title="{{ $announcement->title }}"
                                                data-announcement-content="{{ $announcement->content }}"
                                                data-announcement-author="{{ $announcement->user->name }}"
                                                data-announcement-created="{{ date('d/m/Y', strtotime($announcement->created_at)) }}"
                                                data-announcement-expires="{{ $announcement->expires_at ? date('d/m/Y', strtotime($announcement->expires_at)) : 'Jamais' }}"
                                                data-announcement-is-pinned="{{ $announcement->is_pinned ? 'true' : 'false' }}"
                                                data-announcement-is-global="{{ $announcement->is_global ? 'true' : 'false' }}"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($announcement->user_id === Auth::id())
                                            <button type="button" class="btn btn-outline-primary edit-announcement-btn"
                                                    data-announcement-id="{{ $announcement->id }}"
                                                    data-announcement-title="{{ $announcement->title }}"
                                                    data-announcement-content="{{ $announcement->content }}"
                                                    data-announcement-expires="{{ $announcement->expires_at }}"
                                                    data-announcement-is-pinned="{{ $announcement->is_pinned ? 'true' : 'false' }}"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('department_head.delete_announcement', ['id' => $announcement->id]) }}" method="POST" class="d-inline delete-announcement-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger delete-announcement-btn" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Aucune annonce active</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Annonces des autres départements -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Annonces globales</h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGlobalAnnouncements" aria-expanded="false" aria-controls="collapseGlobalAnnouncements">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="collapseGlobalAnnouncements">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @php
                        // Simuler des annonces globales dans un environnement de démonstration
                        $globalAnnouncements = [
                            [
                                'title' => 'Mise à jour des politiques RH',
                                'content' => 'Une mise à jour des politiques de ressources humaines a été publiée. Tous les employés sont priés de consulter le document mis à jour.',
                                'department' => 'Ressources Humaines',
                                'author' => 'Karim Idrissi',
                                'date' => '10/05/2025',
                                'is_pinned' => true
                            ],
                            [
                                'title' => 'Maintenance informatique prévue',
                                'content' => 'Une maintenance des systèmes informatiques est prévue ce week-end. Certains services pourraient être indisponibles.',
                                'department' => 'IT',
                                'author' => 'Amine Benali',
                                'date' => '08/05/2025',
                                'is_pinned' => false
                            ],
                            [
                                'title' => 'Assemblée générale annuelle',
                                'content' => 'L\'assemblée générale annuelle se tiendra le 30 mai 2025 à 10h dans la salle de conférence principale.',
                                'department' => 'Direction',
                                'author' => 'Mohammed Khalid',
                                'date' => '05/05/2025',
                                'is_pinned' => true
                            ]
                        ];
                    @endphp
                    
                    @foreach($globalAnnouncements as $index => $announcement)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">
                                    @if($announcement['is_pinned'])
                                        <i class="fas fa-thumbtack text-primary me-1" title="Épinglée"></i>
                                    @endif
                                    {{ $announcement['title'] }}
                                </h5>
                                <div>
                                    <span class="badge bg-info me-1">{{ $announcement['department'] }}</span>
                                    <small class="text-muted">{{ $announcement['date'] }}</small>
                                </div>
                            </div>
                            <p class="mb-1">{{ $announcement['content'] }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Publié par: {{ $announcement['author'] }}</small>
                                <button type="button" class="btn btn-sm btn-outline-info view-global-btn" 
                                        data-announcement-index="{{ $index }}">
                                    <i class="fas fa-eye"></i> Voir détails
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Annonces expirées -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Annonces expirées</h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExpiredAnnouncements" aria-expanded="false" aria-controls="collapseExpiredAnnouncements">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="collapseExpiredAnnouncements">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="expiredAnnouncementsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Publié par</th>
                                <th>Date de publication</th>
                                <th>Expiré le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($expiredAnnouncements) > 0)
                                @foreach($expiredAnnouncements as $announcement)
                                <tr>
                                    <td>{{ $announcement->title }}</td>
                                    <td>{{ $announcement->user->name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($announcement->created_at)) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($announcement->expires_at)) }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info view-announcement-btn"
                                                    data-announcement-id="{{ $announcement->id }}"
                                                    data-announcement-title="{{ $announcement->title }}"
                                                    data-announcement-content="{{ $announcement->content }}"
                                                    data-announcement-author="{{ $announcement->user->name }}"
                                                    data-announcement-created="{{ date('d/m/Y', strtotime($announcement->created_at)) }}"
                                                    data-announcement-expires="{{ date('d/m/Y', strtotime($announcement->expires_at)) }}"
                                                    data-announcement-is-pinned="{{ $announcement->is_pinned ? 'true' : 'false' }}"
                                                    data-announcement-is-global="{{ $announcement->is_global ? 'true' : 'false' }}"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success republish-btn"
                                                    data-announcement-id="{{ $announcement->id }}"
                                                    data-announcement-title="{{ $announcement->title }}"
                                                    title="Republier">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Aucune annonce expirée</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour créer une annonce -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAnnouncementModalLabel">Créer une nouvelle annonce</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department_head.create_announcement') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="announcementTitle" class="form-label">Titre de l'annonce <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="announcementTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="announcementContent" class="form-label">Contenu <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="announcementContent" name="content" rows="5" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="announcementExpires" class="form-label">Date d'expiration</label>
                            <input type="date" class="form-control" id="announcementExpires" name="expires_at">
                            <small class="form-text text-muted">Laissez vide pour une annonce sans expiration.</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" value="1" id="announcementPinned" name="is_pinned">
                                <label class="form-check-label" for="announcementPinned">
                                    Épingler cette annonce
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Publier l'annonce</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails d'une annonce -->
<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-labelledby="viewAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAnnouncementModalLabel">Détails de l'annonce</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 id="viewAnnouncementTitle"></h4>
                            <div>
                                <span class="badge bg-info" id="viewAnnouncementGlobal" style="display: none;">Globale</span>
                                <span class="badge bg-success" id="viewAnnouncementDepartmental" style="display: none;">Départementale</span>
                                <span class="badge bg-primary" id="viewAnnouncementPinned" style="display: none;">Épinglée</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mb-3">
                            <div>
                                <strong>Publié par:</strong> <span id="viewAnnouncementAuthor"></span>
                            </div>
                            <div>
                                <strong>Date de publication:</strong> <span id="viewAnnouncementCreated"></span>
                            </div>
                            <div>
                                <strong>Expire le:</strong> <span id="viewAnnouncementExpires"></span>
                            </div>
                        </div>
                        <hr>
                        <div class="announcement-content p-3 bg-light rounded" id="viewAnnouncementContent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="editAnnouncementBtn" style="display: none;">Modifier</button>
                <button type="button" class="btn btn-danger" id="deleteAnnouncementBtn" style="display: none;">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour modifier une annonce -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAnnouncementModalLabel">Modifier l'annonce</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAnnouncementForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editAnnouncementTitle" class="form-label">Titre de l'annonce <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editAnnouncementTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editAnnouncementContent" class="form-label">Contenu <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editAnnouncementContent" name="content" rows="5" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editAnnouncementExpires" class="form-label">Date d'expiration</label>
                            <input type="date" class="form-control" id="editAnnouncementExpires" name="expires_at">
                            <small class="form-text text-muted">Laissez vide pour une annonce sans expiration.</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" value="1" id="editAnnouncementPinned" name="is_pinned">
                                <label class="form-check-label" for="editAnnouncementPinned">
                                    Épingler cette annonce
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour confirmer la suppression -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.</p>
                <p class="fw-bold" id="deleteAnnouncementTitle"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour republier une annonce -->
<div class="modal fade" id="republishModal" tabindex="-1" aria-labelledby="republishModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="republishModalLabel">Republier l'annonce</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="republishForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Vous êtes sur le point de republier l'annonce :</p>
                    <p class="fw-bold" id="republishAnnouncementTitle"></p>
                    <div class="mb-3">
                        <label for="republishExpires" class="form-label">Nouvelle date d'expiration</label>
                        <input type="date" class="form-control" id="republishExpires" name="expires_at" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="republishPinned" name="is_pinned">
                        <label class="form-check-label" for="republishPinned">
                            Épingler cette annonce
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Republier</button>
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
    .announcement-content {
        white-space: pre-line;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default expiration date to 1 month from now for new announcements
        const nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        document.getElementById('announcementExpires').valueAsDate = nextMonth;
        
        // Initialize activity chart
        initAnnouncementActivityChart();
        
        // Search functionality
        document.getElementById('searchActiveBtn').addEventListener('click', function() {
            searchTable('searchActiveAnnouncement', 'activeAnnouncementsTable');
        });
        
        document.getElementById('searchActiveAnnouncement').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchTable('searchActiveAnnouncement', 'activeAnnouncementsTable');
            }
        });
        
        // Export data
        document.getElementById('exportAnnouncements').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en cours de développement...');
        });
        
        // View announcement details
        document.querySelectorAll('.view-announcement-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const announcementId = this.getAttribute('data-announcement-id');
                const title = this.getAttribute('data-announcement-title');
                const content = this.getAttribute('data-announcement-content');
                const author = this.getAttribute('data-announcement-author');
                const created = this.getAttribute('data-announcement-created');
                const expires = this.getAttribute('data-announcement-expires');
                const isPinned = this.getAttribute('data-announcement-is-pinned') === 'true';
                const isGlobal = this.getAttribute('data-announcement-is-global') === 'true';
                
                // Set values in modal
                document.getElementById('viewAnnouncementTitle').textContent = title;
                document.getElementById('viewAnnouncementContent').textContent = content;
                document.getElementById('viewAnnouncementAuthor').textContent = author;
                document.getElementById('viewAnnouncementCreated').textContent = created;
                document.getElementById('viewAnnouncementExpires').textContent = expires;
                
                // Show/hide badges
                document.getElementById('viewAnnouncementPinned').style.display = isPinned ? 'inline-block' : 'none';
                document.getElementById('viewAnnouncementGlobal').style.display = isGlobal ? 'inline-block' : 'none';
                document.getElementById('viewAnnouncementDepartmental').style.display = isGlobal ? 'none' : 'inline-block';
                
                // Show/hide action buttons
                const currentUserId = "{{ Auth::id() }}";
                const isOwner = "{{ Auth::id() }}" === this.closest('tr').querySelector('.delete-announcement-form') !== null;
                
                document.getElementById('editAnnouncementBtn').style.display = isOwner ? 'inline-block' : 'none';
                document.getElementById('deleteAnnouncementBtn').style.display = isOwner ? 'inline-block' : 'none';
                
                // Set button data if owner
                if (isOwner) {
                    document.getElementById('editAnnouncementBtn').setAttribute('data-announcement-id', announcementId);
                    document.getElementById('deleteAnnouncementBtn').setAttribute('data-announcement-id', announcementId);
                    document.getElementById('deleteAnnouncementBtn').setAttribute('data-announcement-title', title);
                }
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('viewAnnouncementModal'));
                modal.show();
            });
        });
        
        // View global announcement details
        document.querySelectorAll('.view-global-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-announcement-index'));
                
                // Sample data for global announcements demo
                const globalAnnouncements = [
                    {
                        'title': 'Mise à jour des politiques RH',
                        'content': 'Une mise à jour des politiques de ressources humaines a été publiée. Tous les employés sont priés de consulter le document mis à jour.',
                        'department': 'Ressources Humaines',
                        'author': 'Karim Idrissi',
                        'date': '10/05/2025',
                        'expires': 'Jamais',
                        'is_pinned': true
                    },
                    {
                        'title': 'Maintenance informatique prévue',
                        'content': 'Une maintenance des systèmes informatiques est prévue ce week-end. Certains services pourraient être indisponibles.',
                        'department': 'IT',
                        'author': 'Amine Benali',
                        'date': '08/05/2025',
                        'expires': '15/05/2025',
                        'is_pinned': false
                    },
                    {
                        'title': 'Assemblée générale annuelle',
                        'content': 'L\'assemblée générale annuelle se tiendra le 30 mai 2025 à 10h dans la salle de conférence principale.',
                        'department': 'Direction',
                        'author': 'Mohammed Khalid',
                        'date': '05/05/2025',
                        'expires': '31/05/2025',
                        'is_pinned': true
                    }
                ];
                
                const announcement = globalAnnouncements[index];
                
                // Set values in modal
                document.getElementById('viewAnnouncementTitle').textContent = announcement.title;
                document.getElementById('viewAnnouncementContent').textContent = announcement.content;
                document.getElementById('viewAnnouncementAuthor').textContent = announcement.author;
                document.getElementById('viewAnnouncementCreated').textContent = announcement.date;
                document.getElementById('viewAnnouncementExpires').textContent = announcement.expires;
                
                // Show/hide badges
                document.getElementById('viewAnnouncementPinned').style.display = announcement.is_pinned ? 'inline-block' : 'none';
                document.getElementById('viewAnnouncementGlobal').style.display = 'inline-block';
                document.getElementById('viewAnnouncementDepartmental').style.display = 'none';
                
                // Hide action buttons for global announcements from other departments
                document.getElementById('editAnnouncementBtn').style.display = 'none';
                document.getElementById('deleteAnnouncementBtn').style.display = 'none';
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('viewAnnouncementModal'));
                modal.show();
            });
        });
        
        // Edit announcement button in view modal
        document.getElementById('editAnnouncementBtn').addEventListener('click', function() {
            const announcementId = this.getAttribute('data-announcement-id');
            
            // Find the edit button with this announcement-id and trigger a click
            document.querySelector(`.edit-announcement-btn[data-announcement-id="${announcementId}"]`).click();
            
            // Close the view modal
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewAnnouncementModal'));
            viewModal.hide();
        });
        
        // Delete announcement button in view modal
        document.getElementById('deleteAnnouncementBtn').addEventListener('click', function() {
            const announcementId = this.getAttribute('data-announcement-id');
            const title = this.getAttribute('data-announcement-title');
            
            // Set confirmation modal content
            document.getElementById('deleteAnnouncementTitle').textContent = title;
            
            // Set form submission target
            document.getElementById('confirmDeleteBtn').setAttribute('data-announcement-id', announcementId);
            
            // Close the view modal
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewAnnouncementModal'));
            viewModal.hide();
            
            // Show confirmation modal
            const confirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            confirmModal.show();
        });
        
        // Edit announcement button in table
        document.querySelectorAll('.edit-announcement-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const announcementId = this.getAttribute('data-announcement-id');
                const title = this.getAttribute('data-announcement-title');
                const content = this.getAttribute('data-announcement-content');
                const expires = this.getAttribute('data-announcement-expires');
                const isPinned = this.getAttribute('data-announcement-is-pinned') === 'true';
                
                // Set values in modal
                document.getElementById('editAnnouncementTitle').value = title;
                document.getElementById('editAnnouncementContent').value = content;
                
                if (expires) {
                    document.getElementById('editAnnouncementExpires').value = expires.split('/').reverse().join('-');
                } else {
                    document.getElementById('editAnnouncementExpires').value = '';
                }
                
                document.getElementById('editAnnouncementPinned').checked = isPinned;
                
                // Set form action (you need to implement the update route)
                document.getElementById('editAnnouncementForm').action = ''; // Add your update announcement route here
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editAnnouncementModal'));
                modal.show();
            });
        });
        
        // Delete announcement button in table
        document.querySelectorAll('.delete-announcement-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                const title = this.closest('tr').cells[0].textContent.trim();
                
                // Set confirmation modal content
                document.getElementById('deleteAnnouncementTitle').textContent = title;
                
                // Set form submission target
                document.getElementById('confirmDeleteBtn').setAttribute('data-form', form.id || 'delete-form-' + Math.random().toString(36).substr(2, 9));
                form.id = document.getElementById('confirmDeleteBtn').getAttribute('data-form');
                
                // Show confirmation modal
                const confirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                confirmModal.show();
            });
        });
        
        // Confirm delete button
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const formId = this.getAttribute('data-form');
            const announcementId = this.getAttribute('data-announcement-id');
            
            if (formId) {
                // Submit the form
                document.getElementById(formId).submit();
            } else if (announcementId) {
                // Find the form in the table
                const form = document.querySelector(`.delete-announcement-form input[value="${announcementId}"]`).closest('form');
                form.submit();
            }
            
            // Close the confirmation modal
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
            confirmModal.hide();
        });
        
        // Republish button
        document.querySelectorAll('.republish-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const announcementId = this.getAttribute('data-announcement-id');
                const title = this.getAttribute('data-announcement-title');
                
                // Set republish modal content
                document.getElementById('republishAnnouncementTitle').textContent = title;
                
                // Set default expiration date to 1 month from now
                const nextMonth = new Date();
                nextMonth.setMonth(nextMonth.getMonth() + 1);
                document.getElementById('republishExpires').valueAsDate = nextMonth;
                
                // Set form action (you need to implement the republish route)
                document.getElementById('republishForm').action = ''; // Add your republish announcement route here
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('republishModal'));
                modal.show();
            });
        });
    });

    function searchTable(inputId, tableId) {
        const searchValue = document.getElementById(inputId).value.toLowerCase();
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const title = rows[i].cells[0].textContent.toLowerCase();
            const author = rows[i].cells[1].textContent.toLowerCase();
            
            if (title.includes(searchValue) || author.includes(searchValue)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function initAnnouncementActivityChart() {
        var ctx = document.getElementById('announcementActivityChart').getContext('2d');
        
        // Sample data for announcement activity
        // In a real application, this would come from the backend
        var months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        var currentMonth = new Date().getMonth();
        var labels = [];
        
        // Get labels for the last 6 months
        for (var i = 5; i >= 0; i--) {
            var monthIndex = (currentMonth - i + 12) % 12; // This ensures we wrap around correctly
            labels.push(months[monthIndex]);
        }
        
        // Sample data for published and expired announcements
        var publishedData = [3, 5, 2, 4, 6, 8];
        var expiredData = [1, 2, 1, 3, 2, 3];
        var viewsData = [45, 60, 35, 55, 70, 90];
        
        var data = {
            labels: labels,
            datasets: [
                {
                    label: 'Publiées',
                    data: publishedData,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    yAxisID: 'y',
                    type: 'bar'
                },
                {
                    label: 'Expirées',
                    data: expiredData,
                    backgroundColor: 'rgba(108, 117, 125, 0.2)',
                    borderColor: 'rgba(108, 117, 125, 1)',
                    borderWidth: 2,
                    yAxisID: 'y',
                    type: 'bar'
                },
                {
                    label: 'Vues (total)',
                    data: viewsData,
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1',
                    type: 'line'
                }
            ]
        };
        
        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Nombre d\'annonces'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Nombre de vues'
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
</script>
@endsection