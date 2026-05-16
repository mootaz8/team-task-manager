<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string|min:2'
        ]);

        $task->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        return redirect()->route('projects.show', $task->project)
            ->with('success', 'Commentaire ajouté!');
    }
}