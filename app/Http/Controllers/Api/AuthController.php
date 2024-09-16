<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Employee;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Constracor to inject user Service
     * @param AuthService $authService
     */
    protected $authService;
    public function __construct(AuthService $authService)
    {
        return $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $safeData = $request->validated();
        $user = $this->authService->createUser($safeData);
        if (isset($user['error'])) {
            // If there is an error in the $user array, return an error response
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message'],
                'error' => $user['error']
            ], $user['status']); // Return 400 Bad Request or another appropriate HTTP status code
        } else {
            // If no error, return the success response
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message'],
                'user' => $user['userData'],
                'authorisation' => $user['authorisation'],
            ], $user['status']);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $user = $this->authService->loginUser($credentials);
        if (isset($user['error'])) {
            // If there is an error in the $user array, return an error response
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message'],
                'error' => $user['error']
            ], $user['status']); // Return 400 Bad Request or another appropriate HTTP status code
        } else {
            // If no error, return the success response
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message'],
                'user' => $user['userData'],
                'authorisation' => $user['authorisation'],
            ], $user['status']);
        }
    }




    public function logout()
    {
        $logout = $this->authService->logoutUser();
        Auth::logout();
        JWTAuth::invalidate(JWTAuth::getToken());
        if (isset($user['error'])) {
            return response()->json([
                'status' => 'failed',
                'message' => $user['message'],
                'error' => $user['error']
            ], $user['status']);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => $logout['message'],
            ], $logout['status']);
        }
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
