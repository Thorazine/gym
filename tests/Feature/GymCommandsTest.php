<?php

namespace Tests\Feature;

use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\WorkoutItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GymCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_body_parts_command(): void
    {
        $this->artisan('gym:add-body-parts')
            ->expectsQuestion('Name of the body part?', 'Chest')
            ->assertSuccessful();

        $this->assertDatabaseHas('body_parts', [
            'name' => 'Chest',
        ]);
    }

    public function test_add_workout_item_command(): void
    {
        $this->artisan('gym:add-workout-item')
            ->expectsQuestion('Name of the workout item (gear)?', 'Dumbbells')
            ->assertSuccessful();

        $this->assertDatabaseHas('workout_items', [
            'name' => 'Dumbbells',
        ]);
    }

    public function test_add_exercise_command(): void
    {
        $bodyPart = BodyPart::create(['name' => 'Chest']);
        $workoutItem = WorkoutItem::create(['name' => 'Bench']);

        $this->artisan('gym:add-excersice')
            ->expectsQuestion('Title of the exercise?', 'Bench Press')
            ->expectsChoice('Which workout gear does this require?', [$workoutItem->name], [$workoutItem->name])
            ->expectsChoice('Which body parts does this target?', [$bodyPart->name], [$bodyPart->name])
            ->assertSuccessful();

        $this->assertDatabaseHas('exercises', [
            'title' => 'Bench Press',
        ]);

        $exercise = Exercise::where('title', 'Bench Press')->first();
        $this->assertCount(1, $exercise->workoutItems);
        $this->assertCount(1, $exercise->bodyParts);
        $this->assertEquals('Bench', $exercise->workoutItems->first()->name);
        $this->assertEquals('Chest', $exercise->bodyParts->first()->name);
    }
}
