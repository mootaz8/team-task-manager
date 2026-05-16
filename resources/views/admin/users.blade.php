@extends('layouts.app')

@section('title', 'Gestion Utilisateurs')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-transparent">
            <h4><i class="fas fa-users"></i> Gestion des Utilisateurs</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Projets créés</th>
                            <th>Tâches assignées</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'info' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>{{ $user->projects->count() }}</td>
                            <td>{{ $user->assignedTasks->count() }}</td>
                            <td>
                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection