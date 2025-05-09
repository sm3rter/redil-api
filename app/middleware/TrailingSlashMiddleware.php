<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class TrailingSlashMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        // If the path ends with a slash and is not just "/"
        if ($path !== '/' && substr($path, -1) === '/') {
            // Remove trailing slash
            $path = rtrim($path, '/');
            
            // Create new URI with the modified path
            $newUri = $uri->withPath($path);
            
            // Create new request with the modified URI
            $request = $request->withUri($newUri);
        }
        
        return $handler->handle($request);
    }
} 