<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gym:add-user')]
#[Description('Add a new user')]
class AddUserCommand extends Command
{
    public function handle()
    {
        $name = \Laravel\Prompts\text(
            label: 'Enter the user name:',
            required: true,
            validate: fn (string $value) => match (true) {
                \App\Models\User::where('name', trim($value))->exists() => 'A user with this name already exists.',
                default => null
            }
        );

        $user = \App\Models\User::create([
            'name' => trim($name),
        ]);

        $this->info("User '{$user->name}' added successfully.");
    }
}
