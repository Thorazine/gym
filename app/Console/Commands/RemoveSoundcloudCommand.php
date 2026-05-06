<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gym:remove-soundcloud')]
#[Description('Remove a Soundcloud track')]
class RemoveSoundcloudCommand extends Command
{
    public function handle()
    {
        $tracks = \App\Models\Soundcloud::pluck('title', 'id')->toArray();

        if (empty($tracks)) {
            $this->error('No Soundcloud tracks found.');
            return;
        }

        $trackId = \Laravel\Prompts\select(
            label: 'Which Soundcloud track do you want to remove?',
            options: $tracks,
        );

        $track = \App\Models\Soundcloud::find($trackId);
        $track->delete();

        $this->info("Soundcloud track '{$track->title}' removed successfully.");
    }
}
