<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $lastWorkout = $user->workouts()->latest('id')->first();
        $workouts = $user->workouts()->latest('id')->get();

        return Inertia::render('Dashboard', [
            'lastWorkout' => $lastWorkout,
            'workouts' => $workouts,
        ]);
    }
}
