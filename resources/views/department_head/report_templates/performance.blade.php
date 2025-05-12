<!-- resources/views/department_head/report_templates/performance.blade.php -->
<div class="report-content">
    <div class="report-section">
        <h3 class="report-section-title">Résumé des performances</h3>
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-primary">4.2</div>
                    <div class="metric-label">Performance moyenne</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-primary">3.9</div>
                    <div class="metric-label">Ponctualité moyenne</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-primary">4.5</div>
                    <div class="metric-label">Travail d'équipe moyen</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-primary">3.8</div>
                    <div class="metric-label">Initiative moyenne</div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <canvas id="performanceScoresChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <canvas id="performanceTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Détail par employé</h3>
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Performance</th>
                        <th>Ponctualité</th>
                        <th>Travail d'équipe</th>
                        <th>Initiative</th>
                        <th>Score global</th>
                        <th>Nbr évaluations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['data'] as $performanceData)
                    <tr>
                        <td>{{ $performanceData['employee']->name }}</td>
                        <td class="text-center">{{ $performanceData['avg_performance'] }}</td>
                        <td class="text-center">{{ $performanceData['avg_punctuality'] }}</td>
                        <td class="text-center">{{ $performanceData['avg_teamwork'] }}</td>
                        <td class="text-center">{{ $performanceData['avg_initiative'] }}</td>
                        <td class="text-center">
                            @php
                                $avgScore = $performanceData['avg_total'];
                                
                                if ($avgScore >= 4.5) {
                                    $scoreClass = 'text-success';
                                } elseif ($avgScore >= 3.5) {
                                    $scoreClass = 'text-info';
                                } elseif ($avgScore >= 2.5) {
                                    $scoreClass = 'text-warning';
                                } else {
                                    $scoreClass = 'text-danger';
                                }
                            @endphp
                            <span class="{{ $scoreClass }} fw-bold">{{ $avgScore }}</span>
                        </td>
                        <td class="text-center">{{ $performanceData['evaluations_count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Meilleures performances</h3>
        <div class="row">
            @php
                // Tri du tableau par score global décroissant
                usort($reportData['data'], function ($a, $b) {
                    return $b['avg_total'] <=> $a['avg_total'];
                });
                
                // Prendre les 3 premiers éléments
                $topPerformers = array_slice($reportData['data'], 0, 3);
            @endphp
            
            @foreach($topPerformers as $performer)
            <div class="col-md-4 mb-3">
                <div class="employee-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle bg-light me-3" style="width: 50px; height: 50px;">
                            @if($performer['employee']->profile_image)
                                <img src="{{ asset('storage/' . $performer['employee']->profile_image) }}" alt="{{ $performer['employee']->name }}" class="avatar-img">
                            @else
                                <span class="avatar-text">{{ strtoupper(substr($performer['employee']->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $performer['employee']->name }}</h5>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($performer['avg_total']))
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Performance:</span>
                            <span>{{ $performer['avg_performance'] }}/5</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $performer['avg_performance'] / 5 * 100 }}%;" aria-valuenow="{{ $performer['avg_performance'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ponctualité:</span>
                            <span>{{ $performer['avg_punctuality'] }}/5</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $performer['avg_punctuality'] / 5 * 100 }}%;" aria-valuenow="{{ $performer['avg_punctuality'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Travail d'équipe:</span>
                            <span>{{ $performer['avg_teamwork'] }}/5</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $performer['avg_teamwork'] / 5 * 100 }}%;" aria-valuenow="{{ $performer['avg_teamwork'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Initiative:</span>
                            <span>{{ $performer['avg_initiative'] }}/5</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $performer['avg_initiative'] / 5 * 100 }}%;" aria-valuenow="{{ $performer['avg_initiative'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Observations et recommandations</h3>
        <div class="summary-box">
            <h5>Observations</h5>
            <p>La performance globale du département est bonne avec un score moyen de 4.1/5. Le travail d'équipe ressort comme le point fort (4.5/5), tandis que l'initiative est le critère avec la marge d'amélioration la plus importante (3.8/5).</p>
            <p>On observe une tendance positive sur les 5 derniers mois, avec une amélioration continue des scores moyens.</p>
            
            <h5 class="mt-4">Recommandations</h5>
            <ol>
                <li>Organiser des ateliers sur la prise d'initiative et l'autonomie pour renforcer ce critère.</li>
                <li>Mettre en place un système de mentorat entre les employés les mieux évalués et ceux qui ont besoin de progresser.</li>
                <li>Continuer à renforcer l'esprit d'équipe qui constitue un point fort du département.</li>
            </ol>
        </div>
    </div>
</div>