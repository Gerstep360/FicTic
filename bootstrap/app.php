<?php

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

        // Soporta v6 (namespace singular) y versiones antiguas (plural)
        $aliases = [];

        if (class_exists(\Spatie\Permission\Middleware\PermissionMiddleware::class)) {
            // v6.x
            $aliases['role']               = \Spatie\Permission\Middleware\RoleMiddleware::class;
            $aliases['permission']         = \Spatie\Permission\Middleware\PermissionMiddleware::class;
            $aliases['role_or_permission'] = \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class;
        } else {
            // fallback v5.x
            $aliases['role']               = \Spatie\Permission\Middlewares\RoleMiddleware::class;
            $aliases['permission']         = \Spatie\Permission\Middlewares\PermissionMiddleware::class;
            $aliases['role_or_permission'] = \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class;
        }
        $aliases['ambito'] = \App\Http\Middleware\CheckAmbito::class;
        $middleware->alias($aliases);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
