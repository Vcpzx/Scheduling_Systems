<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});