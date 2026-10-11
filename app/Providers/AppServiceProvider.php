<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        $trustedProxies = config('app.trusted_proxies');

        if (is_string($trustedProxies) && $trustedProxies !== '') {
            TrustProxies::at($trustedProxies);
        }

        View::composer([
            'layouts.studio',
            'livewire.studio.activity-composer',
            'livewire.studio.news-composer',
        ], function ($view): void {
            $settings = Schema::hasTable('site_settings')
                ? SiteSetting::query()->with('logoMedia')->first()
                : null;

            $view->with('siteSettings', $settings ?? new SiteSetting(SiteSetting::DEFAULTS));
        });
    }
}
