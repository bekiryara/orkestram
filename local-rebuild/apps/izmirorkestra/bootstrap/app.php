<?php

use App\Http\Middleware\ApplyRedirectRules;
use App\Http\Middleware\AdminBasicAuth;
use App\Http\Middleware\EnsureOwnerOwnsResource;
use App\Http\Middleware\RequireAbility;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.basic' => AdminBasicAuth::class,
            'ability' => RequireAbility::class,
            'owner.owns' => EnsureOwnerOwnsResource::class,
        ]);

        $middleware->web(prepend: [
            ApplyRedirectRules::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
