<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_logged_in_and_redirected_when_created(): void
    {
        $response = $this->post('/users', [
            'name' => 'John Doe',
        ]);

        $user = User::where('name', 'John Doe')->first();
        $this->assertNotNull($user);

        $response->assertRedirect(route('users.gear.edit', $user));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_is_logged_in_and_redirected_when_selected(): void
    {
        $user = User::factory()->create();

        $response = $this->post("/users/{$user->id}/login");

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}

