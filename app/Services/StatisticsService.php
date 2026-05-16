<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getDashboardStats($user)
    {
        if ($user->isAdmin()) {
            return $this->getAdminStats();
        }
        return $this->getUserStats($user);
    }

    private function getAdminStats()
    {
        return [
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'in_progress_tasks' => Task::where('status', 'in_progress')->count(),
            'delayed_tasks' => Task::where('deadline', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
            'active_users' => User::whereHas('assignedTasks')->count(),
            'completion_rate' => $this->calculateCompletionRate(),
            'avg_tasks_per_project' => $this->getAverageTasksPerProject(),
            'productivity_score' => $this->calculateProductivityScore(),
        ];
    }

    private function getUserStats($user)
    {
        $myTasks = Task::where('assigned_to', $user->id);
        
        return [
            'my_projects' => Project::where('created_by', $user->id)->count(),
            'my_tasks' => $myTasks->count(),
            'completed_tasks' => (clone $myTasks)->where('status', 'completed')->count(),
            'in_progress_tasks' => (clone $myTasks)->where('status', 'in_progress')->count(),
            'delayed_tasks' => (clone $myTasks)
                ->where('deadline', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
            'my_completion_rate' => $this->calculateUserCompletionRate($user),
            'tasks_by_priority' => $this->getUserTasksByPriority($user),
        ];
    }

    private function calculateCompletionRate()
    {
        $total = Task::count();
        if ($total == 0) return 0;
        $completed = Task::where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }

    private function calculateUserCompletionRate($user)
    {
        $total = Task::where('assigned_to', $user->id)->count();
        if ($total == 0) return 0;
        $completed = Task::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->count();
        return round(($completed / $total) * 100, 2);
    }

    private function getAverageTasksPerProject()
    {
        $totalTasks = Task::count();
        $totalProjects = Project::count();
        if ($totalProjects == 0) return 0;
        return round($totalTasks / $totalProjects, 2);
    }

    private function calculateProductivityScore()
    {
        $completedTasks = Task::where('status', 'completed')->count();
        $totalTasks = Task::count();
        if ($totalTasks == 0) return 0;
        
        $onTimeTasks = Task::where('status', 'completed')
            ->where('deadline', '>=', now())
            ->count();
        
        $completionScore = ($completedTasks / $totalTasks) * 50;
        $punctualityScore = $totalTasks > 0 ? ($onTimeTasks / $totalTasks) * 50 : 0;
        
        return round($completionScore + $punctualityScore, 2);
    }

    private function getUserTasksByPriority($user)
    {
        return Task::where('assigned_to', $user->id)
            ->select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->get()
            ->pluck('total', 'priority')
            ->toArray();
    }
}