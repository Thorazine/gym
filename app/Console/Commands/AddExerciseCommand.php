<?php

namespace App\Console\Commands;

use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\WorkoutItem;
use Illuminate\Console\Command;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

class AddExerciseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym:add-excersice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new exercise to the gym app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = text(
            label: 'Title of the exercise?',
            required: true
        );

        $workoutItems = WorkoutItem::all()->pluck('name', 'id')->toArray();
        if (empty($workoutItems)) {
            $this->error('No workout items available. Please add some first.');

            return self::FAILURE;
        }

        $bodyParts = BodyPart::all()->pluck('name', 'id')->toArray();
        if (empty($bodyParts)) {
            $this->error('No body parts available. Please add some first.');

            return self::FAILURE;
        }

        $selectedWorkoutItemNames = multiselect(
            label: 'Which workout gear does this require?',
            options: array_values($workoutItems)
        );

        $selectedBodyPartNames = multiselect(
            label: 'Which body parts does this target?',
            options: array_values($bodyParts)
        );

        $exercise = Exercise::create(['title' => $title]);

        $selectedWorkoutItemIds = collect($workoutItems)
            ->filter(fn ($name) => in_array($name, $selectedWorkoutItemNames))
            ->keys()
            ->toArray();

        $selectedBodyPartIds = collect($bodyParts)
            ->filter(fn ($name) => in_array($name, $selectedBodyPartNames))
            ->keys()
            ->toArray();

        $exercise->workoutItems()->sync($selectedWorkoutItemIds);
        $exercise->bodyParts()->sync($selectedBodyPartIds);

        $this->info("Exercise '{$exercise->title}' created successfully!");

        return self::SUCCESS;
    }
}
