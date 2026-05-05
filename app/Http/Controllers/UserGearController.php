<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkoutItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserGearController extends Controller
{
    public function edit(User $user)
    {
        $allGear = WorkoutItem::orderBy('name')->get();
        // Pluck the IDs of the gear the user already has
        $userGearIds = $user->workoutItems()->pluck('workout_items.id');

        return Inertia::render('Users/Gear', [
            'user' => $user,
            'allGear' => $allGear,
            'userGearIds' => $userGearIds,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'gear_ids' => 'array',
            'gear_ids.*' => 'exists:workout_items,id',
        ]);

        $user->workoutItems()->sync($validated['gear_ids'] ?? []);

        // After setting gear, redirect to home screen to select the user
        return redirect()->route('home');
    }
}
