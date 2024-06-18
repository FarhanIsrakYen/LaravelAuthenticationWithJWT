<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'getUser']);
        Route::put('/', [UserController::class, 'updateUser']);
        Route::delete('deactivate', [UserController::class, 'deactivateUser']);
        Route::post('logout', [UserController::class, 'logout']);
        Route::put('password', [UserController::class, 'updatePassword']);
        Route::post('refresh', [UserController::class, 'refresh']);
    });
});
