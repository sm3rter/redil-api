<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Controller
{
    protected function successResponse(Response $response, $data = null, string $message = 'Success', int $status = 200): Response
    {
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    protected function errorResponse(Response $response, string $message, int $status = 400): Response
    {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $message
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
} 