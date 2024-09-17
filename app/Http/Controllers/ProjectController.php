<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUsersToProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Constracor to inject Book Service
     * @param UserService $projectService
     */
    protected $projectService;
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $user = User::findOrFail($user->id);
        $projects = Project::with('users')->get();

        // Ensure that there is an authenticated user
        if (!$user || (!$user->is_admin)) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }
        return response()->json($projects, 200);
    }



    /**
     * create new task.
     * @param StoreProjectRequest $request
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function store(StoreProjectRequest $request)
    {
        $validatedData = $request->validated();
        $project = $this->projectService->createProject($validatedData);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message'], 'project' => $project['project']], $project['status']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $user = Auth::user();
        $user = User::findOrFail($user->id);
        $project = Project::findOrFail($project->id);
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
        $project = $project->load('users');
        return response()->json($project, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validatedData = $request->validated();
        $project = $this->projectService->updateProject($project, $validatedData);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message'], 'project' => $project['project']], $project['status']);
        }
    }


    /**
     * Remove project from storage.
     * @param Project $project
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function destroy(Project $project)
    {
        $user = Auth::user();
        // Ensure that there is an authenticated user
        if (!$user || (!$user->is_admin && $this->user()->id !== $user->id)) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }

        $project = $this->projectService->deleteProject($project);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message']], $project['status']);
        }
    }
    /**
     * add Users To Project by admin.
     * @param AddUsersToProjectRequest $request
     * @param  $projectId
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function addUsersToProject(AddUsersToProjectRequest $request, $projectId)
    {
        $validatedData = $request->validated();

        $project = $this->projectService->addUsersToProjectPivot($projectId, $validatedData);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message'], 'project' => $projectId, 'project_users' => $project['project_users']], $project['status']);
        }
    }
    /**
     * set the start time of work on project.
     * @param  $projectId
     * @param  $userId
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function startProject($projectId, $userId)
    {
        $user = Auth::user();
        // Ensure that there is an authenticated user
        if (!$user) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }
        $project = $this->projectService->startTime($projectId, $userId);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message'], 'task_start_time' => $project['start_time']], $project['status']);
        }
    }
    /**
     * set the end time of work on project.
     * @param  $projectId
     * @param  $userId
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function endProject($projectId, $userId)
    {
        $user = Auth::user();
        // Ensure that there is an authenticated user
        if (!$user) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }
        $project = $this->projectService->endTime($projectId, $userId);
        if (isset($task['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response()->json([
                'message' => $project['message'],
                'session_hours' => $project['session_hours'],
                'userTaskInfo' => $project['userTaskInfo'],
                'end_time' => $project['end_time'],
            ], $project['status']);
        }
    }
}
