<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

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
        $user = $this->userService->deleteUser($user);
        if (isset($user['error'])) {
            return response(['error' => $user['error'], 'message' => $user['message']], $user['status']);
        } else {
            return response(['status' => $user['status'], 'message' => $user['message']], $user['status']);
        }
    }
}
