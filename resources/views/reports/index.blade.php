<!-- resources/views/reports/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Rapports Départementaux</h1>
        <div>
            <div class="dropdown d-inline-block me-2">
                <button class="btn btn-info dropdown-toggle" type="button" id="generateReportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-chart-line me-2"></i>Générer un rapport
                </button>
                <ul class="dropdown-menu" aria-labelledby="generateReportDropdown">
                    <li><a class="dropdown-item" href="{{ route('reports.generate.monthly') }}">Rapport mensuel</a></li>
                    <li><a class="dropdown-item" href="#">Rapport trimestriel</a></li>
                    <li><a class="dropdown-item" href="#">Rapport annuel</a></li>
                </ul>
            </div>
            
            <a href="{{ route('reports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Rapport
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('reports.index') }}" method="GET" class="d-flex">
                        <select name="type" class="form-select me-2" style="width: auto;">
                            <option value="">Tous les types</option>
                            <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                            <option value="quarterly" {{ request('type') == 'quarterly' ? 'selected' : '' }}>Trimestriel</option>
                            <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annuel</option>
                            <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                        </select>
                        <select name="status" class="form-select me-2" style="width: auto;">
                            <option value="">Tous les statuts</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publié</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Rechercher..." id="report-search">
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
                            <th>Période</th>
                            <th>Créé par</th>
                            <th>Date de création</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->title }}</td>
                            <td>
                                @if($report->type == 'monthly')
                                    <span class="badge bg-info">Mensuel</span>
                                @elseif($report->type == 'quarterly')
                                    <span class="badge bg-primary">Trimestriel</span>
                                @elseif($report->type == 'annual')
                                    <span class="badge bg-success">Annuel</span>
                                @else
                                    <span class="badge bg-secondary">Personnalisé</span>
                                @endif
                            </td>
                            <td>{{ $report->period_start->format('d/m/Y') }} - {{ $report->period_end->format('d/m/Y') }}</td>
                            <td>{{ $report->creator->name }}</td>
                            <td>{{ $report->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($report->status == 'draft')
                                    <span class="badge bg-secondary">Brouillon</span>
                                @else
                                    <span class="badge bg-success">Publié</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('reports.show', $report->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-download"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Rapports récents</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($reports->take(3) as $report)
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $report->title }}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">{{ $report->period_start->format('d/m/Y') }} - {{ $report->period_end->format('d/m/Y') }}</h6>
                                    <p class="card-text">{{ Str::limit($report->content, 100) }}</p>
                                    <div class="d-flex justify-content-between">
                                        <span class="badge {{ $report->status == 'published' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $report->status == 'published' ? 'Publié' : 'Brouillon' }}
                                        </span>
                                        <a href="{{ route('reports.show', $report->id) }}" class="card-link">Voir le rapport</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection