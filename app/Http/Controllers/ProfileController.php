<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bio' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            
            $avatar = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            
            // Créer le dossier s'il n'existe pas
            if (!file_exists(public_path('images/avatars'))) {
                mkdir(public_path('images/avatars'), 0777, true);
            }
            
            $avatar->move(public_path('images/avatars'), $filename);
            $data['avatar'] = 'images/avatars/' . $filename;
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Profil mis à jour avec succès!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Mot de passe modifié avec succès!');
    }

    public function statistics()
    {
        $user = auth()->user();
        
        $statistics = [
            'total_projects' => $user->projects()->count(),
            'total_tasks' => $user->assignedTasks()->count(),
            'completed_tasks' => $user->assignedTasks()->where('status', 'completed')->count(),
            'in_progress_tasks' => $user->assignedTasks()->where('status', 'in_progress')->count(),
            'pending_tasks' => $user->assignedTasks()->where('status', 'pending')->count(),
            'overdue_tasks' => $user->assignedTasks()
                ->where('deadline', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
            'completion_rate' => $this->getCompletionRate($user),
            'tasks_by_priority' => $user->assignedTasks()
                ->selectRaw('priority, count(*) as total')
                ->groupBy('priority')
                ->get(),
            'tasks_by_month' => $user->assignedTasks()
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as total')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get(),
        ];

        return view('profile.statistics', compact('statistics', 'user'));
    }

    private function getCompletionRate($user)
    {
        $total = $user->assignedTasks()->count();
        if ($total == 0) return 0;
        $completed = $user->assignedTasks()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }
}