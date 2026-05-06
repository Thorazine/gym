<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class YoutubeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_youtube_settings_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/settings/youtube');

        $response->assertOk();
    }

    public function test_user_can_add_youtube_video(): void
    {
        $user = User::factory()->create();
        $category = VideoCategory::create(['name' => 'Yoga', 'slug' => 'yoga']);

        Http::fake([
            'youtube.com/*' => Http::response('<title>Test Video Title - YouTube</title>', 200),
        ]);

        $response = $this->actingAs($user)->post('/settings/youtube', [
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'video_category_id' => $category->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('videos', [
            'video_category_id' => $category->id,
            'title' => 'Test Video Title',
            'video_id' => 'dQw4w9WgXcQ',
        ]);
    }

    public function test_user_can_delete_youtube_video(): void
    {
        $user = User::factory()->create();
        $category = VideoCategory::create(['name' => 'Yoga', 'slug' => 'yoga']);
        
        $video = Video::create([
            'user_id' => $user->id,
            'video_category_id' => $category->id,
            'title' => 'Test Video',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'video_id' => 'dQw4w9WgXcQ',
            'placeholder_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
        ]);

        $response = $this->actingAs($user)->delete("/settings/youtube/{$video->id}");

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseMissing('videos', [
            'id' => $video->id,
        ]);
    }
}
