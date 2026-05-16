@extends('layouts.app')

@section('title', 'Statistiques')

@section('content')
<div class="container">
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Utilisateurs</h6>
                    <h2>{{ $stats['total_users'] }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Administrateurs</h6>
                    <h2>{{ $stats['admin_count'] }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Total Projets</h6>
                    <h2>{{ $stats['total_projects'] }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Taux Complétion</h6>
                    <h2>{{ $stats['completion_rate'] }}%</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection