<?php

namespace App\Bootstrap;

use Framework\Logger\Logger;
use Slim\Exception\HttpException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpUnauthorizedException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class ErrorHandler
{
    // Human-readable titles for Slim HTTP exceptions
    private static array $httpExceptions = [
        HttpNotFoundException::class            => [404, 'Route was not found'],
        HttpForbiddenException::class           => [403, 'Access denied'],
        HttpUnauthorizedException::class        => [401, 'Unauthorized'],
        HttpMethodNotAllowedException::class    => [405, 'Method not allowed'],
        HttpInternalServerErrorException::class => [500, 'Internal Server Error'],
    ];

    public static function setup(): void
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (!(error_reporting() & $errno)) {
                return;
            }

            $exception = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
            Logger::exception($exception, Logger::WARNING);
            throw $exception;
        });

        set_exception_handler(function ($exception) {
            self::handleException($exception);
        });
    }

    // Called by Slim's error middleware
    public static function slimHandler(): callable
    {
        return function (
            ServerRequestInterface $request,
            \Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails
        ): ResponseInterface {

            Logger::exception($exception, Logger::CRITICAL);

            $isDebug    = (($_ENV['APP_DEBUG'] ?? '0') === '1');
            $statusCode = 500;
            $message    = $isDebug ? $exception->getMessage() : 'Internal Server Error';

            // Resolve status code and readable title for known HTTP exceptions
            foreach (self::$httpExceptions as $class => [$code, $title]) {
                if ($exception instanceof $class) {
                    $statusCode = $code;
                    $message    = $title;
                    break;
                }
            }

            // Also handle any other Slim HTTP exception not listed above
            if ($statusCode === 500 && $exception instanceof HttpException) {
                $statusCode = $exception->getCode();
                $message    = $isDebug ? $exception->getMessage() : 'HTTP Error';
            }

            $payload = ['errors' => ['message' => $message]];

            // Always include debug info when APP_DEBUG=1
            if ($isDebug) {
                $payload['errors']['file']  = $exception->getFile();
                $payload['errors']['line']  = $exception->getLine();
                $payload['errors']['trace'] = $exception->getTraceAsString();
            }

            $response = new Response();
            $response->getBody()->write(json_encode($payload));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($statusCode);
        };
    }

    // Handles exceptions that escape Slim (PHP global handler)
    private static function handleException(\Throwable $exception): void
    {
        Logger::exception($exception, Logger::CRITICAL);

        $isDebug = (($_ENV['APP_DEBUG'] ?? '0') === '1');

        $payload = [
            'errors' => [
                'message' => $isDebug ? $exception->getMessage() : 'Internal Server Error',
            ]
        ];

        if ($isDebug) {
            $payload['errors']['file']  = $exception->getFile();
            $payload['errors']['line']  = $exception->getLine();
            $payload['errors']['trace'] = $exception->getTraceAsString();
        }

        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode($payload);
        exit(1);
    }
}