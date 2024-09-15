<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUsersToProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;

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
        //
    }

    /**
     * Store a newly created resource in storage.
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
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
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project = $this->projectService->deleteProject($project);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message']], $project['status']);
        }
    }

    public function addUsersToProject(AddUsersToProjectRequest $request, $projectId)
    {
        // {
        //     "users": [
        //       { "id": 1, "role": "admin" },
        //       { "id": 2, "role": "member" },
        //       { "id": 3, "role": "manager" }
        //     ]
        //   }

        // Validate the request data
        $validatedData = $request->validated();

        $project = $this->projectService->addUsersToProjectPivot($projectId, $validatedData);
        if (isset($project['error'])) {
            return response(['error' => $project['error'], 'message' => $project['message']], $project['status']);
        } else {
            return response(['status' => $project['status'], 'message' => $project['message'], 'project' => $projectId, 'project_users' => $project['project_users']], $project['status']);
        }
    }
}
