<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\UserGearController;
use App\Http\Controllers\WorkoutSessionController;
use App\Http\Controllers\WorkoutTypeController;
use Illuminate\Support\Facades\Route;

// Main Screen (Users)
Route::get('/', [HomeController::class, 'index'])->name('home');

// User Account Creation
Route::post('/users', [UserAccountController::class, 'store'])->name('users.store');

// Gear Selection (Per User)
Route::get('/users/{user}/gear', [UserGearController::class, 'edit'])->name('users.gear.edit');
Route::put('/users/{user}/gear', [UserGearController::class, 'update'])->name('users.gear.update');

// Workout Type Selection (Per User)
Route::get('/users/{user}/type', [WorkoutTypeController::class, 'index'])->name('users.type.index');

// Workout Session Flow
Route::get('/users/{user}/workout/count', [WorkoutSessionController::class, 'count'])->name('users.workout.count');
Route::get('/users/{user}/workout/timing', [WorkoutSessionController::class, 'timing'])->name('users.workout.timing');
Route::get('/users/{user}/workout/setup', [WorkoutSessionController::class, 'setup'])->name('users.workout.setup');
Route::get('/users/{user}/workout/timer', [WorkoutSessionController::class, 'timer'])->name('users.workout.timer');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
