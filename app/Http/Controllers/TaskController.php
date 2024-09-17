<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskDscriptionByManagerRequest;
use App\Http\Requests\UpdateTaskNotesRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function index(FilterRequest $request)
    {
        $status = $request->only('status');
        $priority = $request->only('priority');

        $tasks = Task::priority($priority)
            ->status($status)
            ->get();
        if ($tasks->isEmpty()) {
            return response(['error' => 'error', 'message' => 'there is no task'], 200);
        } else {
            return response(['status' => 'success', 'message' => 'get tasks successfuly', 'tasks' => $tasks], 200);
        }
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
        $user = Auth::user();
        $user = User::findOrFail($user->id);
        $project = Project::findOrFail($task->project_id);

        // Check if the user is associated with the project
        $userProjectRelation = $project->users()->where('user_id', $user->id)->first()?->pivot->role;
        if (!$user->is_admin && !$userProjectRelation) {
            abort(response()->json([
                'message' => 'User is not associated with this project.',
            ], 404));
        }
        // Ensure that there is an authenticated user
        if (!$user || (!$user->is_admin && !$userProjectRelation)) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }
        $task = Task::findOrFail($task->id);
        return response()->json($task, 200);
    }

    /**
     * Update task description by manager how create it.
     * @param UpdateTaskDscriptionByManagerRequest $request
     * @param Task $task
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function updateTaskDscriptionByManager(UpdateTaskDscriptionByManagerRequest $request, Task $task)
    {
        $user = Auth::user();

        $project = Project::findOrFail($task->project_id);
        // Check if the user is associated with the project
        $userProjectRelation = $project->users()->where('user_id', $user->id)->first()?->pivot->role;

        if (!$user->is_admin && !$userProjectRelation) {
            abort(response()->json([
                'message' => 'User is not associated with this project.',
            ], 404));
        }
        // Check if the user is a manager for this project
        $isManager = $userProjectRelation === 'manager';
        // Ensure that there is an authenticated user
        if (!$user || (!$user->is_admin && !$isManager)) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }
        $validatedData = $request->validated();
        $task = $this->taskService->updateTask($task, $validatedData);
        if (isset($task['error'])) {
            return response(['error' => $task['error'], 'message' => $task['message']], $task['status']);
        } else {
            return response(['status' => $task['status'], 'message' => $task['message'], 'task' => $task['task']], $task['status']);
        }
    }

    /**
     * Update task status by developer how do it.
     * @param UpdateTaskStatusRequest $request
     * @param Task $task
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function updateTaskStatusByDeveloer(UpdateTaskStatusRequest $request, Task $task)
    {
        $user = Auth::user();

        $project = Project::findOrFail($task->project_id);
        // Check if the user is associated with the project
        $userProjectRelation = $project->users()->where('user_id', $user->id)->first()?->pivot->role;

        if (!$user->is_admin && !$userProjectRelation) {
            abort(response()->json([
                'message' => 'User is not associated with this project.',
            ], 404));
        }
        // Check if the user is a manager for this project
        $isManager = $userProjectRelation === 'developer';
        // Ensure that there is an authenticated user
        if (!$user || (!$user->is_admin && !$isManager)) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }
        //==========
        $validatedData = $request->validated();
        $task = $this->taskService->updateTask($task, $validatedData);
        if (isset($task['error'])) {
            return response(['error' => $task['error'], 'message' => $task['message']], $task['status']);
        } else {
            return response(['status' => $task['status'], 'message' => $task['message'], 'task' => $task['task']], $task['status']);
        }
    }
    //===============
    /**
     * Update task notes by tester .
     * @param UpdateTaskNotesRequest $request
     * @param Task $task
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function updateNotesByTester(UpdateTaskNotesRequest $request, Task $task)
    {
        $user = Auth::user();

        $project = Project::findOrFail($task->project_id);
        // Check if the user is associated with the project
        $userProjectRelation = $project->users()->where('user_id', $user->id)->first()?->pivot->role;

        if (!$user->is_admin && !$userProjectRelation) {
            abort(response()->json([
                'message' => 'User is not associated with this project.',
            ], 404));
        }
        // Check if the user is a manager for this project
        $isManager = $userProjectRelation === 'tester';
        // Ensure that there is an authenticated user
        if (!$user || (!$user->is_admin && !$isManager)) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }
        $validatedData = $request->validated();
        $task = $this->taskService->updateTask($task, $validatedData);
        if (isset($task['error'])) {
            return response(['error' => $task['error'], 'message' => $task['message']], $task['status']);
        } else {
            return response(['status' => $task['status'], 'message' => $task['message'], 'task' => $task['task']], $task['status']);
        }
    }

    /**
     * Remove task from storage.
     * @param Task $task
     * @return \Illuminate\HTTP\JsonResponse
     */
    public function destroy(Task $task)
    {
        // Get the authenticated user
        $user = Auth::user();

        $project = Project::findOrFail($task->project_id);
        // Check if the user is associated with the project
        $userProjectRelation = $project->users()->where('user_id', $user->id)->first()?->pivot->role;

        if (!$user->is_admin && !$userProjectRelation) {
            abort(response()->json([
                'message' => 'User is not associated with this project.',
            ], 404));
        }
        // Check if the user is a manager for this project
        $isManager = $userProjectRelation === 'manager';
        // Ensure that there is an authenticated user
        if (!$user || (!$user->is_admin && !$isManager)) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }

        $task = $this->taskService->deleteTask($task);
        if (isset($task['error'])) {
            return response(['error' => $task['error'], 'message' => $task['message']], $task['status']);
        } else {
            return response(['status' => $task['status'], 'message' => $task['message']], $task['status']);
        }
    }
    //===============
    /**
     * get Highest Priority Task .
     * @param Request $request
     * @param $projectId
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function getHighestPriorityTask(Request $request, $projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $titleCondition = $request->input('title') ?? null;
        $statusCondition = $request->input('status') ?? null;

        $task = $project->highestPriorityTaskWithConditions($titleCondition, $statusCondition)->first();

        if (!$task) {
            return response()->json(['message' => 'No task found'], 404);
        }

        return response()->json($task, 200);
    }
}
