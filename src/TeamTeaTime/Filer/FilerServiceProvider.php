<?php namespace TeamTeaTime\Filer;

use Illuminate\Support\ServiceProvider;

class FilerServiceProvider extends ServiceProvider {

    /**
    * Register the service provider.
    *
    * @return void
    */
    public function register() {}

    /**
    * Bootstrap the application events.
    *
    * @return void
    */
    public function boot()
    {
        // Publish migrations
        $this->publishes([
            __DIR__.'/../../migrations/' => base_path('/database/migrations')
        ], 'migrations');
    }

}
