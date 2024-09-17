<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'contribution_hours', 'start_time', 'last_activity')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function latestTask()
    {
        return $this->hasOne(Task::class)->latestOfMany();
    }
    public function oldestTask()
    {
        return $this->hasOne(Task::class)->oldestOfMany();
    }
    //=======

    public function highestPriorityTaskWithConditions($titleCondition = null, $statusCondition = null)
    {
        return $this->hasOne(Task::class)->ofMany([
            'priority_rank' => 'max', // استخدم 'min' للتأكد من جلب الأولوية العالية
        ], function ($query) use ($titleCondition, $statusCondition) {
            if ($titleCondition) {
                $query->where('title', $titleCondition);
            }
            if ($statusCondition) {
                $query->where('status', $statusCondition);
            }
        });
    }
}
