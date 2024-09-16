<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//======================
//=====   users   =====
//=====================
Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::post('/users', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
});
//=====================
//=====  Projects =====
//=====================

Route::post('/projects', [ProjectController::class, 'store']);
Route::post('/add_users_to_project/{projectId}', [ProjectController::class, 'addUsersToProject']);
Route::get('/get_project_tasks/{projectId}', [UserController::class, 'getProjectTasks']);


//=====================
//=====   Tasks   =====
//=====================
Route::post('/tasks', [TaskController::class, 'store']);
Route::post('/tasks/{taskId}/start', [TaskController::class, 'startTask']);
Route::post('users/{userId}/tasks/{taskId}/end', [TaskController::class, 'endTask']);
