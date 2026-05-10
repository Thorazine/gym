<?php

namespace Tests\Feature;

use App\Models\Soundcloud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListenFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_view_listen_index()
    {
        $this->withoutVite();

        $user = User::factory()->create();
        $soundcloud = Soundcloud::create([
            'user_id' => $user->id,
            'title' => 'Test Music',
            'url' => 'https://soundcloud.com/test'
        ]);

        $response = $this->get(route('users.listen.index', $user));

        $response->assertStatus(200);
    }

    public function test_it_can_view_listen_player()
    {
        $this->withoutVite();

        $user = User::factory()->create();
        $soundcloud = Soundcloud::create([
            'user_id' => $user->id,
            'title' => 'Test Music',
            'url' => 'https://soundcloud.com/test'
        ]);

        $response = $this->get(route('users.listen.play', ['user' => $user, 'soundcloud' => $soundcloud]));

        $response->assertStatus(200);
    }

    public function test_it_cannot_view_listen_player_for_other_user_soundcloud()
    {
        $this->withoutVite();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $soundcloud = Soundcloud::create([
            'user_id' => $user2->id,
            'title' => 'Test Music',
            'url' => 'https://soundcloud.com/test'
        ]);

        $response = $this->get(route('users.listen.play', ['user' => $user1, 'soundcloud' => $soundcloud]));

        $response->assertStatus(404);
    }
}
