<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index()
    {
        $user = auth()->user();

        // Statistiques dashboard
        $stats = $this->statisticsService->getDashboardStats($user);

        // Données graphiques
        $charts = [
            'tasks_by_status' => $this->getTasksByStatus(),
            'tasks_by_priority' => $this->getTasksByPriority(),
            'projects_by_status' => $this->getProjectsByStatus(),
            'monthly_tasks' => $this->getMonthlyTasksStats(),
        ];

        // Tâches récentes
        if ($user->isAdmin()) {
            $recentTasks = Task::with(['project', 'assignedUser'])
                ->latest()
                ->take(10)
                ->get();
        } else {
            $recentTasks = Task::where('assigned_to', $user->id)
                ->with(['project', 'assignedUser'])
                ->latest()
                ->take(10)
                ->get();
        }

        return view('dashboard', compact(
            'stats',
            'charts',
            'recentTasks'
        ));
    }

    private function getTasksByStatus()
    {
        return Task::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    private function getTasksByPriority()
    {
        return Task::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();
    }

    private function getProjectsByStatus()
    {
        return Project::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    private function getMonthlyTasksStats()
    {
        $months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $months->push([
                'month' => $date->format('M Y'),

                'count' => Task::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),

                'completed' => Task::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'completed')
                    ->count(),
            ]);
        }

        return $months;
    }
}