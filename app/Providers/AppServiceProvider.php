<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
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
        RateLimiter::for('login', function (Request $request): Limit {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('password-email', function (Request $request): Limit {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(3)->by($email.'|'.$request->ip());
        });

        View::composer('components.app-layout', function ($view): void {
            if (! Schema::hasTable('system_settings')) {
                return;
            }

            $appearanceSettings = SystemSetting::query()->where('key', 'appearance')->value('value') ?? [];
            $appearanceSettings['density'] = ($appearanceSettings['density'] ?? 'comfortable') === 'compact'
                ? 'bright'
                : ($appearanceSettings['density'] ?? 'comfortable');

            $view->with([
                'institutionProfile' => SystemSetting::query()->where('key', 'profile')->value('value') ?? [],
                'appearanceSettings' => $appearanceSettings,
            ]);
        });
    }
}
