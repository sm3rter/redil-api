<?php

namespace App\Bootstrap\Middleware;

use App\Config\Config;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;

class RateLimitMiddleware implements MiddlewareInterface
{
    private $limit;
    private $window;
    private $store;

    public function __construct(int $limit = 60, int $window = 60)
    {
        $this->limit = $limit;
        $this->window = $window;
        $this->store = Config::get('security.rate_limit_store', getenv('RATE_LIMIT_STORE') ?: 'apcu');
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $windowKey = floor(time() / $this->window);
        $key = 'rate:' . md5($ip . '|' . $windowKey);

        $count = $this->increment($key);
        $remaining = max(0, $this->limit - $count);

        if ($count > $this->limit) {
            $retryAfter = $this->window - (time() % $this->window);
            $resp = new SlimResponse(429);
            $resp->getBody()->write(json_encode(['errors' => ['message' => 'Too Many Requests']]));
            return $resp
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Retry-After', (string) $retryAfter)
                ->withHeader('X-RateLimit-Limit', (string) $this->limit)
                ->withHeader('X-RateLimit-Remaining', '0');
        }

        $response = $handler->handle($request);
        return $response
            ->withHeader('X-RateLimit-Limit', (string) $this->limit)
            ->withHeader('X-RateLimit-Remaining', (string) $remaining);
    }

    private function increment(string $key): int
    {
        if ($this->store === 'redis' && class_exists('Redis')) {
            try {
                $redis = new \Redis();
                $redis->connect($_ENV['REDIS_HOST'] ?? '127.0.0.1', (int) ($_ENV['REDIS_PORT'] ?? 6379));
                if (!$redis->exists($key)) {
                    $redis->set($key, 1, $this->window);
                    return 1;
                }
                return $redis->incr($key);
            } catch (\Throwable $e) {
                // Fallback to APCu or in-memory store
            }
        }

        if (function_exists('apcu_inc')) {
            $value = apcu_inc($key, 1, $success);
            if ($success === false) {
                apcu_store($key, 1, $this->window);
                return 1;
            }
            return (int) $value;
        }

        static $store = [];
        if (!isset($store[$key])) {
            $store[$key] = 1;
        } else {
            $store[$key]++;
        }

        return $store[$key];
    }
}
