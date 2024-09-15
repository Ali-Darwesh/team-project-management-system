<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TaskController;
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



//=====================
//=====   Tasks   =====
//=====================
Route::post('/tasks/{taskId}/start', [TaskController::class, 'startTask']);
Route::post('/tasks/{taskId}/end', [TaskController::class, 'endTask']);
