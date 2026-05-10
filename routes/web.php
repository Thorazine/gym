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
Route::post('/users/{user}/login', [UserAccountController::class, 'login'])->name('users.login');

// Gear Selection (Per User)
Route::get('/users/{user}/gear', [UserGearController::class, 'edit'])->name('users.gear.edit');
Route::put('/users/{user}/gear', [UserGearController::class, 'update'])->name('users.gear.update');

// Workout Type Selection (Per User)
Route::get('/users/{user}/type', [WorkoutTypeController::class, 'index'])->name('users.type.index');

// Workout Session Flow
Route::get('/users/{user}/workout/count', [WorkoutSessionController::class, 'count'])->name('users.workout.count');
Route::get('/users/{user}/workout/timing', [WorkoutSessionController::class, 'timing'])->name('users.workout.timing');
Route::get('/users/{user}/workout/setup', [WorkoutSessionController::class, 'setup'])->name('users.workout.setup');
Route::get('/users/{user}/workout/music', [WorkoutSessionController::class, 'music'])->name('users.workout.music');
Route::get('/users/{user}/workout/timer', [WorkoutSessionController::class, 'timer'])->name('users.workout.timer');

// Video Workouts
Route::get('/users/{user}/workout/video-categories', [\App\Http\Controllers\VideoWorkoutController::class, 'categories'])->name('users.workout.video-categories');
Route::get('/users/{user}/workout/video-categories/{videoCategory}', [\App\Http\Controllers\VideoWorkoutController::class, 'videos'])->name('users.workout.videos');
Route::get('/users/{user}/workout/videos/{video}/play', [\App\Http\Controllers\VideoWorkoutController::class, 'play'])->name('users.workout.video.play');

// Listen to Music
Route::get('/users/{user}/listen', [\App\Http\Controllers\ListenController::class, 'index'])->name('users.listen.index');
Route::get('/users/{user}/listen/{soundcloud}', [\App\Http\Controllers\ListenController::class, 'play'])->name('users.listen.play');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/last-workout', [\App\Http\Controllers\LastWorkoutController::class, 'index'])->name('lastWorkout');
});

// Workout saving routes
Route::post('/users/{user}/workout', [\App\Http\Controllers\WorkoutController::class, 'store'])->name('users.workout.store');
Route::put('/workouts/{workout}', [\App\Http\Controllers\WorkoutController::class, 'update'])->name('workouts.update');

Route::get('/api/gym/config', function () {
    return response()->json([
        'timings' => config('gym.timings', [])
    ]);
});

require __DIR__.'/settings.php';
