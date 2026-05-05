<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

        // Redirect to gear selection for this new user
        return redirect()->route('users.gear.edit', $user);
    }
}
