<?php
namespace Gnx\LaravelToolkit\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

/**
 * A command to generate autocomplete information for your IDE
 * This file is part of the LaravelToolkit package by Gnx
 *
 */
class ModelCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-toolkit:model
                            {model : Model name singular (will assume table is plural)}
                            {table? : Table name }
                            {--model= : Model name singular (will assume table is plural}
                            {--table= : Table name}
                            {--dir= : The directory}
                            {--no-migration : Do not create a migration file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Laravel model within the Models folder and with revision support';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (parent::fire() !== false) {
            if (!$this->option('no-migration')) {
                $table = $this->getTableInput();
                $this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
            }
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/model.stub';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $model = $this->option('model') ? $this->option('model') : $this->argument('model');
        $dir = $this->option('dir');

        return ($dir ? $dir : 'Models/') . $model;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)
                ->replaceClass($stub, $name);
        return $this->replaceTable($stub, $name);
    }

    /**
     * Replace the table name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceTable($stub, $name)
    {
        $table = $this->getTableInput();

        return str_replace('DummyTable', $table, $stub);
    }

    /**
     * Get the desired table name from the input.
     *
     * @return string
     */
    protected function getTableInput()
    {
        $table = $this->option('table') ? $this->option('table') : $this->argument('table');
        if (!$table) {
            $table = Str::plural(Str::snake(class_basename($this->getNameInput())));
        }

        return $table;
    }
}
