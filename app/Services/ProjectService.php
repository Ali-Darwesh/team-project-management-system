<?php

namespace App\Services;

use App\Models\Project;
use Exception;

class ProjectService
{
    /**
     * service to create project.
     * @param array $data
     * @return array 

     */
    public function createProject(array $data)
    {
        try {
            $project = Project::create($data);
            return ['message' => 'project created successfully', 'project' => $project, 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'create project failed', 'error' => $e, 'status' => 404];
        }
    }
    /**
     * service to update project data in storage.
     * @param Project $project
     * @param array $data
     * @return array 

     */
    public function updateProject(Project $project, array $data)
    {
        try {
            $project = Project::findOrFail($project->id);
            $project->update($data);
            return ['message' => 'project updated successfully', 'project' => $project, 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'Update project failed', 'error' => $e, 'status' => 404];
        }
    }
    /**
     * service method used to delete project from storage.
     * @param Project $project
     *@return array

     */
    public function deleteProject(Project $project)
    {
        try {
            $project = Project::findOrFail($project->id);
            $project->delete();
            return ['message' => 'Project deleted successfully', 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'Delete project failed', 'error' => $e, 'status' => 404];
        }
    }
    public function addUsersToProjectPivot($projectId, array $validatedData)
    {
        try {
            // Find the project by ID
            $project = Project::findOrFail($projectId);

            // Attach the users with their roles to the project
            $project_users = $project->users()->attach([$validatedData['user_id'] => ['role' => $validatedData['role']]]);
            return ['message' => 'user added successfully', 'project_users' => $project_users, 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'adding user failed', 'error' => $e, 'status' => 404];
        }
    }
}
