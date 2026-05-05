<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkoutItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_only_first_name_and_generates_random_email_and_password(): void
    {
        $user = User::create([
            'name' => 'John',
        ]);

        $this->assertEquals('John', $user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
        $this->assertStringEndsWith('@example.com', $user->email);
    }

    public function test_user_workout_items_relationship(): void
    {
        $user = User::create(['name' => 'Jane']);
        $item1 = WorkoutItem::create(['name' => 'Dumbbells']);
        $item2 = WorkoutItem::create(['name' => 'Bench']);

        $user->workoutItems()->attach([$item1->id, $item2->id]);

        $this->assertCount(2, $user->workoutItems);
        $this->assertTrue($user->workoutItems->contains($item1));
        $this->assertTrue($user->workoutItems->contains($item2));
    }
}
