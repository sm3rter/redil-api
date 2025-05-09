<?php

namespace App\Facades;

use Psr\Http\Message\ResponseInterface;

class Response extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ResponseInterface::class;
    }

    public static function json($data = [], array $errors = [], int $status = 200)
    {
        $responseData = [
            'data' => $data,
            'errors' => $errors
        ];

        $response = static::getFacadeRoot();
        $response->getBody()->write(json_encode($responseData));
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public static function success($data = [], string $message = '', int $status = 200)
    {
        return static::json($data, [], $status);
    }

    public static function error($message, int $status = 400)
    {
        return static::json([], ['message' => $message], $status);
    }

    public static function notFound($message = 'Resource not found')
    {
        return static::error($message, 404);
    }

    public static function validationError(array $errors)
    {
        return static::json([], $errors, 422);
    }
} 