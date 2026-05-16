<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'start_date', 'end_date', 
        'status', 'created_by', 'image'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['progress', 'is_overdue'];

    // ============ RELATIONS ============
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


    // ============ ACCESSORS ============
    public function getProgressAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total == 0) return 0;
        $completed = $this->tasks()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date < now() && $this->status !== 'completed';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'planning' => 'secondary',
            'active' => 'success',
            'completed' => 'info',
            'on_hold' => 'warning',
            default => 'secondary',
        };
    }
    // Ajoutez cet accesseur
public function getImageUrlAttribute()
{
    if ($this->image) {
        return Storage::url($this->image);
    }
    return null;
}

    // ============ SCOPES ============
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopePlanning(Builder $query): Builder
    {
        return $query->where('status', 'planning');
    }

    public function scopeCreatedByUser(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) return $query;
        
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    // ============ METHODS ============
    public function isOwner(User $user): bool
    {
        return $this->created_by == $user->id;
    }

    public function canBeDeleted(): bool
    {
        return $this->tasks()->where('status', '!=', 'completed')->count() === 0;
    }

    public function getRemainingDays(): int
    {
        if ($this->end_date < now()) return 0;
        return now()->diffInDays($this->end_date);
    }
}