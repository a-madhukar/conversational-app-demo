<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use App\Services\HttpClient\Http; 
use Illuminate\Support\ServiceProvider;
use App\Services\HttpClient\GuzzleImplementation as GuzzleImpl; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(Http::class, GuzzleImpl::class); 
    }
}
