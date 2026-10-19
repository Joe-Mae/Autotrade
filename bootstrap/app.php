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
        // Derrière un tunnel (ngrok, proxy) : honorer X-Forwarded-Host / X-Forwarded-Proto
        // pour que les URLs générées (assets, routes, redirections) utilisent le domaine public.
        // En production, remplacer '*' par l'adresse IP du proxy.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
