<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
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
            $project_users = $project->users;
            return ['message' => 'user added successfully', 'project_users' => $project_users, 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'adding user failed', 'error' => $e, 'status' => 404];
        }
    }
    public function startTime($userId, $projectId)
    {
        try {
            $user = User::findOrFail($userId);

            // Fetch the pivot record for the user and the project
            $project = $user->projects()->where('project_id', $projectId)->first();

            if (!$project) {
                return ['status' => 404, 'error' => 'error', 'message' => 'User is not associated with this project'];
            }

            $pivotData = $project->pivot; // Safely access pivot data

            // Check if start_time is null in the pivot table
            if (is_null($pivotData->start_time)) {
                // Set the start time in the pivot table
                $user->projects()->syncWithoutDetaching([
                    $projectId => [
                        'start_time' => Carbon::now(),
                    ]
                ]);

                return ['status' => 200, 'message' => 'Task session started successfully', 'start_time' => Carbon::now()];
            } else {
                return ['status' => 404, 'error' => 'error', 'message' => 'The task session has already started'];
            }
        } catch (Exception $e) {
            return ['message' => 'Failed to set task start time', 'error' => $e->getMessage(), 'status' => 404];
        }
    }

    public function endTime($userId, $projectId)
    {
        try {
            $user = User::findOrFail($userId);

            // Fetch the pivot record for the user and the project
            $pivotData = $user->projects()->where('project_id', $projectId)->first()->pivot;

            if (!is_null($pivotData->start_time)) {
                // Set the end time in the pivot table
                $startTime = Carbon::parse($pivotData->start_time);
                $endTime = Carbon::now();

                // Calculate session time in hours
                $sessionHours = $startTime ? $startTime->diffInMinutes($endTime) / 60 : 0;

                // Update contribution_hours in the pivot table
                $user->projects()->syncWithoutDetaching([
                    $projectId => [
                        // Reset start_time for the next session
                        'start_time' => null,
                        'contribution_hours' => $pivotData->contribution_hours + round($sessionHours, 2),
                        'last_activity' => $endTime // Update last activity
                    ]
                ]);
                $userTaskInfo  = $user->projects()->where('project_id', $projectId)
                    ->withPivot('role', 'contribution_hours', 'last_activity')
                    ->first();


                return [
                    'status' => 200,
                    'message' => 'Project session ended successfully',
                    'session_hours' => round($sessionHours, 2),
                    'userTaskInfo' => $userTaskInfo,
                ];
            } else {
                return ['status' => 404, 'error' => 'error', 'message' => 'Project session was not started'];
            }
        } catch (Exception $e) {
            return ['message' => 'Failed to set project end time', 'error' => $e->getMessage(), 'status' => 404];
        }
    }
}
