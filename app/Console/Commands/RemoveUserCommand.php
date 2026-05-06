<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gym:remove-user')]
#[Description('Remove a user')]
class RemoveUserCommand extends Command
{
    public function handle()
    {
        $users = \App\Models\User::pluck('name', 'id')->toArray();

        if (empty($users)) {
            $this->error('No users found.');
            return;
        }

        $userId = \Laravel\Prompts\select(
            label: 'Which user do you want to remove?',
            options: $users,
        );

        $user = \App\Models\User::find($userId);
        $user->delete();

        $this->info("User '{$user->name}' removed successfully.");
    }
}
