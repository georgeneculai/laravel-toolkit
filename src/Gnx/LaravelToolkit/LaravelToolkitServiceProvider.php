<?php
namespace Gnx\LaravelToolkit;

use Illuminate\Support\ServiceProvider;
use Gnx\LaravelToolkit\Console\Commands\ModelCommand;
use Gnx\LaravelToolkit\Console\Commands\ControllerCommand;

/*
 * This file is part of the LaravelToolkit package by Gnx
 *
 */

class LaravelToolkitServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['command.laravel-toolkit.model'] = $this->app->share(
            function ($app) {
                return new ModelCommand($app['files']);
            }
        );

        $this->app['command.laravel-toolkit.controller'] = $this->app->share(
            function ($app) {
                return new ControllerCommand($app['files']);
            }
        );

        $this->commands('command.laravel-toolkit.model', 'command.laravel-toolkit.controller');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('command.laravel-toolkit.model', 'command.laravel-toolkit.controller');
    }

}
