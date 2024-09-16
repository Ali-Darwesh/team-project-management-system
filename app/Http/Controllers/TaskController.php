<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskDscriptionByManagerRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Constracor to inject task Service
     * @param TaskService $taskService
     */
    protected $taskService;
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
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
    public function store(StoreTaskRequest $request)
    {
        $validatedData = $request->validated();
        $task = $this->taskService->createTask($validatedData);
        if (isset($task['error'])) {
            return response(['error' => $task['error'], 'message' => $task['message']], $task['status']);
        } else {
            return response(['status' => $task['status'], 'message' => $task['message'], 'task' => $task['task']], $task['status']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update task description by manager how create it.
     * @param UpdateTaskDscriptionByManagerRequest $request
     * @param Task $task
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function updateTaskDscriptionByManager(UpdateTaskDscriptionByManagerRequest $request, Task $task)
    {
        $validatedData = $request->validated();
        $project = $this->taskService->updateTask($task, $validatedData);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message'], 'project' => $project['project']], $project['status']);
        }
    }

    /**
     * Update task status by user how do it.
     * @param UpdateTaskStatusRequest $request
     * @param Task $task
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function updateTaskStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        $validatedData = $request->validated();
        $project = $this->taskService->updateTask($task, $validatedData);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message'], 'project' => $project['project']], $project['status']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task = $this->taskService->deleteTask($task);
        if (isset($task['error'])) {
            return response(['error' => $task['error'], 'message' => $task['message']], $task['status']);
        } else {
            return response(['status' => $task['status'], 'message' => $task['message']], $task['status']);
        }
    }

    //=================
    public function startTask($taskId)
    {
        $task = $this->taskService->startTime($taskId);
        if (isset($task['error'])) {
            return response(['error' => $task['error'], 'message' => $task['message']], $task['status']);
        } else {
            return response(['status' => $task['status'], 'message' => $task['message'], 'task_start_time' => $task['start_time']], $task['status']);
        }
    }
    public function endTask($userId, $taskId)
    {
        $task = $this->taskService->endTime($taskId, $userId);
        if (isset($task['error'])) {
            return response(['error' => $task['error'], 'message' => $task['message']], $task['status']);
        } else {
            return response()->json([
                'message' => $task['message'],
                'session_hours' => $task['session_hours'],
                'userTaskInfo' => $task['userTaskInfo'],
                'end_time' => $task['end_time'],
            ], $task['status']);
        }
    }
}
