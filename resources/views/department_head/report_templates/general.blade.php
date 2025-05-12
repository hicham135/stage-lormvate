<!-- resources/views/department_head/report_templates/general.blade.php -->
<div class="report-content">
    <div class="report-section">
        <h3 class="report-section-title">Vue d'ensemble du département</h3>
        <div class="summary-box mb-4">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Département:</strong> {{ $reportData['department']->name }}</p>
                    <p><strong>Période du rapport:</strong> {{ $reportData['period'] }}</p>
                    <p><strong>Nombre d'employés:</strong> {{ $reportData['employee_count'] }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Indicateurs clés</h5>
                    <p>
                        <strong>Taux de présence:</strong>
                        <span class="{{ $reportData['attendance_stats']['present'] / ($reportData['attendance_stats']['total'] ?: 1) * 100 >= 85 ? 'text-success' : 'text-warning' }}">
                            {{ $reportData['attendance_stats']['total'] ? round($reportData['attendance_stats']['present'] / $reportData['attendance_stats']['total'] * 100) : 0 }}%
                        </span>
                    </p>
                    <p>
                        <strong>Taux d'achèvement des tâches:</strong>
                        <span class="{{ $reportData['task_stats']['completed'] / ($reportData['task_stats']['total'] ?: 1) * 100 >= 75 ? 'text-success' : 'text-warning' }}">
                            {{ $reportData['task_stats']['total'] ? round($reportData['task_stats']['completed'] / $reportData['task_stats']['total'] * 100) : 0 }}%
                        </span>
                    </p>
                    <p>
                        <strong>Performance moyenne:</strong>
                        <span class="{{ $reportData['evaluation_stats']['avg_performance'] >= 4 ? 'text-success' : 'text-warning' }}">
                            {{ $reportData['evaluation_stats']['avg_performance'] }}/5
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Résumé des présences</h3>
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Statistiques de présence</h5>
                    <ul>
                        <li><strong>Jours de présence:</strong> {{ $reportData['attendance_stats']['present'] }}</li>
                        <li><strong>Jours de retard:</strong> {{ $reportData['attendance_stats']['late'] }}</li>
                        <li><strong>Jours d'absence:</strong> {{ $reportData['attendance_stats']['absent'] }}</li>
                        <li><strong>Jours de congé:</strong> {{ $reportData['attendance_stats']['leave'] }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <canvas id="attendanceTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Résumé des tâches</h3>
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Statistiques des tâches</h5>
                    <ul>
                        <li><strong>Tâches totales:</strong> {{ $reportData['task_stats']['total'] }}</li>
                        <li><strong>Tâches terminées:</strong> {{ $reportData['task_stats']['completed'] }}</li>
                        <li><strong>Tâches en cours:</strong> {{ $reportData['task_stats']['in_progress'] }}</li>
                        <li><strong>Tâches en attente:</strong> {{ $reportData['task_stats']['pending'] }}</li>
                        <li><strong>Tâches retardées:</strong> {{ $reportData['task_stats']['delayed'] }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <canvas id="tasksStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Résumé des performances</h3>
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Scores moyens</h5>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Performance:</span>
                            <span>{{ $reportData['evaluation_stats']['avg_performance'] }}/5</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $reportData['evaluation_stats']['avg_performance'] / 5 * 100 }}%;" aria-valuenow="{{ $reportData['evaluation_stats']['avg_performance'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ponctualité:</span>
                            <span>{{ $reportData['evaluation_stats']['avg_punctuality'] }}/5</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $reportData['evaluation_stats']['avg_punctuality'] / 5 * 100 }}%;" aria-valuenow="{{ $reportData['evaluation_stats']['avg_punctuality'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Travail d'équipe:</span>
                            <span>{{ $reportData['evaluation_stats']['avg_teamwork'] }}/5</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $reportData['evaluation_stats']['avg_teamwork'] / 5 * 100 }}%;" aria-valuenow="{{ $reportData['evaluation_stats']['avg_teamwork'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Initiative:</span>
                            <span>{{ $reportData['evaluation_stats']['avg_initiative'] }}/5</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $reportData['evaluation_stats']['avg_initiative'] / 5 * 100 }}%;" aria-valuenow="{{ $reportData['evaluation_stats']['avg_initiative'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <canvas id="performanceScoresChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Résumé des congés</h3>
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Statistiques des congés</h5>
                    <ul>
                        <li><strong>Demandes totales:</strong> {{ $reportData['leave_stats']['total'] }}</li>
                        <li><strong>Demandes approuvées:</strong> {{ $reportData['leave_stats']['approved'] }}</li>
                        <li><strong>Demandes rejetées:</strong> {{ $reportData['leave_stats']['rejected'] }}</li>
                        <li><strong>Demandes en attente:</strong> {{ $reportData['leave_stats']['pending'] }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Taux d'approbation</h5>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $reportData['leave_stats']['total'] ? ($reportData['leave_stats']['approved'] / $reportData['leave_stats']['total'] * 100) : 0 }}%;" aria-valuenow="{{ $reportData['leave_stats']['total'] ? ($reportData['leave_stats']['approved'] / $reportData['leave_stats']['total'] * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100">{{ $reportData['leave_stats']['total'] ? round($reportData['leave_stats']['approved'] / $reportData['leave_stats']['total'] * 100) : 0 }}%</div>
                    </div>
                    <p>Le taux d'approbation des demandes de congé est de {{ $reportData['leave_stats']['total'] ? round($reportData['leave_stats']['approved'] / $reportData['leave_stats']['total'] * 100) : 0 }}% sur la période.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Analyse et recommandations</h3>
        <div class="summary-box">
            <h5>Points forts</h5>
            <ul>
                <li>Le travail d'équipe est un point fort du département avec un score moyen de {{ $reportData['evaluation_stats']['avg_teamwork'] }}/5.</li>
                <li>Le taux de présence est satisfaisant à {{ $reportData['attendance_stats']['total'] ? round(($reportData['attendance_stats']['present'] + $reportData['attendance_stats']['late']) / $reportData['attendance_stats']['total'] * 100) : 0 }}%.</li>
                <li>Les tâches de haute priorité sont généralement bien gérées.</li>
            </ul>
            
            <h5 class="mt-4">Points d'amélioration</h5>
            <ul>
                <li>Le taux d'achèvement des tâches ({{ $reportData['task_stats']['total'] ? round($reportData['task_stats']['completed'] / $reportData['task_stats']['total'] * 100) : 0 }}%) est légèrement en dessous de l'objectif de 75%.</li>
                <li>Le score d'initiative ({{ $reportData['evaluation_stats']['avg_initiative'] }}/5) peut être amélioré.</li>
                <li>{{ $reportData['task_stats']['delayed'] }} tâches sont actuellement en retard.</li>
            </ul>
            
            <h5 class="mt-4">Recommandations</h5>
            <ol>
                <li>Organiser des sessions de formation sur la prise d'initiative pour encourager l'autonomie des employés.</li>
                <li>Mettre en place un suivi plus régulier des tâches pour identifier les blocages plus rapidement.</li>
                <li>Maintenir la bonne dynamique d'équipe qui est un point fort du département.</li>
                <li>Revoir la répartition des tâches entre les membres de l'équipe pour équilibrer la charge de travail.</li>
            </ol>
        </div>
    </div>
</div>