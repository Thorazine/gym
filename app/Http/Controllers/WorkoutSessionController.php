<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Soundcloud;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkoutSessionController extends Controller
{
    public function count(Request $request, User $user)
    {
        $type = $request->query('type');

        return Inertia::render('Workout/Count', [
            'user' => $user,
            'workoutType' => $type,
        ]);
    }

    public function timing(Request $request, User $user)
    {
        $type = $request->query('type');
        $count = $request->query('count');

        return Inertia::render('Workout/Timing', [
            'user' => $user,
            'workoutType' => $type,
            'exerciseCount' => $count,
        ]);
    }

    public function setup(Request $request, User $user)
    {
        $type = $request->query('type');
        $count = (int) $request->query('count', 5);
        $timing = $request->query('timing');

        // Get the IDs of the gear the user has
        $userGearIds = $user->workoutItems()->pluck('workout_items.id')->toArray();
        
        // Always include Bodyweight as available gear
        $bodyweightItem = \App\Models\WorkoutItem::where('name', 'Bodyweight')->first();
        if ($bodyweightItem && !in_array($bodyweightItem->id, $userGearIds)) {
            $userGearIds[] = $bodyweightItem->id;
        }

        // Query exercises
        $query = Exercise::query();

        // Filter by user's gear
        // An exercise should only be selected if all its required gear is owned by the user.
        // Or simply, an exercise's required gear must be a subset of the user's gear.
        // Actually, let's just make sure the exercise doesn't require any gear the user doesn't have.
        $query->whereDoesntHave('workoutItems', function ($q) use ($userGearIds) {
            $q->whereNotIn('workout_items.id', $userGearIds);
        });

        // Filter by type if not "full"
        if ($type !== 'full') {
            $upperBodyParts = ['Chest', 'Back', 'Shoulders', 'Biceps', 'Triceps', 'Forearms', 'Traps', 'Lats', 'Neck'];
            $lowerBodyParts = ['Quadriceps', 'Hamstrings', 'Calves', 'Abductors', 'Adductors', 'Glutes'];
            $coreBodyParts = ['Core', 'Obliques', 'Lower Back'];
            $buttBodyParts = ['Glutes'];

            $query->whereHas('bodyParts', function ($q) use ($type, $upperBodyParts, $lowerBodyParts, $coreBodyParts, $buttBodyParts) {
                if ($type === 'upper') {
                    $q->whereIn('name', $upperBodyParts);
                } elseif ($type === 'lower') {
                    $q->whereIn('name', $lowerBodyParts);
                } elseif ($type === 'core') {
                    $q->whereIn('name', $coreBodyParts);
                } elseif ($type === 'butt') {
                    $q->whereIn('name', $buttBodyParts);
                } else {
                    $q->where('name', 'like', '%'.$type.'%');
                }
            });
        }

        // Randomize and limit
        $exercises = $query->inRandomOrder()->limit($count)->get();
        $exerciseIds = $exercises->pluck('id')->implode(',');

        return Inertia::render('Workout/Setup', [
            'user' => $user,
            'workoutType' => $type,
            'exerciseCount' => $count,
            'timing' => $timing,
            'exercises' => $exercises,
            'exerciseIds' => $exerciseIds,
        ]);
    }

    public function timer(Request $request, User $user)
    {
        $type = $request->query('type');
        $count = $request->query('count');
        $timing = $request->query('timing');
        $exerciseIds = explode(',', $request->query('exercises', ''));
        $musicId = $request->query('music');
        
        $musicUrl = null;
        if ($musicId) {
            $music = Soundcloud::find($musicId);
            if ($music) {
                $musicUrl = $music->url;
            }
        }

        // Fetch exercises in the exact order they were provided in the URL
        if (empty(array_filter($exerciseIds))) {
            $exercises = collect([]);
        } else {
            $exercises = Exercise::whereIn('id', $exerciseIds)->get();
            $exercises = $exercises->sortBy(function ($exercise) use ($exerciseIds) {
                return array_search($exercise->id, $exerciseIds);
            })->values();
        }

        return Inertia::render('Workout/Timer', [
            'user' => $user,
            'workoutType' => $type,
            'exerciseCount' => $count,
            'timing' => $timing,
            'exercises' => $exercises,
            'exerciseIds' => $request->query('exercises'),
            'musicUrl' => $musicUrl,
        ]);
    }

    public function music(Request $request, User $user)
    {
        $type = $request->query('type');
        $count = $request->query('count');
        $timing = $request->query('timing');
        $exerciseIds = $request->query('exercises');

        return Inertia::render('Workout/Music', [
            'user' => $user,
            'workoutType' => $type,
            'exerciseCount' => $count,
            'timing' => $timing,
            'exerciseIds' => $exerciseIds,
            'soundclouds' => Soundcloud::where('user_id', $user->id)->get(),
        ]);
    }
}
