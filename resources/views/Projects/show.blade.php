@extends('layouts.app')

@section('title', $project->title)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $project->title }}</h2>
        <div>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            @if(auth()->user()->isAdmin() || auth()->id() == $project->created_by)
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endif
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                @if($project->image && Storage::disk('public')->exists($project->image))
    <img src="{{ Storage::url($project->image) }}" class="card-img-top" alt="{{ $project->title }}" style="max-height: 300px; object-fit: cover;">
@else
    <div class="bg-secondary text-white text-center py-5" style="height: 200px;">
        <i class="fas fa-project-diagram fa-4x"></i>
        <p class="mt-2">Aucune image</p>
    </div>
@endif
                
                <div class="card-body">
                    <h5>Informations</h5>
                    <hr>
                    <p><i class="fas fa-user"></i> Créé par: {{ $project->creator->name }}</p>
                    <p><i class="fas fa-calendar"></i> Dates: {{ $project->start_date->format('d/m/Y') }} - {{ $project->end_date->format('d/m/Y') }}</p>
                    <p><i class="fas fa-chart-line"></i> Progression: {{ $project->progress }}%</p>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <p><i class="fas fa-tasks"></i> Tâches: {{ $stats['total'] ?? $project->tasks->count() }} totales</p>
                    <p><i class="fas fa-check-circle text-success"></i> Terminées: {{ $stats['completed'] ?? 0 }}</p>
                    <p><i class="fas fa-clock text-warning"></i> En cours: {{ $stats['in_progress'] ?? 0 }}</p>
                    @if(($stats['overdue'] ?? 0) > 0)
                    <p><i class="fas fa-exclamation-triangle text-danger"></i> En retard: {{ $stats['overdue'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Description</h5>
                </div>
                <div class="card-body">
                    <p>{{ $project->description }}</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Tâches</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="fas fa-plus"></i> Ajouter Tâche
                    </button>
                </div>
                <div class="card-body">
                    @forelse($tasks as $task)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $task->title }}</h6>
                                    <p class="text-muted small mb-2">{{ Str::limit($task->description, 100) }}</p>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <span class="badge bg-{{ $task->priority_color }}">{{ ucfirst($task->priority) }}</span>
                                        <span class="badge bg-{{ $task->status_color }}">{{ $task->status }}</span>
                                        <span class="badge bg-info">Assignée à: {{ $task->assignedUser->name ?? 'N/A' }}</span>
                                        @if($task->is_overdue)
                                        <span class="badge bg-danger">En retard!</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-calendar-alt"></i> Deadline: {{ $task->deadline->format('d/m/Y') }}
                                    </small>
                                </div>
                                
                                <div class="text-end">
                                    <select class="form-select form-select-sm status-update mb-2" data-task-id="{{ $task->id }}" style="width: 130px;">
                                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>⚙️ En cours</option>
                                        <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>🔍 En révision</option>
                                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>✅ Terminée</option>
                                    </select>
                                    
                                    @if(auth()->user()->isAdmin() || auth()->id() == $project->created_by)
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette tâche?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Commentaires -->
                            <div class="mt-3">
                                <button class="btn btn-sm btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#comments{{ $task->id }}">
                                    <i class="fas fa-comments"></i> Commentaires ({{ $task->comments->count() }})
                                </button>
                                
                                <div class="collapse mt-2" id="comments{{ $task->id }}">
                                    @foreach($task->comments as $comment)
                                    <div class="border-start border-primary ps-3 mb-2">
                                        <strong>{{ $comment->user->name }}</strong>
                                        <small class="text-muted">- {{ $comment->created_at->diffForHumans() }}</small>
                                        <p class="mb-0">{{ $comment->content }}</p>
                                    </div>
                                    @endforeach
                                    
                                    <form action="{{ route('comments.store', $task) }}" method="POST" class="mt-2">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="content" class="form-control form-control-sm" placeholder="Ajouter un commentaire..." required>
                                            <button type="submit" class="btn btn-sm btn-primary">Envoyer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucune tâche trouvée. Ajoutez votre première tâche!
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Tâche -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tasks.store', $project) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Assigner à</label>
                        <select name="assigned_to" class="form-control" required>
                            <option value="">Sélectionner un utilisateur</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priorité</label>
                            <select name="priority" class="form-control">
                                <option value="low">Basse</option>
                                <option value="medium">Moyenne</option>
                                <option value="high">Haute</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update task status via AJAX
    document.querySelectorAll('.status-update').forEach(select => {
        select.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const status = this.value;
            const originalText = this.options[this.selectedIndex].text;
            
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