<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('kuesioner', function (Request $request) {
            if ((int) $request->route('step') !== 5) {
                return Limit::none();
            }

            return Limit::perHour(3)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'throttle' => 'Anda sudah menyelesaikan kuesioner 3 kali dalam 1 jam terakhir. Silakan coba lagi nanti.',
                    ]);
                });
        });
    }
}