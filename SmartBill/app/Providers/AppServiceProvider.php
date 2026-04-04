<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        if ($this->app->bound('request')) {
            config([
                'session.domain' => $this->resolveSessionDomain(request()->getHost()),
            ]);
        }

        View::composer('*', function ($view) {
            $user = Auth::user();
            $workspaceStores = once(function () use ($user) {
                return $user ? $user->accessibleMerchants()->get() : collect();
            });

            $view->with('workspaceSwitcherStores', $workspaceStores);
            $view->with('workspaceSwitcherBaseDomain', env('APP_DOMAIN', 'localhost'));
        });
    }

    private function resolveSessionDomain(?string $host): ?string
    {
        $configuredDomain = trim((string) env('SESSION_DOMAIN', ''));

        if (!$host) {
            return $configuredDomain !== '' && strtolower($configuredDomain) !== 'null'
                ? $configuredDomain
                : null;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return null;
        }

        if ($host === 'localhost' || Str::endsWith($host, '.localhost')) {
            return '.localhost';
        }

        if ($configuredDomain !== '' && strtolower($configuredDomain) !== 'null') {
            return $configuredDomain;
        }

        return null;
    }
}
