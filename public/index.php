<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use App\Config\Database;
use App\Middleware\Middleware;
use App\Middleware\TrailingSlashMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

Database::init();

// Configure error handling based on environment
$displayErrorDetails = $_ENV['APP_ENV'] === 'development';
$logErrors = true;
$logErrorDetails = $_ENV['APP_ENV'] === 'development';

$app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);

// Add middleware in the correct order
$app->add(new TrailingSlashMiddleware());
$app->add(new Middleware());
$app->addBodyParsingMiddleware();

require __DIR__ . '/../app/routes.php';

$app->run(); 