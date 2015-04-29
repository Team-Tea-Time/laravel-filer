<?php namespace TeamTeaTime\Filer;

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
        $this->mergeConfigFrom(__DIR__.'/../../config/filer.php', 'filer');
    }

    /**
    * Bootstrap the application events.
    *
    * @return void
    */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../migrations/' => base_path('/database/migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../../config/filer.php' => config_path('filer.php')
        ], 'config');
    }

}
