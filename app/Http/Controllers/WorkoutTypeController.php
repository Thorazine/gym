<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class WorkoutTypeController extends Controller
{
    public function index(User $user)
    {
        // Screen 2-4: Type selection
        return Inertia::render('Workout/Type', [
            'user' => $user,
        ]);
    }
}
