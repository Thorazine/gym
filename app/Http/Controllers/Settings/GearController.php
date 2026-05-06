<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\WorkoutItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class GearController extends Controller
{
    /**
     * Show the user's gear settings page.
     */
    public function edit(Request $request): Response
    {
        $allGear = WorkoutItem::orderBy('name')->get();
        // Pluck the IDs of the gear the user already has
        $userGearIds = $request->user()->workoutItems()->pluck('workout_items.id');

        return Inertia::render('settings/Gear', [
            'allGear' => $allGear,
            'userGearIds' => $userGearIds,
        ]);
    }

    /**
     * Update the user's gear.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gear_ids' => 'array',
            'gear_ids.*' => 'exists:workout_items,id',
        ]);

        $request->user()->workoutItems()->sync($validated['gear_ids'] ?? []);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Gear updated.')]);

        return to_route('settings.gear.edit');
    }
}
