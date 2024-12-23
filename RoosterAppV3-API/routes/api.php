<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtValidator;
use App\Http\Middleware\RoleGuard;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Public routes
Route::post('/token', [AuthController::class, 'login']);

// Secured routes
Route::middleware([JwtValidator::class])->group(function () {
    
    Route::resource('user', UserController::class)->middleware(RoleGuard::class);
});