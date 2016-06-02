<?php namespace TeamTeaTime\Filer;

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
        // Publish migrations, config and seeders
        $this->publishes([
            __DIR__.'/../migrations/' => base_path('/database/migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../config/filer.php' => config_path('filer.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../seeds/' => base_path('/database/seeds')
        ], 'seeds');

        // Routes
        if (config('filer.routes')) {
            $router->group(['prefix' => config('filer.route_prefix'), 'middleware' => 'web'], function ($router) {
                Filer::routes($router);
            });
        }
    }
}
