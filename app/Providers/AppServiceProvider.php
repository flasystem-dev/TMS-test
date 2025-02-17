<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Google\Cloud\AIPlatform\V1\PredictionServiceClient;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PredictionServiceClient::class, function () {

            $options = [
                'projectId' => env('GOOGLE_CLOUD_PROJECT'),
                'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'),

            ];
            return new PredictionServiceClient($options);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();
    }
}
