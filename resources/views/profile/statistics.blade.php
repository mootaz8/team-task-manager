@extends('layouts.app')

@section('title', 'Mes Statistiques')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-line text-primary"></i> Mes Statistiques</h2>
        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour au profil
        </a>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6 class="text-white-50">Projets</h6>
                    <h2 class="mb-0">{{ $statistics['total_projects'] }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="text-white-50">Tâches terminées</h6>
                    <h2 class="mb-0">{{ $statistics['completed_tasks'] }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <h6 class="text-white-50">Tâches en cours</h6>
                    <h2 class="mb-0">{{ $statistics['in_progress_tasks'] }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <h6 class="text-white-50">Tâches en retard</h6>
                    <h2 class="mb-0">{{ $statistics['overdue_tasks'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Taux de complétion</h5>
                </div>
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        <canvas id="completionChart" width="200" height="200"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <h2 class="mb-0">{{ $statistics['completion_rate'] }}%</h2>
                            <small class="text-muted">Complétion</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Tâches par priorité</h5>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Évolution mensuelle</h5>
        </div>
        <div class="card-body">
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique de complétion
    const completionCtx = document.getElementById('completionChart').getContext('2d');
    new Chart(completionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Complétées', 'Restantes'],
            datasets: [{
                data: [{{ $statistics['completion_rate'] }}, {{ 100 - $statistics['completion_rate'] }}],
                backgroundColor: ['#198754', '#e9ecef'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    
    // Graphique des priorités
    const priorityData = @json($statistics['tasks_by_priority']);
    const priorityLabels = priorityData.map(p => p.priority);
    const priorityCounts = priorityData.map(p => p.total);
    
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: priorityLabels,
            datasets: [{
                label: 'Nombre de tâches',
                data: priorityCounts,
                backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    
    // Graphique mensuel
    const monthlyData = @json($statistics['tasks_by_month']);
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(m => m.month),
            datasets: [{
                label: 'Tâches créées',
                data: monthlyData.map(m => m.total),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endsection