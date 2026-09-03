<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

        // PayPal's IPN is a server-to-server POST: there is no browser, no session and no
        // CSRF token, so with the route sitting in the web group every notification was
        // rejected with a 419 and silently lost.
        $middleware->preventRequestForgery(except: [
            'notify/paypal',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Anything submitted from a page left open past SESSION_LIFETIME (2h) fails CSRF and
        // used to render Laravel's bare "419 | Page Expired" screen — worst on the public scan
        // form, where a long set of answers was simply lost.
        //
        // This has to be keyed off the *prepared* exception. Handler::render() calls
        // prepareException() BEFORE renderViaCallbacks(), and that maps
        // TokenMismatchException to HttpException(419) — so a callback type-hinted on
        // TokenMismatchException can never match, which is why the previous version of this
        // handler never fired. The original is still reachable via getPrevious().
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($e->getStatusCode() !== 419
                || ! $e->getPrevious() instanceof TokenMismatchException) {
                return null;
            }

            // Livewire shows its own "this page has expired" prompt and reloads, and API
            // clients want the status code. Only plain form posts are rewritten.
            if ($request->expectsJson() || $request->hasHeader('X-Livewire')) {
                return null;
            }

            $message = 'Your session expired for security. Please try again.';

            // 'form' is the key the marketing pages and the scan page already display.
            if ($request->is('portal-login')) {
                // The portal login is a modal on the marketing home, not its own page.
                return redirect()
                    ->route('marketing.home')
                    ->withErrors(['form' => $message]);
            }

            return back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation']))
                ->withErrors(['form' => $message]);
        });
    })->create();
