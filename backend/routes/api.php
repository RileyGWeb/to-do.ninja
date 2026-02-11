<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskListController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    Route::post('/projects/reorder', [ProjectController::class, 'reorder']);

    Route::post('/projects/{project}/lists', [TaskListController::class, 'store']);
    Route::put('/lists/{taskList}', [TaskListController::class, 'update']);
    Route::delete('/lists/{taskList}', [TaskListController::class, 'destroy']);
    Route::post('/projects/{project}/lists/reorder', [TaskListController::class, 'reorder']);

    Route::post('/lists/{taskList}/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/lists/{taskList}/tasks/reorder', [TaskController::class, 'reorder']);
});
