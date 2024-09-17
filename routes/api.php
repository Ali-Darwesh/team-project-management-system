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
Route::delete('/users/{user}', [UserController::class, 'destroy']);

//=====================
//=====  Projects =====
//=====================

Route::get('/projects', [ProjectController::class, 'index'])->middleware('auth:api');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->middleware('auth:api');
Route::post('/projects', [ProjectController::class, 'store'])->middleware('auth:api');
Route::post('/add_users_to_project/{projectId}', [ProjectController::class, 'addUsersToProject'])->middleware('auth:api');
Route::get('/get_project_tasks/{projectId}', [UserController::class, 'getProjectTasks'])->middleware('auth:api');
Route::post('/user/{userId}/project/{projectId}/start', [ProjectController::class, 'startProject'])->middleware('auth:api');
Route::post('/user/{userId}/project/{projectId}/end', [ProjectController::class, 'endProject'])->middleware('auth:api');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);


//=====================
//=====   Tasks   =====$projectId, $userId
//=====================
Route::get('/tasks', [TaskController::class, 'index'])->middleware('auth:api');
Route::get('/tasks/{task}', [TaskController::class, 'show'])->middleware('auth:api');
Route::get('/projects/{projectId}/highest-priority-task', [TaskController::class, 'getHighestPriorityTask'])->middleware('auth:api');
Route::post('/tasks', [TaskController::class, 'store'])->middleware('auth:api');
Route::post('/tasks/{task}/update_description', [TaskController::class, 'updateTaskDscriptionByManager'])->middleware('auth:api');
Route::post('/tasks/{task}/update_status', [TaskController::class, 'updateTaskStatusByDeveloer'])->middleware('auth:api');
Route::post('/tasks/{task}/update_notes', [TaskController::class, 'updateNotesByTester'])->middleware('auth:api');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
