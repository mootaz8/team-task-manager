<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;
use App\Policies\ProjectPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Project::class => ProjectPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        
        // Admin Gate
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
        
        // Manage projects Gate
        Gate::define('manage-projects', function ($user) {
            return $user->isAdmin();
        });
    }
}