<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VideoWorkoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_add_a_video_category_via_command(): void
    {
        $this->artisan('gym:add-video-category')
             ->expectsQuestion('What is the name of the category?', 'Test Category')
             ->expectsOutput('Category \'Test Category\' added successfully.')
             ->assertSuccessful();

        $this->assertDatabaseHas('video_categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_it_can_add_a_video_via_command(): void
    {
        $category = VideoCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        Http::fake([
            '*' => Http::response('Server Error', 500)
        ]);

        $this->artisan('gym:add-video')
             ->expectsQuestion('Which category does this video belong to?', $category->id)
             ->expectsQuestion('Enter the full YouTube URL of the video:', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
             ->expectsQuestion('Enter the video title:', 'Rick Astley - Never Gonna Give You Up (Official Music Video)')
             ->expectsOutputToContain('added successfully to category \'Test Category\'.')
             ->assertSuccessful();

        $this->assertDatabaseHas('videos', [
            'video_category_id' => $category->id,
            'title' => 'Rick Astley - Never Gonna Give You Up (Official Music Video)',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'video_id' => 'dQw4w9WgXcQ',
            'placeholder_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
        ]);
    }

    public function test_video_categories_page_renders(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.workout.video-categories', $user));

        $response->assertStatus(200);
    }

    public function test_videos_page_renders(): void
    {
        $user = User::factory()->create();
        $category = VideoCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $response = $this->get(route('users.workout.videos', ['user' => $user, 'videoCategory' => $category]));

        $response->assertStatus(200);
    }

    public function test_play_video_page_renders(): void
    {
        $user = User::factory()->create();
        $category = VideoCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
        $video = Video::create([
            'video_category_id' => $category->id,
            'title' => 'Test Title',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'video_id' => 'dQw4w9WgXcQ',
            'placeholder_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
        ]);

        $response = $this->get(route('users.workout.video.play', ['user' => $user, 'video' => $video]));

        $response->assertStatus(200);
    }
}
