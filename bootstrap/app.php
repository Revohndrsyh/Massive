<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->booting(function () {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->input('email', '') . '|' . $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'email' => 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam 1 menit.',
                    ]);
                });
        });

        RateLimiter::for('kuesioner', function (Request $request) {
            if ((int) $request->route('step') !== 5) {
                return Limit::none();
            }

            return Limit::perHour(3)
                ->by(auth()->id() ?? $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'kuesioner' => 'Anda sudah menyelesaikan kuesioner 3 kali dalam 1 jam terakhir. Silakan coba lagi nanti.',
                    ]);
        });
});
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->back()->withInput()->withErrors(['email' => 'Sesi telah berakhir. Silakan coba lagi.']);
        });
    })->create();

