@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2">
            <i class="fas fa-chart-line text-primary me-2"></i>
            Tableau de bord
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshStats">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportReport">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row g-4 mb-4">
        @if(auth()->user()->isAdmin())
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm card-hover animate__animated animate__fadeInUp">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold mb-2">Total Projets</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_projects'] ?? 0) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i class="fas fa-project-diagram fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" style="width: {{ ($stats['total_projects'] ?? 0) > 0 ? 100 : 0 }}%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-chart-line me-1"></i>
                            Taux d'activité général
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm card-hover animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold mb-2">Tâches complétées</h6>
                            <h2 class="mb-0 text-success">{{ number_format($stats['completed_tasks'] ?? 0) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['completion_rate'] ?? 0 }}%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-percent me-1"></i>
                            Taux de complétion: {{ $stats['completion_rate'] ?? 0 }}%
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm card-hover animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold mb-2">Tâches en retard</h6>
                            <h2 class="mb-0 text-danger">{{ number_format($stats['delayed_tasks'] ?? 0) }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded p-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-danger" style="width: {{ ($stats['delayed_tasks'] ?? 0) > 0 ? min(100, ($stats['delayed_tasks'] / $stats['total_tasks'] * 100)) : 0 }}%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-clock me-1"></i>
                            Nécessite une attention immédiate
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm card-hover animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold mb-2">Score productivité</h6>
                            <h2 class="mb-0 text-info">{{ $stats['productivity_score'] ?? 0 }}/100</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded p-3">
                            <i class="fas fa-rocket fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-info" style="width: {{ $stats['productivity_score'] ?? 0 }}%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-chart-simple me-1"></i>
                            Performance globale
                        </small>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Version utilisateur -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-gradient-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Mes Projets</h6>
                            <h2 class="mb-0">{{ $stats['my_projects'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-project-diagram fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-gradient-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Mes Tâches</h6>
                            <h2 class="mb-0">{{ $stats['my_tasks'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-tasks fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-gradient-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Tâches terminées</h6>
                            <h2 class="mb-0">{{ $stats['completed_tasks'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-gradient-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Tâches en retard</h6>
                            <h2 class="mb-0">{{ $stats['delayed_tasks'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Répartition des tâches</h5>
                </div>
                <div class="card-body">
                    <canvas id="tasksStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Activité mensuelle</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyActivityChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Priorités</h5>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tâches récentes -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Tâches récentes</h5>
            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-link">Voir toutes <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Tâche</th>
                            <th>Projet</th>
                            <th>Assigné à</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Deadline</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $task)
                        <tr class="align-middle">
                            <td>
                                <strong>{{ Str::limit($task->title, 40) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $task->project->title ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <i class="fas fa-user-circle fa-lg"></i>
                                    </div>
                                    <span>{{ $task->assignedUser->name ?? 'Non assigné' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->priority_color }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->status_color }}">
                                    {{ $task->status_label }}
                                </span>
                            </td>
                            <td class="{{ $task->is_overdue ? 'text-danger fw-bold' : '' }}">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ $task->deadline->format('d/m/Y') }}
                                @if($task->is_overdue)
                                    <i class="fas fa-exclamation-circle ms-1" title="En retard"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Aucune tâche trouvée
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
}
.card-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique statut des tâches
    const tasksStatusCtx = document.getElementById('tasksStatusChart')?.getContext('2d');
    if (tasksStatusCtx) {
        new Chart(tasksStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'En cours', 'En révision', 'Terminées'],
                datasets: [{
                    data: {!! json_encode(array_values($charts['tasks_by_status'] ?? ['pending' => 0, 'in_progress' => 0, 'review' => 0, 'completed' => 0])) !!},
                    backgroundColor: ['#6c757d', '#0d6efd', '#ffc107', '#198754'],
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Graphique activité mensuelle
    const monthlyCtx = document.getElementById('monthlyActivityChart')?.getContext('2d');
    if (monthlyCtx && {!! json_encode($charts['monthly_tasks'] ?? []) !!}) {
        const monthlyData = {!! json_encode($charts['monthly_tasks'] ?? []) !!};
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [{
                    label: 'Tâches créées',
                    data: monthlyData.map(d => d.count),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Tâches terminées',
                    data: monthlyData.map(d => d.completed),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Graphique priorités
    const priorityCtx = document.getElementById('priorityChart')?.getContext('2d');
    if (priorityCtx) {
        const priorityData = {!! json_encode($charts['tasks_by_priority'] ?? ['low' => 0, 'medium' => 0, 'high' => 0, 'urgent' => 0]) !!};
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: ['Basse', 'Moyenne', 'Haute', 'Urgente'],
                datasets: [{
                    label: 'Nombre de tâches',
                    data: [
                        priorityData.low || 0,
                        priorityData.medium || 0,
                        priorityData.high || 0,
                        priorityData.urgent || 0
                    ],
                    backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, grid: { display: false } } }
            }
        });
    }

    // Rafraîchissement
    document.getElementById('refreshStats')?.addEventListener('click', function() {
        location.reload();
    });

    // Export report
    document.getElementById('exportReport')?.addEventListener('click', function() {
        window.print();
    });
});
</script>
@endpush