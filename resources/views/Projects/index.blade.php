@extends('layouts.app')

@section('title', 'Mes Projets')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-folder-open"></i> Mes Projets</h2>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Projet
        </a>
    </div>
    
    <div class="row">
        @forelse($projects as $project)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                @if($project->image && file_exists(public_path($project->image)))
                <img src="{{ asset($project->image) }}" class="card-img-top" alt="{{ $project->title }}" style="height: 200px; object-fit: cover;">
                @elseif($project->image && Storage::disk('public')->exists($project->image))
                <img src="{{ Storage::url($project->image) }}" class="card-img-top" alt="{{ $project->title }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="bg-secondary text-white text-center py-5" style="height: 200px;">
                    <i class="fas fa-project-diagram fa-4x"></i>
                    <p class="mt-2 small">Aucune image</p>
                </div>
                @endif
                
                <div class="card-body">
                    <h5 class="card-title">{{ $project->title }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($project->description, 100) }}</p>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Progression</small>
                            <small>{{ $project->progress }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $project->progress }}%"></div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <small><i class="fas fa-calendar-alt"></i> {{ $project->start_date->format('d/m/Y') }}</small>
                        <small><i class="fas fa-calendar-check"></i> {{ $project->end_date->format('d/m/Y') }}</small>
                    </div>
                    
                    <div>
                        @php
                            $statusColors = [
                                'planning' => 'secondary',
                                'active' => 'success',
                                'completed' => 'info',
                                'on_hold' => 'warning'
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                        @if($project->is_overdue)
                            <span class="badge bg-danger">En retard</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-footer bg-transparent">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Détails
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->id() == $project->created_by)
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce projet?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                <p>Aucun projet trouvé.</p>
                <a href="{{ route('projects.create') }}" class="btn btn-primary">Créez votre premier projet!</a>
            </div>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination corrigée -->
    <div class="d-flex justify-content-center mt-4">
        @if ($projects->hasPages())
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($projects->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $projects->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                        @if ($page == $projects->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($projects->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $projects->nextPageUrl() }}" rel="next">&raquo;</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
</div>
@endsection