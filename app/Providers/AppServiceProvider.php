<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
		
		/*
          == API ==
        */
        if (!class_exists('ApiObsifight'))
            require base_path('vendor/eywek/obsifight/API/ApiObsifight.class.php');
        $api = new \ApiObsifight(env('DATA_SERVER_TOKEN'), env('DATA_SERVER_ENDPOINT'));
        $this->app->instance('\ApiObsifight', $api);
    }
}
