<?php
namespace Gnx\LaravelToolkit\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Config;

/**
 * A command to generate autocomplete information for your IDE
 * This file is part of the LaravelToolkit package by Gnx
 *
 */
class ControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-toolkit:controller
                            {controller : Controller name}
                            {model : Model name}
                            {modelns? : Model name space (optional)}
                            {--controller= : Controller name}
                            {--model= : Model name}
                            {--modelns= : Model name space}
                            {--dir= : The directory}
                            {--no-routes : Do not append the routes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Laravel controller with API support';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (parent::fire() !== false) {
            if (!$this->option('no-routes')) {
                $path = Config::get('generator.path_routes', app_path('Http/routes.php'));
                $path = Config::get('generator.path_routes', app_path('Http/routes.php'));
                $content = $this->files->get($path);
                $name = $this->parseName($this->getNameInput());
                $content .= $this->buildRoute($name);
                $this->files->put($path, $content);
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
        return $this->getStubDir() . '/controller.stub';
    }
    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $controller = $this->option('controller') ? $this->option('controller') : $this->argument('controller');

        $dir = $this->option('dir');
        if (strlen($dir) && !Str::endsWith($dir, DIRECTORY_SEPARATOR)) {
            $dir .= DIRECTORY_SEPARATOR;
        }

        return ($dir ? $dir : '') . $controller;
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

        $stub = $this->replaceModelNs($stub);
        $stub = $this->replaceModel($stub);

        return $stub;
    }

    /**
     * Build the route with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildRoute($name)
    {
        $stub = $this->files->get($this->getStubDir() . '/routes.stub');

        $stub = $this->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);

        $path = strtolower($this->getNameInput());
        if (Str::endsWith($path, 'controller')) {
            $path = substr($path, 0, strlen($path) - strlen('controller'));
        }
        $stub = str_replace('DummyPath', $path, $stub);

        return $stub;
    }

    /**
     * Replace the model name for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function replaceModelNs($stub)
    {
        $modelns = $this->option('modelns') ? $this->option('modelns') : $this->argument('modelns');


        return str_replace('DummyModelNamespace', strlen($modelns) ? $modelns : ($this->laravel->getNamespace() . 'Models'), $stub);
    }

    /**
     * Replace the model name for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function replaceModel($stub)
    {
        $model = $this->option('model') ? $this->option('model') : $this->argument('model');

        return str_replace('DummyModel', $model, $stub);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
    }
}
