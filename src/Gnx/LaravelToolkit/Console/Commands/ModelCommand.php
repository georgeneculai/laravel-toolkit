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
                            {table? : Table name (optional)}
                            {--model= : Model name singular (will assume table is plural}
                            {--table= : Table name}
                            {--dir= : The directory}
                            {--no-migration : Do not create a migration file}
                            {--run-migration : Run the migration file}';

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

                $path = $this->laravel->databasePath().'/migrations/' . date('Y_m_d_His') . '_create_' . $table . '_table.php';
                $stub = $this->buildMigration($table);

                $this->files->put($path, $stub);
                $file = pathinfo($path, PATHINFO_FILENAME);
                $this->line("<info>Created Migration:</info> $file");

                //$this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
                if ($this->option('run-migration')) {
                    $this->call('migrate');
                }
            }
        }
    }

    /**
     * Get the stub directory
     *
     * @return string
     */
    public function getStubDir() {
        return __DIR__.'/../stubs';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->getStubDir() . '/model.stub';
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
        if (strlen($dir) && !Str::endsWith($dir, DIRECTORY_SEPARATOR)) {
            $dir .= DIRECTORY_SEPARATOR;
        }

        return (strlen($dir) ? $dir : 'Models/') . $model;
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
        return $this->replaceTable($stub);
    }

    /**
     * Build the migration class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildMigration($table)
    {
        $stub = $this->files->get($this->getStubDir() . '/create.stub');

        $stub = $this->replaceClass($stub, Str::studly('create ' . $table . ' table'));
        return $this->replaceTable($stub, $table);
    }

    /**
     * Replace the table name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $table
     * @return string
     */
    protected function replaceTable($stub, $table = null)
    {
        if ($table === null) {
            $table = $this->getTableInput();
        }

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
