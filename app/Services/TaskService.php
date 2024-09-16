<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Exception;

use function PHPUnit\Framework\isNull;

class TaskService
{
    /**
     * service to create task.
     * @param array $data
     * @return array 

     */
    public function createTask(array $data)
    {
        try {
            $task = Task::create($data);
            return ['message' => 'task created successfully', 'task' => $task, 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'create task failed', 'error' => $e, 'status' => 404];
        }
    }
    /**
     * service to update task data in storage.
     * @param Task $task
     * @param array $data
     * @return array 

     */
    public function updateTask(Task $task, array $data)
    {
        try {
            $task = Task::findOrFail($task->id);
            $task->update($data);
            return ['message' => 'task updated successfully', 'task' => $task, 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'Update task failed', 'error' => $e, 'status' => 404];
        }
    }
    /**
     * service method used to delete task from storage.
     * @param Task $task
     *@return array

     */
    public function deleteTask(Task $task)
    {
        try {
            $task = Task::findOrFail($task->id);
            $task->delete();
            return ['message' => 'Task deleted successfully', 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'Delete task failed', 'error' => $e, 'status' => 404];
        }
    }
    public function startTime($taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
            if ($task->start_time === null) {
                // Set the start time for the task
                $task->start_time = Carbon::now();
                $task->end_time = null;
                $task->save();
                return ['status' => 200, 'message' => 'Task started successfully', 'start_time' => $task->start_time];
            } else {
                return ['status' => 404, 'error' => 'error', 'message' => 'the task session is started already '];
            }
        } catch (Exception $e) {
            return ['message' => 'Set task start time  failed', 'error' => $e, 'status' => 404];
        }
    }
    public function endTime($taskId, $userId)
    {
        try {
            $task = Task::findOrFail($taskId);
            if (!is_null($task->start_time)) {
                echo $task->start_time;

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
                        'last_activity' => Carbon::now(), // Set last_active to the current timestamp
                    ]
                ]);
                $task->start_time = null;
                $task->save();
                $userTaskInfo  = $user->projects()->where('project_id', $projectId)
                    ->withPivot('role', 'contribution_hours', 'last_activity')
                    ->first();
                return [
                    'status' => 200,
                    'message' => 'Task ended successfully',
                    'session_hours' => round($sessionHours, 2),
                    'userTaskInfo' => $userTaskInfo,
                    'end_time' => $task->end_time
                ];
            } else {
                return ['status' => 404, 'error' => 'error', 'message' => 'the task session was not started '];
            }
        } catch (Exception $e) {
            return ['message' => 'Set end task end time  failed', 'error' => $e, 'status' => 404];
        }
    }
}
