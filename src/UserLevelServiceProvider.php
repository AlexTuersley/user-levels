<?php

namespace alextuersley\Userlevels\Models;


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
            __DIR__.'/database/' => database_path(''),
        ], 'userlevels-database');
    }
}