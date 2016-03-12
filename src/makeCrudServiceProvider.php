<?php

namespace dreamingincode\makeCrud;

use Illuminate\Support\ServiceProvider;

class makeCrudServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('makeCrud',function($app){
            return new MakeCrud($app);
        });
    }
}