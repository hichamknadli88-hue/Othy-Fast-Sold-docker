<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
    if (env('APP_ENV') === 'production') {
        URL::forceScheme('https');
    }
    Blade::if('admin', function () {
        return Auth::check() && Auth::user()->isAdmin();
    });

    RateLimiter::for('recharge', function (Request $request) {
        $tooMany = function (Request $request, array $headers) {
            $minutes = (int) ceil(((int) ($headers['Retry-After'] ?? 60)) / 60);

            return back()
                ->with('error', "محاولات كثيرة جداً. حاول مرة أخرى بعد {$minutes} دقيقة.")
                ->withHeaders($headers);
        };

        $ip = $request->ip();

        $limits = [
            Limit::perMinute(5)->by('min:ip:' . $ip)->response($tooMany),
            Limit::perHour(20)->by('hour:ip:' . $ip)->response($tooMany),
        ];

        // Logged-in users also get a per-account limit, so changing IP
        // (VPN, mobile data) doesn't reset their quota.
        if ($user = $request->user()) {
            $limits[] = Limit::perHour(15)
                ->by('hour:user:' . strtolower($user->email))
                ->response($tooMany);
        }

        return $limits;
    });
}
}
