<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Controllers\UserController;
use App\Controllers\ProductController;

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    $responseData = [
        'data' => [
            'message' => 'Welcome to Redil API Framework',
            'version' => '1.0.0',
            'documentation' => 'https://github.com/sm3rter/redil-api'
        ],
        'errors' => []
    ];
    
    $response->getBody()->write(json_encode($responseData));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->group('/api', function (RouteCollectorProxy $group) {
    // User routes
    $group->get('/users', [UserController::class, 'index']);
    $group->get('/users/{id}', [UserController::class, 'show']);
    $group->post('/users', [UserController::class, 'store']);
    $group->put('/users/{id}', [UserController::class, 'update']);
    $group->delete('/users/{id}', [UserController::class, 'delete']);
    $group->post('/login', [UserController::class, 'login']);

    // Product routes
    $group->get('/products', [ProductController::class, 'index']);
    $group->get('/products/{id}', [ProductController::class, 'show']);
    $group->post('/products', [ProductController::class, 'store']);
    $group->patch('/products/{id}/stock', [ProductController::class, 'updateStock']);
});
