<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login']);
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Area (protected by role:Admin)
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/meeting-rooms', function () {
            return view('admin.dashboard');
        })->name('meeting-rooms.index');
    });

    // Staff Area (protected by role:Staff)
    Route::middleware('role:Staff')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard'); // Temporary until staff dashboard is created
        })->name('staff.dashboard');
    });
});
