<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Behind a proxy / load balancer (nginx, AWS ELB) the app otherwise sees the
        // proxy's private IP, so scan geolocation (IpGeolocationService) skips it and no
        // country is recorded. Trust the forwarding headers so $request->ip() resolves to
        // the real visitor IP.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_AWS_ELB,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // A login submitted from a stale tab (expired session/CSRF token) threw a 419
        // "Page Expired" error page. Send the visitor back to the login instead so they
        // can simply try again.
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            if ($request->is('portal-login')) {
                return redirect()
                    ->route('marketing.home')
                    ->withErrors(['portal_login' => 'Your session expired. Please log in again.']);
            }

            return null;
        });
    })->create();
