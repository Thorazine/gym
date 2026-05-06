<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\VideoCategory;
use Illuminate\Support\Str;
use function Laravel\Prompts\text;

#[Signature('gym:add-video-category')]
#[Description('Add a new video category')]
class AddVideoCategoryCommand extends Command
{
    public function handle()
    {
        $name = text(
            label: 'What is the name of the category?',
            required: true,
        );

        $category = VideoCategory::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        $this->info("Category '{$category->name}' added successfully.");
    }
}
