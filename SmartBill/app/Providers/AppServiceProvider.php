<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Slip;
use App\Observers\UserObserver;
use App\Observers\MerchantObserver;
use App\Observers\SlipObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
        // Set global password defaults
        Password::defaults(function () {
            return Password::min(2)->max(30);
        });
        // Register Observers
        User::observe(UserObserver::class);
        Merchant::observe(MerchantObserver::class);
        Slip::observe(SlipObserver::class);

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
