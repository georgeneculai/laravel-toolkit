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
class ControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-toolkit:controller
                            {controller : Controller name}
                            {--controller= : Controller name}
                            {--dir= : The directory}
                            {--force : Overwrite files if already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Laravel controller within the Controllers folder and with API support';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $controller = $this->option('controller') ? $this->option('controller') : $this->argument('controller');

        echo $controller;
    }
}
