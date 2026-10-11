<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeetingRoomController;
use Illuminate\Support\Facades\Auth;
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
        Route::get('/dashboard', [MeetingRoomController::class, 'index'])->name('dashboard');
        Route::get('/meeting-rooms', [MeetingRoomController::class, 'index'])->name('meeting-rooms.index');
        Route::get('/meeting-rooms/create', [MeetingRoomController::class, 'create'])->name('meeting-rooms.create');
        Route::post('/meeting-rooms', [MeetingRoomController::class, 'store'])->name('meeting-rooms.store');
    });

    // Staff Area (protected by role:Staff)
    Route::middleware('role:Staff')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard'); // Temporary until staff dashboard is created
        })->name('staff.dashboard');
    });
});

// Fallback Route
Route::fallback(function () {
    if (! Auth::check()) {
        return redirect('/');
    }

    abort(404);
});
