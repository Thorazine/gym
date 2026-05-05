<?php

namespace Tests\Feature\Frontend;

use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_count_selection_screen()
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.workout.count', ['user' => $user->id, 'type' => 'full']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Workout/Count')
            ->has('user')
            ->where('workoutType', 'full')
        );
    }

    public function test_user_can_view_timing_selection_screen()
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.workout.timing', [
            'user' => $user->id,
            'type' => 'full',
            'count' => 5,
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Workout/Timing')
            ->has('user')
            ->where('workoutType', 'full')
            ->where('exerciseCount', '5')
        );
    }

    public function test_setup_screen_filters_exercises_by_gear()
    {
        $user = User::factory()->create();

        $gearUserHas = WorkoutItem::create(['name' => 'Dumbbells']);
        $gearUserDoesntHave = WorkoutItem::create(['name' => 'Barbell']);

        $user->workoutItems()->attach($gearUserHas);

        // Exercise 1 requires no gear
        $ex1 = Exercise::create(['title' => 'Pushups']);

        // Exercise 2 requires gear the user HAS
        $ex2 = Exercise::create(['title' => 'Dumbbell Curls']);
        $ex2->workoutItems()->attach($gearUserHas);

        // Exercise 3 requires gear the user DOES NOT HAVE
        $ex3 = Exercise::create(['title' => 'Barbell Squats']);
        $ex3->workoutItems()->attach($gearUserDoesntHave);

        // Setup screen should return ex1 and ex2, but NOT ex3
        $response = $this->get(route('users.workout.setup', [
            'user' => $user->id,
            'type' => 'full',
            'count' => 5,
            'timing' => 1,
        ]));

        $response->assertStatus(200);

        // Check the exercises prop array
        $exercises = $response->viewData('page')['props']['exercises'];

        $this->assertCount(2, $exercises);
        $titles = collect($exercises)->pluck('title')->toArray();
        $this->assertContains('Pushups', $titles);
        $this->assertContains('Dumbbell Curls', $titles);
        $this->assertNotContains('Barbell Squats', $titles);
    }

    public function test_setup_screen_filters_exercises_by_body_part_type()
    {
        $user = User::factory()->create();

        $upperPart = BodyPart::create(['name' => 'Upper Body']);
        $lowerPart = BodyPart::create(['name' => 'Lower Body']);

        $exUpper = Exercise::create(['title' => 'Bench Press']);
        $exUpper->bodyParts()->attach($upperPart);

        $exLower = Exercise::create(['title' => 'Squats']);
        $exLower->bodyParts()->attach($lowerPart);

        $response = $this->get(route('users.workout.setup', [
            'user' => $user->id,
            'type' => 'upper', // Requesting upper body
            'count' => 5,
            'timing' => 1,
        ]));

        $response->assertStatus(200);

        $exercises = $response->viewData('page')['props']['exercises'];
        $this->assertCount(1, $exercises);
        $this->assertEquals('Bench Press', $exercises[0]['title']);
    }

    public function test_setup_screen_limits_count()
    {
        $user = User::factory()->create();

        // Create 10 exercises
        for ($i = 0; $i < 10; $i++) {
            Exercise::create(['title' => "Ex $i"]);
        }

        $response = $this->get(route('users.workout.setup', [
            'user' => $user->id,
            'type' => 'full',
            'count' => 3, // Only request 3
            'timing' => 1,
        ]));

        $response->assertStatus(200);
        $exercises = $response->viewData('page')['props']['exercises'];

        $this->assertCount(3, $exercises);
    }
}
