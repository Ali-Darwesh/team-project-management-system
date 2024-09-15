<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'contribution_hours', 'last_activity')
            ->withTimestamps();
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
