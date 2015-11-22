<?php
namespace Gnx\LaravelToolkit\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * A command to generate autocomplete information for your IDE
 * This file is part of the LaravelToolkit package by Gnx
 *
 */
class ModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-toolkit:model
                            {model : Model name singular (will assume table is plural}
                            {--model= : Model name singular (will assume table is plural}
                            {--dir= : The directory}
                            {--force : Overwrite files if already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Laravel model within the Models folder and with revision support';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = $this->option('model') ? $this->option('model') : $this->argument('model');

        echo $model;
    }
}
