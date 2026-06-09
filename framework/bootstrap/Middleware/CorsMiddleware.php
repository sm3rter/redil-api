<?php

namespace App\Bootstrap\Middleware;

use App\Config\Config;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $config = Config::get('cors', []);
        $allowOrigins = isset($_ENV['CORS_ALLOW_ORIGINS']) ? array_map('trim', explode(',', $_ENV['CORS_ALLOW_ORIGINS'])) : ($config['allow_origins'] ?? ['*']);
        $allowMethods = isset($_ENV['CORS_ALLOW_METHODS']) ? array_map('trim', explode(',', $_ENV['CORS_ALLOW_METHODS'])) : ($config['allow_methods'] ?? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']);
        $allowHeaders = isset($_ENV['CORS_ALLOW_HEADERS']) ? array_map('trim', explode(',', $_ENV['CORS_ALLOW_HEADERS'])) : ($config['allow_headers'] ?? ['Content-Type', 'Authorization', 'Accept', 'X-Requested-With']);
        $allowCredentials = isset($_ENV['CORS_ALLOW_CREDENTIALS']) ? (in_array(strtolower($_ENV['CORS_ALLOW_CREDENTIALS']), ['1', 'true', 'yes'], true) ? 'true' : 'false') : (($config['allow_credentials'] ?? false) ? 'true' : 'false');
        $maxAge = isset($_ENV['CORS_MAX_AGE']) ? (int) $_ENV['CORS_MAX_AGE'] : (int) ($config['max_age'] ?? 600);

        $origin = $request->getHeaderLine('Origin');
        $allowOrigin = in_array('*', $allowOrigins, true) ? '*' : (in_array($origin, $allowOrigins, true) ? $origin : '');

        $response = $handler->handle($request);

        if ($allowOrigin) {
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
                ->withHeader('Access-Control-Allow-Methods', implode(', ', $allowMethods))
                ->withHeader('Access-Control-Allow-Headers', implode(', ', $allowHeaders))
                ->withHeader('Access-Control-Allow-Credentials', $allowCredentials)
                ->withHeader('Access-Control-Max-Age', (string) $maxAge);
        }

        if ($request->getMethod() === 'OPTIONS') {
            $response = $response->withStatus(204);
        }

        return $response;
    }
}
