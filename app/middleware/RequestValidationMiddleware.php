<?php

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;

class RequestValidationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');

        // Only validate if content-type is JSON and body is present
        if (stripos($contentType, 'application/json') !== false) {
            $body = $request->getParsedBody();
            // Only reject if we have a body but it's not an array (failed to parse)
            if ($body !== null && !is_array($body) && !is_object($body)) {
                $resp = new \Slim\Psr7\Response(400);
                $resp->getBody()->write(json_encode(['errors' => ['message' => 'Invalid JSON']]));
                return $resp->withHeader('Content-Type', 'application/json');
            }
        }

        return $handler->handle($request);
    }
}
