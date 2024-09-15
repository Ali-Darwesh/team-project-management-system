<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];






    //==================================
    //==================================
    //=========MAKE TASK CRUD===========
    //==================================
    //==================================








    protected $guarded = [
        'start_time',
        'end_time'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
