<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'status' => 'success',
        'message' => 'Welcome to Redil API Framework',
        'version' => '1.0.0',
        'documentation' => 'https://github.com/sm3rter/redil-api'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->group('/api', function (RouteCollectorProxy $group) {
    
}); 