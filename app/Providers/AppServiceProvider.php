<?php

namespace App\Providers;

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
        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            // Di belakang Cloudflare Tunnel + Nginx, PHP menerima request sebagai HTTP biasa,
            // sehingga redirect() (mis. setelah login) menghasilkan http://... dan diblokir
            // browser sebagai mixed content pada navigasi XHR Inertia. Tandai request sebagai HTTPS.
            if (! $this->app->runningInConsole()) {
                request()->server->set('HTTPS', 'on');
            }
        }
    }
}
