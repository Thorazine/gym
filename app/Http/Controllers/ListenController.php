<?php

namespace App\Http\Controllers;

use App\Models\Soundcloud;
use App\Models\User;
use Inertia\Inertia;

class ListenController extends Controller
{
    public function index(User $user)
    {
        $soundclouds = $user->soundclouds()->get();

        return Inertia::render('Listen/Index', [
            'user' => $user,
            'soundclouds' => $soundclouds,
        ]);
    }

    public function play(User $user, Soundcloud $soundcloud)
    {
        // Ensure the soundcloud belongs to the user
        if ($soundcloud->user_id !== $user->id) {
            abort(404);
        }

        return Inertia::render('Listen/Player', [
            'user' => $user,
            'soundcloud' => $soundcloud,
        ]);
    }
}
