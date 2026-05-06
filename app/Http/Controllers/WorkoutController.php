<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'start_time' => 'required|date',
        ]);

        $workout = $user->workouts()->create($validated);

        return response()->json($workout);
    }

    public function update(Request $request, Workout $workout)
    {
        $validated = $request->validate([
            'end_time' => 'required|date',
        ]);

        $workout->update($validated);

        return response()->json($workout);
    }
}
