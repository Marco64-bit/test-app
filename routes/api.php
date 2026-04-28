<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/login/verify', [LoginController::class, 'verifyLogin']);

// All routes inside this group require the user to be logged in!
Route::middleware('auth')->group(function () {

    // Get Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    // Update Profile
    Route::put('/profile', [ProfileController::class, 'update']);
    // Upload Profile Image
    Route::post('/profile/image', [ProfileController::class, 'uploadImage']);
    // External Links Route
    Route::get('/dashboard/links', [DashboardController::class, 'getExternalLinks']);
});
