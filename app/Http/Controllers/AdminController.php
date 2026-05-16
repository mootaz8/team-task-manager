<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Vérification dans le constructeur
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Accès non autorisé. Cette zone est réservée aux administrateurs.');
            }
            
            return $next($request);
        });
    }

    public function users()
    {
        $users = User::with(['projects', 'assignedTasks'])->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->route('admin.users')
            ->with('success', 'Rôle utilisateur modifié!');
    }

    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'admin_count' => User::where('role', 'admin')->count(),
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'completion_rate' => $this->getCompletionRate(),
        ];

        return view('admin.statistics', compact('stats'));
    }

    private function getCompletionRate()
    {
        $total = Task::count();
        if ($total == 0) return 0;
        $completed = Task::where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }
}