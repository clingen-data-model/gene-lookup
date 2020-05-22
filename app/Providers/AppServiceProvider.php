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
            config(['backpack.base.skin'=>'skin-blue']);
        }
        if ($this->app->environment('local', 'demo')) {
            config(['backpack.base.logo_lg' => '<b>ClinGen</b> - '.$this->app->environment()]);
        }

        if (config('app.url_scheme')) {
            URL::forceScheme('http');
        }

        $this->app->bind(OmimClientContract::class, OmimClient::class);

        Collection::macro('trim', function () {
            return $this->map(function ($value) {
                return trim($value);
            });
        });
    }
}
