<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\JwtValidator;
use App\Http\Middleware\RoleGuard;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::post('/token', [AuthController::class, 'login']);

// Secured routes
Route::middleware([JwtValidator::class])->group(function () {
    
    // Resource routes
    Route::resource('user', UserController::class)->middleware(RoleGuard::class);

    // Custom routes
    Route::get('registrations', [RegistrationController::class, 'GetRegistrations']);
    Route::post('registrations/start', [RegistrationController::class, 'Start']);
    Route::post('registrations/end', [RegistrationController::class, 'End']);
    Route::get('registrations/today', [RegistrationController::class, 'Today']);
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('profile/password', [ProfileController::class, 'updatePassword']);
});