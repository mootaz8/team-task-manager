@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($user->avatar && file_exists(public_path($user->avatar)))
                            <img src="{{ asset($user->avatar) }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-4x"></i>
                            </div>
                        @endif
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">
                        <i class="fas fa-envelope"></i> {{ $user->email }}
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-user-tag"></i> 
                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'info' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier mon profil
                        </a>
                        <a href="{{ route('profile.statistics') }}" class="btn btn-outline-info">
                            <i class="fas fa-chart-line"></i> Mes statistiques
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nom complet:</div>
                        <div class="col-md-8">{{ $user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email:</div>
                        <div class="col-md-8">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Rôle:</div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'info' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Membre depuis:</div>
                        <div class="col-md-8">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                    @if($user->bio)
                    <div class="row">
                        <div class="col-md-4 fw-bold">Bio:</div>
                        <div class="col-md-8">{{ $user->bio }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection