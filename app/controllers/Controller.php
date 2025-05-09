<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class Controller
{
    protected function response(Response $response, array $data = [], array $errors = [], int $status = 200): Response
    {
        $responseData = [
            'data' => $data,
            'errors' => $errors
        ];

        $response->getBody()->write(json_encode($responseData));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
} 