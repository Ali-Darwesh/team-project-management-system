<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }

    //=================
    public function startTask($taskId)
    {
        $task = Task::findOrFail($taskId);

        // Set the start time for the task
        $task->start_time = Carbon::now();
        $task->end_time = null;
        $task->save();

        return response()->json(['message' => 'Task started successfully', 'start_time' => $task->start_time]);
    }
    public function endTask($taskId, $userId)
    {
        $task = Task::findOrFail($taskId);

        // Log the end time for the task
        $task->end_time = Carbon::now();
        $task->save();

        // Calculate session time in hours
        $sessionHours = $task->start_time ? $task->start_time->diffInMinutes($task->end_time) / 60 : 0;

        // Find the user and project relationship
        $user = User::findOrFail($userId);
        $projectId = $task->project_id;  // Assuming `project_id` exists in the `tasks` table

        // Get the current contribution_hours from the pivot table
        $currentPivotData = $user->projects()->where('project_id', $projectId)->first()->pivot;

        // Update the contribution_hours and last_active without detaching existing pivot data
        $user->projects()->syncWithoutDetaching([
            $projectId => [
                'contribution_hours' => $currentPivotData->contribution_hours + round($sessionHours, 2),
                'last_active' => Carbon::now(), // Set last_active to the current timestamp
            ]
        ]);
        $task->start_time = null;
        $task->save();

        return response()->json([
            'message' => 'Task ended successfully',
            'session_hours' => round($sessionHours, 2),
            'total_active_hours' => $currentPivotData->active_hours + round($sessionHours, 2),
            'last_active' => Carbon::now(),
        ]);
    }
}
