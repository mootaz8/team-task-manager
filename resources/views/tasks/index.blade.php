@extends('layouts.app')

@section('title', 'Toutes les tâches')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tasks"></i> Toutes les tâches</h2>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle tâche
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Projet</th>
                            <th>Assignée à</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Deadline</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td>
                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-bold">
                                    {{ $task->title }}
                                </a>
                            </td>
                            <td>
                                @if($task->project)
                                    <a href="{{ route('projects.show', $task->project_id) }}" class="text-decoration-none">
                                        {{ $task->project->title }}
                                    </a>
                                @else
                                    <span class="text-muted">Projet supprimé</span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-user-circle text-muted"></i>
                                {{ $task->assignedUser->name ?? 'Non assigné' }}
                            </td>
                            <td>
                                @php
                                    $priorityColors = ['low' => 'success', 'medium' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
                                @endphp
                                <span class="badge bg-{{ $priorityColors[$task->priority] ?? 'secondary' }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = ['pending' => 'secondary', 'in_progress' => 'primary', 'review' => 'warning', 'completed' => 'success'];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                    @if($task->status == 'pending') ⏳ En attente
                                    @elseif($task->status == 'in_progress') ⚙️ En cours
                                    @elseif($task->status == 'review') 🔍 En révision
                                    @elseif($task->status == 'completed') ✅ Terminée
                                    @else {{ $task->status }}
                                    @endif
                                </span>
                            </td>
                            <td class="{{ $task->is_overdue ? 'text-danger fw-bold' : '' }}">
                                <i class="fas fa-calendar-alt"></i> {{ $task->deadline->format('d/m/Y') }}
                                @if($task->is_overdue)
                                    <i class="fas fa-exclamation-circle ms-1" title="En retard"></i>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->id() == $task->assigned_to)
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    {{-- Vérification corrigée pour project --}}
                                    @if(auth()->user()->isAdmin() || ($task->project && auth()->id() == $task->project->created_by))
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette tâche ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x mb-3 d-block text-muted"></i>
                                <p class="text-muted mb-3">Aucune tâche trouvée</p>
                                <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Créer votre première tâche
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                @if ($tasks->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            @if ($tasks->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $tasks->previousPageUrl() }}">&laquo;</a></li>
                            @endif

                            @foreach ($tasks->getUrlRange(1, $tasks->lastPage()) as $page => $url)
                                @if ($page == $tasks->currentPage())
                                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            @if ($tasks->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $tasks->nextPageUrl() }}">&raquo;</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection