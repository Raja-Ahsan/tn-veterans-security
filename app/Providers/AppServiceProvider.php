<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        try {
            $settings = SiteSetting::first();
            if ($settings?->email) {
                Config::set('mail.from.address', $settings->email);
                Config::set('mail.from.name', config('app.name'));
            }
        } catch (\Throwable) {
            // Database may not be ready during install/migrate
        }
    }
}
