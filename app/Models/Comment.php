<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'commentable_type', 'commentable_id', 'user_id', 'content'
    ];

    protected $with = ['user'];

    // ============ RELATIONS ============
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ============ ACCESSORS ============
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y à H:i');
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    // ============ SCOPES ============
    public function scopeForTask($query, int $taskId)
    {
        return $query->where('commentable_type', Task::class)
                     ->where('commentable_id', $taskId);
    }

    public function scopeForProject($query, int $projectId)
    {
        return $query->where('commentable_type', Project::class)
                     ->where('commentable_id', $projectId);
    }
}