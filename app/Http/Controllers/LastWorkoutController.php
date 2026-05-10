<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LastWorkoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $lastWorkout = $user->workouts()->latest('id')->first();
        $workouts = $user->workouts()->latest('id')->get();

        return Inertia::render('LastWorkout', [
            'lastWorkout' => $lastWorkout,
            'workouts' => $workouts,
        ]);
    }
}
