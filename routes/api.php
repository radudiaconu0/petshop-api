<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MainPageController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/greeting', function () {
    return 'Hello World';
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::post('login', [AdminController::class, 'login']);
        Route::post('logout', [AdminController::class, 'logout'])->middleware('jwt.auth');
        Route::get('user', [AdminController::class, 'user'])->middleware('jwt.auth');
    });
    Route::post('login', [UserController::class, 'login']);
    Route::post('logout', [UserController::class, 'logout'])->middleware('jwt.auth');
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'user'])->middleware('jwt.auth');
        Route::post('logout', [UserController::class, 'logout'])->middleware('jwt.auth');
        Route::post('create', [UserController::class, 'createUser']);
        Route::get('orders', [UserController::class, 'getOrders'])->middleware('jwt.auth');
        Route::post('forgot-password', [UserController::class, 'forgotPassword']);
        Route::post('reset-password-token', [UserController::class, 'resetPassword']);
    });
    Route::apiResource('categories', CategoryController::class);
    Route::get('main/blog', [MainPageController::class, 'getBlogPosts']);
});

Route::get('up', function () {
    return response()->json(['status' => 'ok']);
});
