<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'processForgotPassword'])->name('password.email');

    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [ScheduleController::class, 'home'])->name('home');
    Route::get('/campus-schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Teacher Routes
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::post('/schedules', [TeacherDashboardController::class, 'storeClassSchedule'])->name('schedules.store');
        Route::delete('/schedules/{classSchedule}', [TeacherDashboardController::class, 'deleteClassSchedule'])->name('schedules.delete');
        Route::post('/notifications', [TeacherDashboardController::class, 'sendNotification'])->name('notifications.send');
    });
    Route::middleware('role:teacher')->group(function () {
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    });

    // Student Routes
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::post('/enroll-subject', [StudentDashboardController::class, 'enrollSubject'])->name('enroll.subject');
        Route::post('/enroll-section', [StudentDashboardController::class, 'enrollSection'])->name('enroll.section');
        Route::delete('/unenroll/{classSchedule}', [StudentDashboardController::class, 'unenroll'])->name('unenroll');
    });

    // Admin Routes
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