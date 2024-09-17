<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'project_id',
        'status',
        'priority',
        'priority_rank',
        'notes',
        'due_date',
    ];

    protected static function booted()
    {
        static::saving(function ($task) {
            $task->priority_rank = match ($task->priority) {
                'high' => 3,
                'medium' => 2,
                'low' => 1,
                default => 0, // Default rank if priority is undefined
            };
        });
    }


    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function scopePriority($query, $priority)
    {
        if (!empty($priority['priority'])) {
            $query->whereRelation('project', 'priority', $priority['priority']);
        }
        return $query;
    }
    public function scopeStatus($query, $status)
    {

        if (!empty($status['status'])) {
            $query->whereRelation('project', 'status', $status['status']);
        }
        return $query;
    }
}
