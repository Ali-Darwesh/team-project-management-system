<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Constracor to inject user Service
     * @param UserService $userService
     */
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getProjectTasks(Request $request, $projectId)
    {
        // $user = Auth::user();
        $userId = 2; // Assume this is the user ID you're interested in
        $user = User::find($userId);
        $project = Project::find($projectId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($request->has('latest')) {
            $tasks = $project->latestTask;
        } elseif ($request->has('old')) {
            $tasks = $project->oldestTask;
        } else {
            $tasks = $project->tasks;
        }
        return response()->json($tasks, 200);
    }
    /**
     * Update user data in storage.
     * @param UpdateUserRequest $request
     * @param User $user
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->safe()->all();
        $user = $this->userService->updateUser($user, $validatedData);
        if (isset($user['error'])) {
            return response(['error' => $user['error'], 'message' => $user['message']], $user['status']);
        } else {
            return response(['status' => $user['status'], 'message' => $user['message'], 'user' => $user['user']], $user['status']);
        }
    }
    /**
     * Delete user from storage.
     * @param User $user
     * @return \Illuminate\HTTP\JsonResponse

     */
    public function destroy(User $user)
    {
        $user = Auth::user();

        // Ensure that there is an authenticated user
        if (!$user || !$user->is_admin) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }

        $user = $this->userService->deleteUser($user);
        if (isset($user['error'])) {
            return response(['error' => $user['error'], 'message' => $user['message']], $user['status']);
        } else {
            return response(['status' => $user['status'], 'message' => $user['message']], $user['status']);
        }
    }
}
