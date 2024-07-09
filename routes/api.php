<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MainPageController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::post('login', [AdminController::class, 'login']);
        Route::get('logout', [AdminController::class, 'logout'])->middleware('jwt.auth');
        Route::post('create', [AdminController::class, 'createAdminUser'])->middleware('jwt.auth');
        Route::get('user-listing', [AdminController::class, 'getUsers'])->middleware('jwt.auth');
        Route::put('user-edit/{uuid}', [AdminController::class, 'editUser'])->middleware('jwt.auth');
        Route::delete('user-delete/{uuid}', [AdminController::class, 'deleteUser'])->middleware('jwt.auth');
    })->middleware('is_admin');
    Route::post('login', [UserController::class, 'login']);
    Route::post('logout', [UserController::class, 'logout'])->middleware('jwt.auth');
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'user'])->middleware('jwt.auth');
        Route::get('logout', [UserController::class, 'logout'])->middleware('jwt.auth');
        Route::post('create', [UserController::class, 'createUser']);
        Route::get('orders', [UserController::class, 'getOrders'])->middleware('jwt.auth');
        Route::post('forgot-password', [UserController::class, 'forgotPassword']);
        Route::post('reset-password-token', [UserController::class, 'resetPassword']);
        Route::put('edit', [UserController::class, 'editUser'])->middleware('jwt.auth');
    });
    Route::get('main/blog', [MainPageController::class, 'getBlogPosts']);
    Route::get('main/promotions', [MainPageController::class, 'getPromotions']);
    Route::get('main/blog/{uid}', [MainPageController::class, 'getPost']);
});

