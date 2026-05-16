@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-tasks text-primary"></i>
            {{ $task->title }}
        </h2>
        <div>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('projects.show', $task->project_id) }}" class="btn btn-outline-primary">
                <i class="fas fa-folder"></i> Voir projet
            </a>
            @if(auth()->user()->isAdmin() || auth()->id() == $task->assigned_to || auth()->id() == $task->project->created_by)
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Détails de la tâche</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Projet:</div>
                        <div class="col-md-9">
                            <a href="{{ route('projects.show', $task->project_id) }}" class="text-decoration-none">
                                {{ $task->project->title ?? 'N/A' }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Assignée à:</div>
                        <div class="col-md-9">
                            <i class="fas fa-user-circle"></i> {{ $task->assignedUser->name ?? 'Non assigné' }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Priorité:</div>
                        <div class="col-md-9">
                            @php
                                $priorityColors = ['low' => 'success', 'medium' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
                            @endphp
                            <span class="badge bg-{{ $priorityColors[$task->priority] ?? 'secondary' }} fs-6">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Statut:</div>
                        <div class="col-md-9">
                            <select class="form-select form-select-sm status-update w-auto" data-task-id="{{ $task->id }}">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>⚙️ En cours</option>
                                <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>🔍 En révision</option>
                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>✅ Terminée</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Deadline:</div>
                        <div class="col-md-9 {{ $task->is_overdue ? 'text-danger fw-bold' : '' }}">
                            <i class="fas fa-calendar-alt"></i> {{ $task->deadline->format('d/m/Y') }}
                            @if($task->is_overdue)
                                <span class="badge bg-danger ms-2">En retard!</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Créée le:</div>
                        <div class="col-md-9">
                            {{ $task->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    
                    @if($task->completed_at)
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Terminée le:</div>
                        <div class="col-md-9">
                            {{ $task->completed_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    @endif
                    
                    @if($task->description)
                    <div class="row">
                        <div class="col-md-3 fw-bold">Description:</div>
                        <div class="col-md-9">
                            <div class="p-3 bg-light rounded">
                                {{ $task->description }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i> Commentaires
                        <span class="badge bg-secondary ms-2">{{ $task->comments->count() }}</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @forelse($task->comments as $comment)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <i class="fas fa-user-circle text-primary"></i>
                                <strong>{{ $comment->user->name }}</strong>
                            </div>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 ps-2">{{ $comment->content }}</p>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comment-slash fa-2x mb-2 d-block"></i>
                        <p>Aucun commentaire pour le moment</p>
                    </div>
                    @endforelse
                    
                    <form action="{{ route('comments.store', $task) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" class="form-control" placeholder="Ajouter un commentaire..." required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.status-update').forEach(select => {
        select.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const status = this.value;
            
            this.disabled = true;
            
            axios.patch(`/tasks/${taskId}/status`, { status })
                .then(response => {
                    if (response.data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la mise à jour du statut');
                    this.disabled = false;
                });
        });
    });
</script>
@endpush