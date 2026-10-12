<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
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

    // Admin
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [MeetingRoomController::class, 'index'])->name('dashboard');
        Route::get('/meeting-rooms', [MeetingRoomController::class, 'index'])->name('meeting-rooms.index');
        Route::get('/meeting-rooms/create', [MeetingRoomController::class, 'create'])->name('meeting-rooms.create');
        Route::post('/meeting-rooms', [MeetingRoomController::class, 'store'])->name('meeting-rooms.store');
        Route::get('/meeting-rooms/{meetingRoom}/edit', [MeetingRoomController::class, 'edit'])->name('meeting-rooms.edit');
        Route::put('/meeting-rooms/{meetingRoom}', [MeetingRoomController::class, 'update'])->name('meeting-rooms.update');

        // Bookings Management
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    });

    // Staff
    Route::middleware('role:Staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', function () {
            return redirect()->route('staff.bookings.index');
        })->name('dashboard');
        Route::get('/bookings', [BookingController::class, 'staffIndex'])->name('bookings.index');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    });

    // Root /dashboard route for staff
    Route::middleware(['auth', 'role:Staff'])->group(function () {
        Route::get('/dashboard', function () {
            return redirect()->route('staff.bookings.index');
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
