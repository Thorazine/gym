<?php

namespace App\Console\Commands;

use App\Models\BodyPart;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

class AddBodyPartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym:add-body-parts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new body part to the gym app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = text(
            label: 'Name of the body part?',
            required: true
        );

        $bodyPart = BodyPart::create(['name' => $name]);

        $this->info("Body part '{$bodyPart->name}' created successfully!");
    }
}
