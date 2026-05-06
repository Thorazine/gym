<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gym:remove-youtube')]
#[Description('Remove a YouTube video')]
class RemoveYoutubeCommand extends Command
{
    public function handle()
    {
        $videos = \App\Models\Video::pluck('title', 'id')->toArray();

        if (empty($videos)) {
            $this->error('No YouTube videos found.');
            return;
        }

        $videoId = \Laravel\Prompts\select(
            label: 'Which YouTube video do you want to remove?',
            options: $videos,
        );

        $video = \App\Models\Video::find($videoId);
        $video->delete();

        $this->info("YouTube video '{$video->title}' removed successfully.");
    }
}
