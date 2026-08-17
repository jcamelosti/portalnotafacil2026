<?php

namespace App\Providers;

//use App\Facades\TecnoSpeed;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*$this->app->bind('tecnospeedclass',function(){
            return new TecnoSpeed();
        });*/
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
