<?php

namespace App\Providers;

use App\Omim\OmimClient;
use App\Omim\OmimClientContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
     
        Schema::defaultStringLength(191);

        $this->app->bind(OmimClientContract::class, OmimClient::class);

        Collection::macro('trim', function () {
            return $this->map(function ($value) {
                return trim($value);
            });
        });
    }
}
