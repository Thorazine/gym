<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $workouts = $user->workouts()->whereNotNull('end_time')->get();
        $totalWorkouts = $workouts->count();
        
        $totalTimeSeconds = 0;
        foreach ($workouts as $workout) {
            $start = strtotime($workout->start_time);
            $end = strtotime($workout->end_time);
            $totalTimeSeconds += max(0, $end - $start);
        }

        return Inertia::render('Dashboard', [
            'totalWorkouts' => $totalWorkouts,
            'totalTimeSeconds' => $totalTimeSeconds,
        ]);
    }
}
