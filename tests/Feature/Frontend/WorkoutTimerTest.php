<?php

namespace Tests\Feature\Frontend;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutTimerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_timer_screen_with_exercises()
    {
        $user = User::factory()->create();

        $ex1 = Exercise::create(['title' => 'Pushups']);
        $ex2 = Exercise::create(['title' => 'Situps']);

        $response = $this->get(route('users.workout.timer', [
            'user' => $user->id,
            'type' => 'full',
            'count' => 2,
            'timing' => 1,
            'exercises' => "{$ex1->id},{$ex2->id}",
        ]));

        $response->assertStatus(200);

        $response->assertInertia(fn ($page) => $page
            ->component('Workout/Timer')
            ->has('user')
            ->where('workoutType', 'full')
            ->where('exerciseCount', '2')
            ->where('timing', '1')
            ->where('exerciseIds', "{$ex1->id},{$ex2->id}")
        );

        $exercises = $response->viewData('page')['props']['exercises'];

        $this->assertCount(2, $exercises);
        // Verify order is preserved
        $this->assertEquals('Pushups', $exercises[0]['title']);
        $this->assertEquals('Situps', $exercises[1]['title']);
    }

    public function test_timer_handles_empty_exercises_gracefully()
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.workout.timer', [
            'user' => $user->id,
            'type' => 'full',
            'count' => 0,
            'timing' => 1,
            'exercises' => '',
        ]));

        $response->assertStatus(200);

        $response->assertInertia(fn ($page) => $page
            ->component('Workout/Timer')
        );

        $exercises = $response->viewData('page')['props']['exercises'];

        $this->assertCount(0, $exercises);
    }
}
