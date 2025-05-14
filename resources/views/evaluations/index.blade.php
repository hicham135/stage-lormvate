<!-- resources/views/evaluations/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Évaluations des Employés</h1>
        <a href="{{ route('evaluations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Évaluation
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('evaluations.index') }}" method="GET" class="d-flex">
                        <select name="period" class="form-select me-2" style="width: auto;">
                            <option value="">Toutes les périodes</option>
                            <option value="Q1 2024" {{ request('period') == 'Q1 2024' ? 'selected' : '' }}>Q1 2024</option>
                            <option value="Q2 2024" {{ request('period') == 'Q2 2024' ? 'selected' : '' }}>Q2 2024</option>
                            <option value="Q3 2024" {{ request('period') == 'Q3 2024' ? 'selected' : '' }}>Q3 2024</option>
                            <option value="Q4 2024" {{ request('period') == 'Q4 2024' ? 'selected' : '' }}>Q4 2024</option>
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
                        <input type="text" class="form-control" placeholder="Rechercher..." id="evaluation-search">
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
                            <th>Employé</th>
                            <th>Période</th>
                            <th>Perf.</th>
                            <th>Comm.</th>
                            <th>Équipe</th>
                            <th>Innov.</th>
                            <th>Moyenne</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                        <tr>
                            <td>{{ $evaluation->evaluatedUser->name }}</td>
                            <td>{{ $evaluation->period }}</td>
                            <td>{{ $evaluation->performance_score }}/10</td>
                            <td>{{ $evaluation->communication_score }}/10</td>
                            <td>{{ $evaluation->teamwork_score }}/10</td>
                            <td>{{ $evaluation->innovation_score }}/10</td>
                            <td>
                                @php
                                    $average = ($evaluation->performance_score + $evaluation->communication_score + $evaluation->teamwork_score + $evaluation->innovation_score) / 4;
                                @endphp
                                <span class="badge {{ $average >= 8 ? 'bg-success' : ($average >= 6 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ number_format($average, 1) }}/10
                                </span>
                            </td>
                            <td>
                                @if($evaluation->status == 'draft')
                                    <span class="badge bg-secondary">Brouillon</span>
                                @else
                                    <span class="badge bg-success">Publié</span>
                                @endif
                            </td>
                            <td>{{ $evaluation->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('evaluations.show', $evaluation->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Performance de l'équipe</h5>
                </div>
                <div class="card-body">
                    <canvas id="teamPerformanceChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Progression des évaluations</h5>
                </div>
                <div class="card-body">
                    <canvas id="evaluationProgressChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Graphique de performance de l'équipe
    const performanceCtx = document.getElementById('teamPerformanceChart').getContext('2d');
    
    // Collecter les données pour les employés ayant des évaluations
    const employeeNames = [];
    const performanceData = [];
    const communicationData = [];
    const teamworkData = [];
    const innovationData = [];
    
    @foreach($evaluations->groupBy('evaluated_user_id') as $userId => $userEvals)
        @php
            $latestEval = $userEvals->sortByDesc('created_at')->first();
        @endphp
        
        employeeNames.push("{{ $latestEval->evaluatedUser->name }}");
        performanceData.push({{ $latestEval->performance_score }});
        communicationData.push({{ $latestEval->communication_score }});
        teamworkData.push({{ $latestEval->teamwork_score }});
        innovationData.push({{ $latestEval->innovation_score }});
    @endforeach
    
    const performanceChart = new Chart(performanceCtx, {
        type: 'radar',
        data: {
            labels: ['Performance', 'Communication', 'Travail d\'équipe', 'Innovation'],
            datasets: Array.from({ length: employeeNames.length }, (_, i) => ({
                label: employeeNames[i],
                data: [
                    performanceData[i],
                    communicationData[i],
                    teamworkData[i],
                    innovationData[i]
                ],
                fill: true,
                backgroundColor: `rgba(${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, 0.2)`,
                borderColor: `rgba(${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, 1)`,
                pointBackgroundColor: `rgba(${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, 1)`,
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: `rgba(${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, 1)`
            }))
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    min: 0,
                    max: 10,
                    ticks: {
                        stepSize: 2
                    }
                }
            }
        }
    });
    
    // Graphique de progression des évaluations
    const progressCtx = document.getElementById('evaluationProgressChart').getContext('2d');
    
    // Simuler des données de progression (à remplacer par des données réelles)
    const progressChart = new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024', 'Q2 2024'],
            datasets: [{
                label: 'Moyenne de performance',
                data: [7.2, 7.5, 7.8, 8.0, 8.2, 8.5],
                borderColor: 'rgba(0, 123, 255, 0.8)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false,
                    min: 5,
                    max: 10
                }
            }
        }
    });
</script>
@endsection
