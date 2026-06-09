<?php

namespace App\Bootstrap;

use DI\Container;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use App\Config\Database;
use App\Config\Config;
use App\Middleware\Middleware;
use App\Middleware\TrailingSlashMiddleware;
use App\Bootstrap\Middleware\CorsMiddleware;
use App\Bootstrap\Middleware\RateLimitMiddleware;
use App\Bootstrap\Middleware\SecurityHeadersMiddleware;
use App\Middleware\RequestValidationMiddleware;

class Bootstrap
{
    private $app;
    private $config;

    public function __construct()
    {
        $this->loadEnvironment();
        $this->loadConfig();
    }

    private function loadEnvironment()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $this->validateEnvironment();
    }

    private function validateEnvironment()
    {
        $required = ['APP_ENV'];
        $missing  = [];

        foreach ($required as $var) {
            if (!isset($_ENV[$var])) {
                $missing[] = $var;
            }
        }

        if (!empty($missing)) {
            throw new \RuntimeException('Missing required environment variables: ' . implode(', ', $missing));
        }

        if (!isset($_ENV['APP_DEBUG'])) {
            $_ENV['APP_DEBUG'] = (($_ENV['APP_ENV'] ?? 'production') === 'development') ? '1' : '0';
        } else {
            $_ENV['APP_DEBUG'] = in_array(strtolower((string) $_ENV['APP_DEBUG']), ['1', 'true', 'yes'], true) ? '1' : '0';
        }

        if ((($_ENV['APP_ENV'] ?? 'production') === 'production') && empty($_ENV['APP_KEY'])) {
            throw new \RuntimeException('Missing required APP_KEY environment variable in production.');
        }
    }

    private function loadConfig()
    {
        Config::load(__DIR__ . '/../../config');
        $this->config = Config::get('app', []);
    }

    public function bootstrap()
    {
        ErrorHandler::setup();

        $container = new Container();
        AppFactory::setContainer($container);

        if (method_exists($container, 'set')) {
            $container->set('config', Config::all());
        }

        $this->app = AppFactory::create();

        Database::init();

        $this->setupMiddleware();

        // Add Slim error middleware and plug in our custom handler
        $isDebug      = (($_ENV['APP_DEBUG'] ?? '0') === '1');
        $errorMiddleware = $this->app->addErrorMiddleware($isDebug, true, true);
        $errorMiddleware->setDefaultErrorHandler(ErrorHandler::slimHandler());

        ServiceProvider::registerFacades($container);

        $this->loadRoutes();

        return $this->app;
    }

    private function setupMiddleware()
    {
        $this->app->add(new CorsMiddleware());
        $this->app->add(new SecurityHeadersMiddleware());
        $this->app->add(new RateLimitMiddleware(
            config('app.rate_limit', 100),
            config('app.rate_window', 60)
        ));
        $this->app->addBodyParsingMiddleware();
        $this->app->add(new RequestValidationMiddleware());
        $this->app->add(new TrailingSlashMiddleware());
        $this->app->add(new Middleware());
    }

    private function loadRoutes()
    {
        $app       = $this->app;
        $routesDir = __DIR__ . '/../../routes';

        foreach (glob($routesDir . '/*.php') as $routeFile) {
            require $routeFile;
        }
    }

    public function getApp()    { return $this->app; }
    public function getConfig() { return $this->config; }
}
