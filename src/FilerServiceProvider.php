<?php

namespace TeamTeaTime\Filer;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class FilerServiceProvider extends ServiceProvider
{

    /**
    * Register the service provider.
    *
    * @return void
    */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filer.php', 'filer');
    }

    /**
    * Bootstrap the application events.
    *
    * @param  Router  $router
    * @return void
    */
    public function boot(Router $router)
    {
        // Publish migrations and config
        $this->publishes([
            __DIR__.'/../migrations/' => base_path('/database/migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../config/filer.php' => config_path('filer.php')
        ], 'config');

        // Routes
        $router->group(['namespace' => 'TeamTeaTime\Filer\Controllers', 'prefix' => 'files'], function ($router) {
            $router->get('{id}/download', [
                'as'    => 'filer.file.download',
                'uses'  => 'LocalFileController@download'
            ]);
        });
    }

}
