<?php

namespace Database\Seeders;

use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\WorkoutItem;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bodyParts = [
            'Chest', 'Back', 'Shoulders', 'Biceps', 'Triceps', 'Forearms',
            'Core', 'Quadriceps', 'Hamstrings', 'Calves', 'Glutes', 'Neck',
            'Traps', 'Lats', 'Lower Back', 'Obliques', 'Abductors', 'Adductors'
        ];

        $workoutItems = [
            'Barbell', 'Dumbbell', 'Kettlebell', 'Cables', 'Smith Machine',
            'Pull-up Bar', 'Bench', 'Resistance Band', 'Bodyweight', 'Machine',
            'Medicine Ball', 'Stability Ball', 'Trap Bar', 'EZ Curl Bar',
            'Leg Press Machine', 'Treadmill', 'Rowing Machine', 'Stationary Bike',
            'Plyo Box', 'Battle Ropes', 'Suspension Trainer (TRX)', 'Sandbag', 'Sled'
        ];

        $createdBodyParts = [];
        foreach ($bodyParts as $part) {
            $createdBodyParts[$part] = BodyPart::firstOrCreate(['name' => $part]);
        }

        $createdWorkoutItems = [];
        foreach ($workoutItems as $item) {
            $createdWorkoutItems[$item] = WorkoutItem::firstOrCreate(['name' => $item]);
        }

        $exercises = [
            // Chest
            'Bench Press' => ['body_parts' => ['Chest', 'Shoulders', 'Triceps'], 'items' => ['Barbell', 'Bench']],
            'Incline Dumbbell Press' => ['body_parts' => ['Chest', 'Shoulders', 'Triceps'], 'items' => ['Dumbbell', 'Bench']],
            'Cable Crossover' => ['body_parts' => ['Chest'], 'items' => ['Cables']],
            'Push-up' => ['body_parts' => ['Chest', 'Shoulders', 'Triceps', 'Core'], 'items' => ['Bodyweight']],
            'Machine Fly' => ['body_parts' => ['Chest'], 'items' => ['Machine']],
            'Dumbbell Pullover' => ['body_parts' => ['Chest', 'Lats'], 'items' => ['Dumbbell', 'Bench']],
            
            // Back
            'Deadlift' => ['body_parts' => ['Back', 'Lower Back', 'Glutes', 'Hamstrings', 'Core', 'Traps'], 'items' => ['Barbell']],
            'Pull-up' => ['body_parts' => ['Back', 'Lats', 'Biceps'], 'items' => ['Bodyweight', 'Pull-up Bar']],
            'Lat Pulldown' => ['body_parts' => ['Back', 'Lats', 'Biceps'], 'items' => ['Machine', 'Cables']],
            'Barbell Row' => ['body_parts' => ['Back', 'Lats', 'Biceps', 'Lower Back'], 'items' => ['Barbell']],
            'Seated Cable Row' => ['body_parts' => ['Back', 'Lats', 'Biceps', 'Traps'], 'items' => ['Cables']],
            'T-Bar Row' => ['body_parts' => ['Back', 'Lats', 'Biceps'], 'items' => ['Barbell']],
            'Single-Arm Dumbbell Row' => ['body_parts' => ['Back', 'Lats', 'Biceps'], 'items' => ['Dumbbell', 'Bench']],
            'Face Pull' => ['body_parts' => ['Shoulders', 'Traps', 'Back'], 'items' => ['Cables']],

            // Shoulders
            'Overhead Press' => ['body_parts' => ['Shoulders', 'Triceps', 'Core'], 'items' => ['Barbell']],
            'Dumbbell Lateral Raise' => ['body_parts' => ['Shoulders'], 'items' => ['Dumbbell']],
            'Front Raise' => ['body_parts' => ['Shoulders'], 'items' => ['Dumbbell']],
            'Reverse Pec Deck Fly' => ['body_parts' => ['Shoulders', 'Back'], 'items' => ['Machine']],
            'Arnold Press' => ['body_parts' => ['Shoulders', 'Triceps'], 'items' => ['Dumbbell', 'Bench']],
            'Upright Row' => ['body_parts' => ['Shoulders', 'Traps', 'Biceps'], 'items' => ['Barbell', 'EZ Curl Bar']],
            'Shrugs' => ['body_parts' => ['Traps', 'Shoulders'], 'items' => ['Dumbbell', 'Barbell', 'Trap Bar']],

            // Biceps
            'Barbell Curl' => ['body_parts' => ['Biceps'], 'items' => ['Barbell']],
            'Dumbbell Hammer Curl' => ['body_parts' => ['Biceps', 'Forearms'], 'items' => ['Dumbbell']],
            'Preacher Curl' => ['body_parts' => ['Biceps'], 'items' => ['Machine', 'EZ Curl Bar']],
            'Incline Dumbbell Curl' => ['body_parts' => ['Biceps'], 'items' => ['Dumbbell', 'Bench']],
            'Concentration Curl' => ['body_parts' => ['Biceps'], 'items' => ['Dumbbell', 'Bench']],
            'Cable Curl' => ['body_parts' => ['Biceps'], 'items' => ['Cables']],

            // Triceps
            'Triceps Pushdown' => ['body_parts' => ['Triceps'], 'items' => ['Cables']],
            'Skull Crushers' => ['body_parts' => ['Triceps'], 'items' => ['EZ Curl Bar', 'Bench']],
            'Overhead Triceps Extension' => ['body_parts' => ['Triceps'], 'items' => ['Dumbbell']],
            'Dips' => ['body_parts' => ['Triceps', 'Chest', 'Shoulders'], 'items' => ['Bodyweight']],
            'Close-Grip Bench Press' => ['body_parts' => ['Triceps', 'Chest', 'Shoulders'], 'items' => ['Barbell', 'Bench']],
            'Triceps Kickback' => ['body_parts' => ['Triceps'], 'items' => ['Dumbbell', 'Bench']],

            // Legs (Quadriceps, Hamstrings, Calves, Glutes)
            'Squat' => ['body_parts' => ['Quadriceps', 'Glutes', 'Hamstrings', 'Core', 'Lower Back'], 'items' => ['Barbell']],
            'Leg Press' => ['body_parts' => ['Quadriceps', 'Glutes', 'Hamstrings'], 'items' => ['Leg Press Machine']],
            'Bulgarian Split Squat' => ['body_parts' => ['Quadriceps', 'Glutes'], 'items' => ['Dumbbell', 'Bench', 'Bodyweight']],
            'Romanian Deadlift' => ['body_parts' => ['Hamstrings', 'Glutes', 'Lower Back'], 'items' => ['Barbell', 'Dumbbell']],
            'Leg Curl' => ['body_parts' => ['Hamstrings'], 'items' => ['Machine']],
            'Leg Extension' => ['body_parts' => ['Quadriceps'], 'items' => ['Machine']],
            'Calf Raise' => ['body_parts' => ['Calves'], 'items' => ['Machine', 'Dumbbell', 'Bodyweight', 'Smith Machine']],
            'Hip Thrust' => ['body_parts' => ['Glutes', 'Hamstrings'], 'items' => ['Barbell', 'Bench', 'Machine']],
            'Lunge' => ['body_parts' => ['Quadriceps', 'Glutes', 'Hamstrings', 'Core'], 'items' => ['Dumbbell', 'Barbell', 'Bodyweight']],
            'Goblet Squat' => ['body_parts' => ['Quadriceps', 'Glutes', 'Core'], 'items' => ['Kettlebell', 'Dumbbell']],
            'Hack Squat' => ['body_parts' => ['Quadriceps', 'Glutes'], 'items' => ['Machine']],
            'Seated Calf Raise' => ['body_parts' => ['Calves'], 'items' => ['Machine']],

            // Core
            'Plank' => ['body_parts' => ['Core', 'Shoulders'], 'items' => ['Bodyweight']],
            'Crunch' => ['body_parts' => ['Core'], 'items' => ['Bodyweight']],
            'Cable Woodchopper' => ['body_parts' => ['Core', 'Obliques'], 'items' => ['Cables']],
            'Hanging Leg Raise' => ['body_parts' => ['Core', 'Obliques', 'Forearms'], 'items' => ['Pull-up Bar', 'Bodyweight']],
            'Russian Twist' => ['body_parts' => ['Core', 'Obliques'], 'items' => ['Medicine Ball', 'Dumbbell', 'Bodyweight']],
            'Ab Wheel Rollout' => ['body_parts' => ['Core', 'Shoulders', 'Lats'], 'items' => ['Bodyweight']],
            'Bicycle Crunch' => ['body_parts' => ['Core', 'Obliques'], 'items' => ['Bodyweight']],
            'Decline Crunch' => ['body_parts' => ['Core'], 'items' => ['Bench', 'Bodyweight']],

            // Forearms
            'Wrist Curl' => ['body_parts' => ['Forearms'], 'items' => ['Barbell', 'Dumbbell', 'Bench']],
            'Reverse Wrist Curl' => ['body_parts' => ['Forearms'], 'items' => ['Barbell', 'Dumbbell', 'Bench']],
            'Farmer\'s Walk' => ['body_parts' => ['Forearms', 'Traps', 'Core', 'Legs'], 'items' => ['Dumbbell', 'Kettlebell', 'Trap Bar']],
            
            // Full Body / Conditioning
            'Kettlebell Swing' => ['body_parts' => ['Glutes', 'Hamstrings', 'Core', 'Lower Back', 'Shoulders'], 'items' => ['Kettlebell']],
            'Burpee' => ['body_parts' => ['Chest', 'Shoulders', 'Triceps', 'Quadriceps', 'Core'], 'items' => ['Bodyweight']],
            'Box Jump' => ['body_parts' => ['Quadriceps', 'Glutes', 'Hamstrings', 'Calves'], 'items' => ['Plyo Box', 'Bodyweight']],
            'Battle Ropes' => ['body_parts' => ['Shoulders', 'Arms', 'Core', 'Back'], 'items' => ['Battle Ropes']],
            'Sled Push' => ['body_parts' => ['Quadriceps', 'Glutes', 'Calves', 'Core'], 'items' => ['Sled']],
            'Sled Pull' => ['body_parts' => ['Hamstrings', 'Back', 'Biceps'], 'items' => ['Sled']],
        ];

        foreach ($exercises as $title => $data) {
            $exercise = Exercise::firstOrCreate(['title' => $title]);

            // Sync Body Parts
            $bodyPartIds = [];
            foreach ($data['body_parts'] as $part) {
                if (isset($createdBodyParts[$part])) {
                    $bodyPartIds[] = $createdBodyParts[$part]->id;
                }
            }
            $exercise->bodyParts()->sync($bodyPartIds);

            // Sync Workout Items
            $itemIds = [];
            foreach ($data['items'] as $item) {
                if (isset($createdWorkoutItems[$item])) {
                    $itemIds[] = $createdWorkoutItems[$item]->id;
                }
            }
            $exercise->workoutItems()->sync($itemIds);
        }
    }
}
