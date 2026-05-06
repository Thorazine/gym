<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\VideoCategory;
use App\Models\Video;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[Signature('gym:add-youtube')]
#[Description('Add a new workout video from YouTube')]
class AddYoutubeCommand extends Command
{
    public function handle()
    {
        $categories = VideoCategory::pluck('name', 'id')->toArray();
        
        if (empty($categories)) {
            $this->error('No video categories found. Please add a category first.');
            return;
        }

        $categoryId = select(
            label: 'Which category does this video belong to?',
            options: $categories,
        );

        $url = text(
            label: 'Enter the full YouTube URL of the video:',
            required: true,
        );

        // Extract video ID
        $videoId = null;
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match)) {
            $videoId = $match[1];
        }

        if (!$videoId) {
            $this->error('Could not extract a valid YouTube video ID from the URL.');
            return;
        }

        $placeholderUrl = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";

        // Extract title
        $this->info('Fetching video title...');
        $title = 'Unknown Title';
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($url);
            $html = $response->body();
            if ($response->successful() && preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
                $title = html_entity_decode(trim(str_replace(' - YouTube', '', $matches[1])));
            } else {
                throw new \Exception('Failed to fetch title');
            }
        } catch (\Exception $e) {
            $this->warn('Could not fetch title automatically. Please enter it manually.');
            $title = text(
                label: 'Enter the video title:',
                required: true,
            );
        }

        Video::create([
            'video_category_id' => $categoryId,
            'title' => $title,
            'url' => "https://www.youtube.com/watch?v={$videoId}",
            'video_id' => $videoId,
            'placeholder_url' => $placeholderUrl,
        ]);

        $this->info("Video '{$title}' added successfully to category '{$categories[$categoryId]}'.");
    }
}
