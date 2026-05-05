<?php

namespace Tests\Feature\Frontend;

use App\Models\User;
use App\Models\WorkoutItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_screen_displays_users()
    {
        User::factory()->create(['name' => 'John Gym']);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        // Inertia testing
        $response->assertInertia(fn ($page) => $page
            ->component('Users/Index')
            ->has('users', 1)
        );
    }

    public function test_can_create_new_user_and_redirect_to_gear_selection()
    {
        $response = $this->post(route('users.store'), [
            'name' => 'Alice',
        ]);

        $this->assertDatabaseHas('users', ['name' => 'Alice']);

        $user = User::where('name', 'Alice')->first();
        $response->assertRedirect(route('users.gear.edit', $user));
    }

    public function test_user_can_view_gear_selection()
    {
        $user = User::factory()->create();
        WorkoutItem::create(['name' => 'Dumbbells']);

        $response = $this->get(route('users.gear.edit', $user));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Users/Gear')
            ->has('allGear', 1)
            ->has('user')
        );
    }

    public function test_user_can_save_gear_and_redirect_to_home()
    {
        $user = User::factory()->create();
        $gear1 = WorkoutItem::create(['name' => 'Dumbbells']);
        $gear2 = WorkoutItem::create(['name' => 'Kettlebells']);

        $response = $this->put(route('users.gear.update', $user), [
            'gear_ids' => [$gear1->id, $gear2->id],
        ]);

        $this->assertCount(2, $user->fresh()->workoutItems);
        $response->assertRedirect(route('home'));
    }

    public function test_user_can_view_workout_type_selection()
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.type.index', $user));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Workout/Type')
            ->has('user')
        );
    }
}
