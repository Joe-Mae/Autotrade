<?php

use App\Http\Middleware\SecurityHeaders;
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
        // Seuls les proxys locaux sont dignes de confiance (ngrok s'exécute sur la machine).
        // Ajouter ici l'IP du reverse proxy si le site est déployé sur un autre serveur.
        $middleware->trustProxies(at: ['127.0.0.1', '::1']);

        // En-têtes de sécurité (CSP, anti-clickjacking, nosniff, HSTS...).
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
