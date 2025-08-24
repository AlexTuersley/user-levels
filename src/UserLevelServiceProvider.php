<?php

namespace App;

use Illuminate\Support\ServiceProvider;

class UserLevelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
          $this->publishes([
            __DIR__.'/../database' => database_path(''),
        ], 'userlevels-database');
        $this->publishes([
            __DIR__.'/../Models' => '', base_path('app/Models')
        ], 'userlevels-models');
        $this->publishes([
            __DIR__.'/../Helpers' => '', base_path('app/Helpers')
        ], 'userlevels-helpers');
        $this->publishes([
            __DIR__.'/../Services' => '', base_path('app/Services')
        ], 'userlevels-services');
           $this->publishes([
            __DIR__.'/../Services' => '', base_path('app/Traits')
        ], 'userlevels-traits');
    }
}