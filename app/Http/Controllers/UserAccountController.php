<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAccountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
        ]);

        Auth::login($user);

        // Redirect to gear selection for this new user
        return redirect()->route('users.gear.edit', $user);
    }

    public function login(User $user)
    {
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
