<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'assigned_to', 'title', 'description',
        'priority', 'status', 'deadline', 'completed_at'
    ];

    protected $casts = [
        'deadline' => 'date',
        'completed_at' => 'datetime',
    ];

    protected $appends = ['priority_color', 'status_color', 'is_overdue'];

    // ============ RELATIONS ============
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

 
    // ============ ACCESSORS ============
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'secondary',
            'in_progress' => 'primary',
            'review' => 'warning',
            'completed' => 'success',
            default => 'secondary'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline < now() && $this->status !== 'completed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'review' => 'En révision',
            'completed' => 'Terminée',
            default => $this->status
        };
    }

    // ============ SCOPES ============
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('deadline', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeAssignedToUser(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    // ============ METHODS ============
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function canBeEditedBy(User $user): bool
    {
        return $user->isAdmin() || 
               $this->assigned_to == $user->id || 
               $this->project->created_by == $user->id;
    }
}