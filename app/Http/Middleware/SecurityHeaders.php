<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajoute les en-têtes de sécurité HTTP à toutes les réponses.
 * La CSP autorise le CDN Bootstrap et les Google Fonts utilisés par le site.
 */
class SecurityHeaders
{
    private const CSP = "default-src 'self'; "
        ."script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; "
        ."style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; "
        ."font-src 'self' data: https://fonts.gstatic.com; "
        ."img-src 'self' data: https:; "
        ."connect-src 'self'; "
        ."object-src 'none'; "
        ."base-uri 'self'; "
        ."form-action 'self'; "
        ."frame-ancestors 'self'";

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $headers->set('Content-Security-Policy', self::CSP);
        $headers->remove('X-Powered-By');

        // HSTS uniquement lorsque la requête arrive réellement en HTTPS.
        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        // PHP ajoute X-Powered-By au niveau du serveur : on le retire aussi ici.
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        return $response;
    }
}
