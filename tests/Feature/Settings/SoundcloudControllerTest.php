<?php

namespace Tests\Feature\Settings;

use App\Models\Soundcloud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SoundcloudControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_soundcloud_settings_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/settings/soundcloud');

        $response->assertOk();
    }

    public function test_user_can_add_soundcloud_track(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'soundcloud.com/oembed*' => Http::response(['title' => 'Test Track'], 200),
        ]);

        $response = $this->actingAs($user)->post('/settings/soundcloud', [
            'url' => 'https://soundcloud.com/test/track',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('soundclouds', [
            'title' => 'Test Track',
            'url' => 'https://soundcloud.com/test/track',
        ]);
    }

    public function test_user_can_delete_soundcloud_track(): void
    {
        $user = User::factory()->create();
        
        $soundcloud = Soundcloud::create([
            'user_id' => $user->id,
            'title' => 'Test Track',
            'url' => 'https://soundcloud.com/test/track',
        ]);

        $response = $this->actingAs($user)->delete("/settings/soundcloud/{$soundcloud->id}");

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseMissing('soundclouds', [
            'id' => $soundcloud->id,
        ]);
    }
}
