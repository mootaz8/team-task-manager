<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'bio', 'last_seen_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    // ============ RELATIONS ============
    public function projects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // ============ ACCESSORS ============
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar 
            ? asset('storage/avatars/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

    // ============ METHODS ============
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }

    public function updateLastSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    public function getStatistics(): array
    {
        $tasks = $this->assignedTasks();
        
        return [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => (clone $tasks)->where('status', 'completed')->count(),
            'in_progress_tasks' => (clone $tasks)->where('status', 'in_progress')->count(),
            'completion_rate' => $this->calculateCompletionRate(),
        ];
    }

    private function calculateCompletionRate(): float
    {
        $total = $this->assignedTasks()->count();
        if ($total == 0) return 0;
        $completed = $this->assignedTasks()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }
}