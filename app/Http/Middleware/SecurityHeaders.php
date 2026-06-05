<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent browser from guessing content type (MIME-type sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking by disallowing embedding in iframes (except same origin)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Enable XSS filter in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy — send origin only on cross-origin requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy — restrict browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy
        $viteOrigins = app()->isLocal()
            ? "http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173"
            : '';

        $csp = implode('; ', [
            "default-src 'self'",
            trim("script-src 'self' 'unsafe-inline' 'unsafe-eval' {$viteOrigins} https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://www.youtube.com"),
            trim("style-src 'self' 'unsafe-inline' {$viteOrigins} https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://fonts.googleapis.com"),
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "frame-src https://www.youtube.com https://www.youtube-nocookie.com",
            "img-src 'self' data: https://img.youtube.com https://i.ytimg.com https://ui-avatars.com",
            trim("connect-src 'self' {$viteOrigins} http://localhost:5000 http://127.0.0.1:5000 https://cdn.jsdelivr.net"),
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // Remove server identification headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
