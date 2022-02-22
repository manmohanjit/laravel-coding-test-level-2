<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\V1\{UserController, ProjectController, ProjectTaskController, ProjectUserController, AuthController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->name('v1')->group(function () {
    Route::apiResources([
        'users' => UserController::class,
        'projects' => ProjectController::class,
        'projects.tasks' => ProjectTaskController::class,
    ]);

    Route::apiResource('projects.users', ProjectUserController::class)
        ->only(['index', 'update', 'destroy']);

    Route::get('/auth', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/auth', [AuthController::class, 'login']);
});
