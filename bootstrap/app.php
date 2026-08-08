<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
        ->withProviders([App\\Providers\\AppServiceProvider::class])
    ->withMiddleware(function (Middleware $middleware) {
        // Paystack cannot send a CSRF token, so its webhook endpoint is exempt.
        // It is still authenticated via HMAC signature check inside the controller —
        // see PaystackService::isValidWebhookSignature().
        $middleware->validateCsrfTokens(except: [
            'webhooks/paystack',
        ]);

        $middleware->alias([
            // Deliberately just the class name here, with no ':admin' baked in — middleware
            // parameter syntax (name:param) is parsed on the string passed to ->middleware()
            // at the ROUTE, before alias resolution runs. Baking ':admin' into the alias
            // mapping itself would make Laravel try to resolve a class literally named
            // "...RedirectIfNotAdmin:admin", which doesn't exist, and crash every admin
            // route with a 500. The guard parameter is applied at the route instead — see
            // routes/web.php, which uses 'auth.admin:admin'.
            'auth.admin' => \App\Http\Middleware\RedirectIfNotAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

