<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class GymMenuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Main menu for managing gym app content';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = select(
            label: 'What would you like to do?',
            options: [
                'gym:add-body-parts' => 'Add Body Part',
                'gym:add-workout-item' => 'Add Workout Item (Gear)',
                'gym:add-excersice' => 'Add Exercise',
                'exit' => 'Exit',
            ]
        );

        if ($action !== 'exit') {
            $this->call($action);
        } else {
            $this->info('Goodbye!');
        }
    }
}
