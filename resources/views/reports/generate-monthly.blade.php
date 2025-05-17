<!-- resources/views/reports/generate-monthly.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Générer un Rapport Mensuel</h1>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button type="button" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Sauvegarder
            </button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rapport de performance départemental - {{ $startDate->format('M Y') }}</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-download me-1"></i>PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-file-excel me-1"></i>Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="report-title" class="form-label">Titre du rapport</label>
                                    <input type="text" class="form-control" id="report-title" name="title" value="Rapport mensuel de performance - {{ $startDate->format('F Y') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="period-start" class="form-label">Début</label>
                                            <input type="date" class="form-control" id="period-start" name="period_start" value="{{ $startDate->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="period-end" class="form-label">Fin</label>
                                            <input type="date" class="form-control" id="period-end" name="period_end" value="{{ $endDate->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Résumé exécutif</h6>
                                    </div>
                                    <div class="card-body">
                                        <textarea class="form-control" name="executive_summary" rows="5" placeholder="Saisissez un résumé des points clés du rapport...">Durant le mois de {{ $startDate->format('F Y') }}, notre département a atteint la plupart de ses objectifs avec un taux de complétion des tâches de {{ $teamPerformance['tasks_completed'] }} sur {{ $teamPerformance['tasks_completed'] + 5 }} assignées. Le taux de présence était de {{ round(($teamPerformance['present_count'] / $teamPerformance['total_attendance']) * 100) }}%, ce qui est conforme à nos attentes.</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Points forts du mois</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="card bg-success text-white">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="text-white mb-0">Tâches complétées</h6>
                                                                <h3 class="text-white mb-0">{{ $teamPerformance['tasks_completed'] }}</h3>
                                                            </div>
                                                            <i class="fas fa-tasks fa-2x opacity-50"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card bg-primary text-white">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="text-white mb-0">Taux de présence</h6>
                                                                <h3 class="text-white mb-0">{{ round(($teamPerformance['present_count'] / $teamPerformance['total_attendance']) * 100) }}%</h3>
                                                            </div>
                                                            <i class="fas fa-user-check fa-2x opacity-50"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card bg-warning text-white">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="text-white mb-0">Employés</h6>
                                                                <h3 class="text-white mb-0">{{ $teamPerformance['total_employees'] }}</h3>
                                                            </div>
                                                            <i class="fas fa-users fa-2x opacity-50"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card bg-info text-white">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="text-white mb-0">Satisfaction</h6>
                                                                <h3 class="text-white mb-0">8.7/10</h3>
                                                            </div>
                                                            <i class="fas fa-smile fa-2x opacity-50"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Performance de l'équipe</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <canvas id="taskPerformanceChart" height="260"></canvas>
                                            </div>
                                            <div class="col-md-6">
                                                <canvas id="attendancePerformanceChart" height="260"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Analyse détaillée</h6>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="mb-3">1. Réalisation des objectifs</h6>
                                        <textarea class="form-control mb-3" name="objectives_analysis" rows="3" placeholder="Analyse des objectifs...">L'équipe a atteint 90% des objectifs fixés pour ce mois. Nos points forts incluent le développement des nouvelles fonctionnalités et l'optimisation des processus internes. Cependant, nous avons rencontré quelques défis concernant les délais de livraison de certains projets.</textarea>
                                        
                                        <h6 class="mb-3">2. Performance individuelle</h6>
                                        <textarea class="form-control mb-3" name="individual_performance" rows="3" placeholder="Analyse des performances individuelles...">Plusieurs membres de l'équipe se sont distingués ce mois-ci. Marie Martin a excellé dans la coordination du projet X, tandis que Jean Dupont a apporté des solutions innovantes pour résoudre les problèmes techniques rencontrés.</textarea>
                                        
                                        <h6 class="mb-3">3. Défis et solutions</h6>
                                        <textarea class="form-control mb-3" name="challenges_solutions" rows="3" placeholder="Défis rencontrés et solutions proposées...">Nous avons fait face à des défis liés à la communication entre les équipes et à la gestion des priorités. Pour y remédier, nous avons mis en place des réunions hebdomadaires de coordination et amélioré notre système de suivi des tâches.</textarea>
                                        
                                        <h6 class="mb-3">4. Recommandations</h6>
                                        <textarea class="form-control" name="recommendations" rows="3" placeholder="Recommandations pour amélioration...">Pour améliorer davantage notre performance, je recommande : 1) une formation supplémentaire sur les nouveaux outils, 2) une meilleure répartition des tâches en fonction des compétences, et 3) l'établissement d'objectifs plus clairs et mesurables pour le prochain mois.</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Prévisions et objectifs pour le mois prochain</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="next-month-objectives" class="form-label">Objectifs principaux</label>
                                            <textarea class="form-control" id="next-month-objectives" name="next_month_objectives" rows="3">1. Compléter le projet X avant la date limite du 15.
2. Améliorer le taux de résolution des problèmes techniques de 15%.
3. Développer et déployer la version beta du nouvel outil interne.
4. Réduire le taux d'absentéisme à moins de 5%.</textarea>
                                        </div>
                                        
                                        </span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Créer la tâche</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
