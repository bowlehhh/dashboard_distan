<?php

namespace App\Providers;

use App\Models\SystemSetting;
use App\PageRecommendationProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
    public function boot(PageRecommendationProvider $pageRecommendationProvider): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        RateLimiter::for('login', function (Request $request): Limit {
            $email = Str::transliterate($request->string('email')->trim()->lower()->toString());

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('password-email', function (Request $request): Limit {
            $email = Str::transliterate($request->string('email')->trim()->lower()->toString());

            return Limit::perMinute(3)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request): Limit {
            $email = Str::transliterate($request->string('email')->trim()->lower()->toString());

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        View::composer('components.app-layout', function ($view) use ($pageRecommendationProvider): void {
            $view->with('pageRecommendations', $pageRecommendationProvider->forIndexPage(request(), auth()->user()));

            $settings = SystemSetting::query()
                ->whereIn('key', ['appearance', 'profile'])
                ->get(['key', 'value'])
                ->keyBy('key');

            $appearanceSettings = $settings->get('appearance')?->value ?? [];
            $appearanceSettings['density'] = ($appearanceSettings['density'] ?? 'comfortable') === 'compact'
                ? 'bright'
                : ($appearanceSettings['density'] ?? 'comfortable');

            $view->with([
                'institutionProfile' => $settings->get('profile')?->value ?? [],
                'appearanceSettings' => $appearanceSettings,
            ]);
        });
    }
}
