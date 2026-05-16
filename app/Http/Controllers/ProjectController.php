<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $projects = Project::with(['creator', 'tasks'])->latest()->paginate(10);
        } else {
            $projects = Project::where('created_by', auth()->id())
                ->with(['creator', 'tasks'])
                ->latest()
                ->paginate(10);
        }
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }
public function store(ProjectRequest $request)
{
    try {
        DB::beginTransaction();
        
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        // Gestion de l'image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('projects', $filename, 'public');
            $data['image'] = $path;
        }

        $project = Project::create($data);
        
        DB::commit();
        
        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
    }
}

    public function show(Project $project)
    {
        // Vérifier l'autorisation
        if (!auth()->user()->isAdmin() && $project->created_by != auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à ce projet.');
        }
        
        // Charger les tâches avec leurs relations (sans 'tags')
        $tasks = $project->tasks()->with(['assignedUser', 'comments.user'])->get();
        $users = User::where('role', 'user')->get();
        
        // Statistiques des tâches
        $stats = [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'overdue' => $tasks->filter(function($task) {
                return $task->is_overdue;
            })->count(),
        ];
        
        return view('projects.show', compact('project', 'tasks', 'users', 'stats'));
    }

public function edit(Project $project)
{
    if (!auth()->user()->isAdmin() && $project->created_by != auth()->id()) {
        abort(403, 'Vous n\'avez pas accès à ce projet.');
    }
    return view('projects.edit', compact('project'));
}

   public function update(ProjectRequest $request, Project $project)
{
    if (!auth()->user()->isAdmin() && $project->created_by != auth()->id()) {
        abort(403, 'Vous n\'avez pas accès à ce projet.');
    }

    try {
        DB::beginTransaction();
        
        $data = $request->validated();
        
        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($project->image && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }
            
            // Enregistrer la nouvelle image
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('projects', $filename, 'public');
            $data['image'] = $path;
        }

        $project->update($data);
        
        DB::commit();
        
        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet modifié avec succès!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Erreur lors de la modification: ' . $e->getMessage());
    }
}

    public function destroy(Project $project)
    {
        if (!auth()->user()->isAdmin() && $project->created_by != auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à ce projet.');
        }

        try {
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            
            $project->delete();
            
            return redirect()->route('projects.index')
                ->with('success', 'Projet supprimé avec succès!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression');
        }
    }
    
    private function authorizeProject(Project $project)
    {
        if (!auth()->user()->isAdmin() && $project->created_by != auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à ce projet.');
        }
    }
}