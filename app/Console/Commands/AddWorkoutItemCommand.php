<?php

namespace App\Console\Commands;

use App\Models\WorkoutItem;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

class AddWorkoutItemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym:add-workout-item';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new workout item (gear) to the gym app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = text(
            label: 'Name of the workout item (gear)?',
            required: true
        );

        $workoutItem = WorkoutItem::create(['name' => $name]);

        $this->info("Workout item '{$workoutItem->name}' created successfully!");
    }
}
