<!-- resources/views/department_head/report_templates/attendance.blade.php -->
<div class="report-content">
    <div class="report-section">
        <h3 class="report-section-title">Résumé des présences</h3>
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-success">85%</div>
                    <div class="metric-label">Taux de présence</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-warning">8%</div>
                    <div class="metric-label">Taux de retard</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-danger">5%</div>
                    <div class="metric-label">Taux d'absence</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="metric-box">
                    <div class="metric-value text-info">2%</div>
                    <div class="metric-label">Taux de congés</div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <canvas id="attendancePresentChart"></canvas>
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
        <h3 class="report-section-title">Détail par employé</h3>
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Jours travaillés</th>
                        <th>Jours d'absence</th>
                        <th>Jours de retard</th>
                        <th>Congés</th>
                        <th>Heures totales</th>
                        <th>Heures suppl.</th>
                        <th>Taux de présence</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['data'] as $employeeData)
                    <tr>
                        <td>{{ $employeeData['employee']->name }}</td>
                        <td class="text-center">{{ $employeeData['present_days'] }}</td>
                        <td class="text-center">{{ $employeeData['absent_days'] }}</td>
                        <td class="text-center">{{ $employeeData['late_days'] }}</td>
                        <td class="text-center">{{ $employeeData['leave_days'] }}</td>
                        <td class="text-center">{{ $employeeData['total_hours'] }} h</td>
                        <td class="text-center">{{ $employeeData['overtime_hours'] }} h</td>
                        <td class="text-center">
                            @php
                                $totalDays = $employeeData['present_days'] + $employeeData['absent_days'] + $employeeData['late_days'] + $employeeData['leave_days'];
                                $presenceRate = $totalDays > 0 ? round(($employeeData['present_days'] + $employeeData['late_days']) / $totalDays * 100) : 0;
                                
                                if ($presenceRate >= 90) {
                                    $rateClass = 'text-success';
                                } elseif ($presenceRate >= 75) {
                                    $rateClass = 'text-info';
                                } elseif ($presenceRate >= 60) {
                                    $rateClass = 'text-warning';
                                } else {
                                    $rateClass = 'text-danger';
                                }
                            @endphp
                            <span class="{{ $rateClass }} fw-bold">{{ $presenceRate }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Heures supplémentaires</h3>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Résumé des heures supplémentaires</h5>
                    <p><strong>Total des heures supplémentaires:</strong> 45 heures</p>
                    <p><strong>Moyenne par employé:</strong> 2.5 heures</p>
                    <p><strong>Employé avec le plus d'heures:</strong> Ahmed Benali (8 heures)</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="summary-box">
                    <h5>Répartition par semaine</h5>
                    <ul>
                        <li><strong>Semaine 1:</strong> 12 heures</li>
                        <li><strong>Semaine 2:</strong> 15 heures</li>
                        <li><strong>Semaine 3:</strong> 10 heures</li>
                        <li><strong>Semaine 4:</strong> 8 heures</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h3 class="report-section-title">Observations et recommandations</h3>
        <div class="summary-box">
            <h5>Observations</h5>
            <p>Le taux de présence global du département est de 85%, ce qui est légèrement inférieur à l'objectif de 90%. On note une amélioration par rapport au mois précédent (82%).</p>
            <p>Les retards sont principalement concentrés en début de semaine, avec un pic le lundi. Les absences sont réparties de manière assez uniforme sur la période.</p>
            
            <h5 class="mt-4">Recommandations</h5>
            <ol>
                <li>Organiser un point d'équipe pour discuter des raisons des retards en début de semaine.</li>
                <li>Mettre en place un système de reconnaissance pour les employés ayant un taux de présence de 100%.</li>
                <li>Revoir la planification des congés pour assurer une meilleure répartition.</li>
            </ol>
        </div>
    </div>
</div>

