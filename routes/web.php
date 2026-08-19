<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
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
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('role:teacher')->group(function () {
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::patch('/users/{user}', [AdminController::class, 'decideUser'])->name('users.decide');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::patch('/bookings/{bookingRequest}', [AdminController::class, 'decideBooking'])->name('bookings.decide');
        Route::post('/resources', [AdminController::class, 'storeResource'])->name('resources.store');
        Route::put('/resources/{resource}', [AdminController::class, 'updateResource'])->name('resources.update');
        Route::delete('/resources/{resource}', [AdminController::class, 'deleteResource'])->name('resources.delete');
        Route::post('/notifications', [AdminController::class, 'storeNotification'])->name('notifications.store');
    });
});