<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Liste des tâches
     */
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $tasks = Task::with(['project', 'assignedUser'])->latest()->paginate(15);
        } else {
            $tasks = Task::where('assigned_to', auth()->id())
                ->with(['project', 'assignedUser'])
                ->latest()
                ->paginate(15);
        }
        
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $projects = Project::where('created_by', auth()->id())->get();
        $users = User::where('role', 'user')->get();
        
        return view('tasks.create', compact('projects', 'users'));
    }

    /**
     * Enregistrer une tâche (création indépendante)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'deadline' => 'required|date',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'status' => 'pending',
            'deadline' => $request->deadline,
        ]);

        $user = User::find($request->assigned_to);
        $user->notify(new TaskAssigned($task));

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tâche créée avec succès!');
    }

    /**
     * Enregistrer une tâche depuis un projet (formulaire dans show.blade.php)
     */
    public function storeFromProject(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'deadline' => 'required|date',
        ]);

        $task = $project->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'status' => 'pending',
            'deadline' => $request->deadline,
        ]);

        $user = User::find($request->assigned_to);
        $user->notify(new TaskAssigned($task));

        return redirect()->route('projects.show', $project)
            ->with('success', 'Tâche ajoutée avec succès!');
    }

    /**
     * Afficher une tâche
     */
    public function show(Task $task)
    {
        if (!auth()->user()->isAdmin() && $task->assigned_to != auth()->id() && $task->project->created_by != auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à cette tâche.');
        }
        
        $task->load(['project', 'assignedUser', 'comments.user']);
        $users = User::where('role', 'user')->get();
        
        return view('tasks.show', compact('task', 'users'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Task $task)
    {
        if (!auth()->user()->isAdmin() && $task->assigned_to != auth()->id() && $task->project->created_by != auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à cette tâche.');
        }
        
        $projects = Project::all();
        $users = User::where('role', 'user')->get();
        
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Mettre à jour une tâche
     */
    public function update(Request $request, Task $task)
    {
        if (!auth()->user()->isAdmin() && $task->assigned_to != auth()->id() && $task->project->created_by != auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à cette tâche.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,review,completed',
            'deadline' => 'required|date',
        ]);

        $task->update($request->all());

        if ($request->status == 'completed') {
            $task->update(['completed_at' => now()]);
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tâche mise à jour avec succès!');
    }

    /**
     * Mettre à jour le statut via AJAX
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,review,completed'
        ]);

        $task->update(['status' => $request->status]);

        if ($request->status == 'completed') {
            $task->update(['completed_at' => now()]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'status' => $task->status]);
        }

        return redirect()->back()->with('success', 'Statut mis à jour!');
    }

    /**
     * Supprimer une tâche
     */
    public function destroy(Task $task)
    {
        $project = $task->project;
        
        if (!auth()->user()->isAdmin() && $task->assigned_to != auth()->id() && $task->project->created_by != auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à cette tâche.');
        }
        
        $task->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Tâche supprimée avec succès!');
    }
}