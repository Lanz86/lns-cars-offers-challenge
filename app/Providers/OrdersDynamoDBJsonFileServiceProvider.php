<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class OrdersDynamoDBJsonFileServiceProvider extends ServiceProvider
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
        $this->app->bind("App\Services\OrdersServicesInterface", "App\Services\OrdersDynamoDBJsonFileService");
    }


}
