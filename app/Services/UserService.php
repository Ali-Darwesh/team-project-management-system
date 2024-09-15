<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserService
{
    /**
     * service to update user data in storage.
     * @param User $user
     * @param array $data
     * @return array 

     */
    public function updateUser(User $user, array $data)
    {
        try {
            $user = User::findOrFail($user->id);
            $user->update($data);
            return ['message' => 'user updated successfully', 'user' => $user, 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'Update user failed', 'error' => $e, 'status' => 404];
        }
    }
    /**
     * service method used to delete user from storage.
     * @param User $user
     *@return array

     */
    public function deleteUser(User $user)
    {
        try {
            $user = User::findOrFail($user->id);
            $user->delete();
            return ['message' => 'User deleted successfully', 'status' => 200];
        } catch (Exception $e) {
            return ['message' => 'Delete user failed', 'error' => $e, 'status' => 404];
        }
    }
}
