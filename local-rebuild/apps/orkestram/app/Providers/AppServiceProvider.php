<?php

namespace App\Providers;

use App\Services\Ui\NavigationFactory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        View::composer(['frontend.*', 'portal.*', 'auth.*', 'admin.*'], function ($view): void {
            $request = request();
            $identity = (array) $request->session()->get('admin_identity', []);
            $authenticated = isset($identity['user']) && (string) $identity['user'] !== '';
            $role = isset($identity['role']) ? (string) $identity['role'] : null;
            $siteMeta = $this->resolveSiteMeta($request);

            $zone = 'public';

            $nav = app(NavigationFactory::class)->build($zone, $authenticated, $role);

            $view->with('shellIdentity', $identity);
            $view->with('shellNav', $nav);
            $view->with('shellAuthenticated', $authenticated);
            $view->with('shellSiteMeta', $siteMeta);
        });
    }

    private function resolveSiteMeta(Request $request): array
    {
        $themes = (array) config('site_themes.sites', []);
        $default = (array) config('site_themes.default', [
            'name' => 'Orkestram',
            'theme' => 'orkestram',
        ]);

        $host = strtolower((string) $request->getHost());
        $httpHost = strtolower((string) $request->getHttpHost());
        $site = str_contains($httpHost, ':8181') || str_contains($host, 'izmirorkestra')
            ? 'izmirorkestra.net'
            : 'orkestram.net';

        $siteMeta = $themes[$site] ?? [];
        if (!is_array($siteMeta)) {
            $siteMeta = [];
        }

        return array_merge($default, $siteMeta);
    }
}
