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
        echo $task->id;
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
}
