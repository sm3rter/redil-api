<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\UserController;

$app->get('/', function (Request $request, Response $response) {
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
}); 