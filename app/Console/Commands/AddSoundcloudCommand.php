<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\text;

class AddSoundcloudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym:add-soundcloud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract a title and URL from a Soundcloud URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = text(
            label: 'What is the Soundcloud URL?',
            required: true
        );

        $response = Http::get('https://soundcloud.com/oembed', [
            'format' => 'json',
            'url' => $url,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $title = $data['title'] ?? 'Unknown Title';
            
            $soundcloud = \App\Models\Soundcloud::create([
                'title' => $title,
                'url' => $url,
            ]);
            
            $this->info("Title: {$soundcloud->title}");
            $this->info("URL: {$soundcloud->url}");
            $this->info("Successfully saved to database!");
        } else {
            $this->error('Failed to extract information from the provided URL.');
        }
    }
}
