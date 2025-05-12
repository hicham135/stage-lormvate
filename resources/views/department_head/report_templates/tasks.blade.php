<!-- resources/views/department_head/report_templates/tasks.blade.php -->
<div class="report-content">
    <div class="report-section">
        <h3 class="report-section-title">Résumé des tâches</h3>
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-primary">61</div>
                    <div class="metric-label">Tâches totales</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-success">42</div>
                    <div class="metric-label">Tâches terminées</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-info">14</div>
                    <div class="metric-label">Tâches en cours</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-danger">5</div>
                    <div class="metric-label">Tâches retardées</div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <canvas id="tasksStatusChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <canvas id="tasksPriorityChart"></canvas>
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
                        <th>Total tâches</th>
                        <th>Terminées</th>
                        <th>En cours</th>
                        <th>En attente</th>
                        <th>Retardées</th>
                        <th>Taux d'achèvement</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['data'] as $taskData)
                    <tr>
                        <td>{{ $taskData['employee']->name }}</td>
                        <td class="text-center">{{ $taskData['total_tasks'] }}</td>
                        <td class="text-center">{{ $taskData['completed_tasks'] }}</td>
                        <td class="text-center">{{ $taskData['in_progress_tasks'] }}</td>
                        <td class="text-center">{{ $taskData['pending_tasks'] }}</td>
                        <td class="text-center">{{ $taskData['delayed_tasks'] }}</td>
                        <td class="text-center">
                            @php
                                $completionRate = $taskData['completion_rate'];
                                
                                if ($completionRate >= 90) {
                                    $rateClass = 'text-success';
                                } elseif ($completionRate >= 75) {
                                    $rateClass = 'text-info';
                                } elseif ($completionRate >= 60) {
                                    $rateClass = 'text-warning';
                                } else {
                                    $rateClass = 'text-danger';
                                }
                            @endphp
                            <span class="{{ $rateClass }} fw-bold">{{ $completionRate }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Répartition par priorité</h3>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Tâches par priorité</h5>
                    <ul>
                        <li><strong>Urgente:</strong> 8 tâches (13%)</li>
                        <li><strong>Haute:</strong> 15 tâches (25%)</li>
                        <li><strong>Moyenne:</strong> 28 tâches (46%)</li>
                        <li><strong>Basse:</strong> 10 tâches (16%)</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Taux d'achèvement par priorité</h5>
                    <ul>
                        <li><strong>Urgente:</strong> 85%</li>
                        <li><strong>Haute:</strong> 78%</li>
                        <li><strong>Moyenne:</strong> 66%</li>
                        <li><strong>Basse:</strong> 54%</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Tâches retardées</h3>
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Assignée à</th>
                        <th>Priorité</th>
                        <th>Date d'échéance</th>
                        <th>Retard</th>
                        <th>Progression</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Mise à jour du manuel d'utilisation</td>
                        <td>Ahmed Benali</td>
                        <td><span class="badge bg-warning">Haute</span></td>
                        <td>10/05/2025</td>
                        <td>2 jours</td>
                        <td>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="small">65%</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Analyse des données clients</td>
                        <td>Samira Alaoui</td>
                        <td><span class="badge bg-info">Moyenne</span></td>
                        <td>08/05/2025</td>
                        <td>4 jours</td>
                        <td>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="small">80%</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Rapport financier trimestriel</td>
                        <td>Karim Idrissi</td>
                        <td><span class="badge bg-danger">Urgente</span></td>
                        <td>05/05/2025</td>
                        <td>7 jours</td>
                        <td>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 90%;" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="small">90%</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Observations et recommandations</h3>
        <div class="summary-box">
            <h5>Observations</h5>
            <p>Le taux d'achèvement des tâches est de 69% sur la période, ce qui est légèrement en dessous de l'objectif de 75%. On observe que les tâches de priorité haute et urgente sont traitées en priorité, avec des taux d'achèvement respectifs de 85% et 78%.</p>
            <p>Cinq tâches sont actuellement en retard, dont une de priorité urgente qui est cependant presque terminée (90% de progression).</p>
            
            <h5 class="mt-4">Recommandations</h5>
            <ol>
                <li>Mettre en place un suivi plus régulier des tâches en cours pour identifier les risques de retard plus tôt.</li>
                <li>Revoir la répartition des tâches entre les membres de l'équipe pour équilibrer la charge de travail.</li>
                <li>Organiser une courte réunion quotidienne pour discuter des obstacles rencontrés et faciliter l'avancement des tâches bloquées.</li>
            </ol>
        </div>
    </div>
</div>
