<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bag workout',
            'Crossfit',
            'Hyrox',
            'Running',
            'Spinning',
        ];

        foreach ($categories as $category) {
            \App\Models\VideoCategory::firstOrCreate([
                'slug' => \Illuminate\Support\Str::slug($category),
            ], [
                'name' => $category,
            ]);
        }
    }
}
