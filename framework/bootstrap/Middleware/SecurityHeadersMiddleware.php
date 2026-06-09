<?php

namespace App\Bootstrap\Middleware;

use App\Config\Config;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SecurityHeadersMiddleware implements MiddlewareInterface
{
    /**
     * Add security headers to all responses
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $security = Config::get('security', []);
        $response = $handler->handle($request);

        if ($response->hasHeader('X-Powered-By')) {
            $response = $response->withoutHeader('X-Powered-By');
        }

        $response = $response
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block')
            ->withHeader('Referrer-Policy', $security['referrer_policy'] ?? 'strict-origin-when-cross-origin')
            ->withHeader('Permissions-Policy', $security['permissions_policy'] ?? 'geolocation=(), microphone=(), camera=()')
            ->withHeader('Content-Security-Policy', $security['csp'] ?? "default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none';");

        if (!empty($security['hsts']['enabled'])) {
            $hsts = 'max-age=' . ($security['hsts']['max_age'] ?? 31536000);
            if (!empty($security['hsts']['include_sub_domains'])) {
                $hsts .= '; includeSubDomains';
            }
            if (!empty($security['hsts']['preload'])) {
                $hsts .= '; preload';
            }
            $response = $response->withHeader('Strict-Transport-Security', $hsts);
        }

        return $response;
    }
}
