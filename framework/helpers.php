<?php

use App\Config\Config;
use Framework\Logger\Logger;

// ── Environment ──────────────────────────────────────────────────────────────

if (!function_exists('is_production')) {
    function is_production(): bool
    {
        return env('APP_ENV', 'production') === 'production';
    }
}

if (!function_exists('is_development')) {
    function is_development(): bool
    {
        return env('APP_ENV', 'production') === 'development';
    }
}

if (!function_exists('is_debug')) {
    function is_debug(): bool
    {
        return env('APP_DEBUG', '0') === '1';
    }
}

// ── Config ───────────────────────────────────────────────────────────────────

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

// ── Logging ──────────────────────────────────────────────────────────────────

if (!function_exists('logger')) {
    function logger(): Logger
    {
        return new class {
            public function info(string $msg, array $ctx = [])    { Logger::info($msg, $ctx); }
            public function error(string $msg, array $ctx = [])   { Logger::error($msg, $ctx); }
            public function warning(string $msg, array $ctx = []) { Logger::warning($msg, $ctx); }
            public function debug(string $msg, array $ctx = [])   { Logger::debug($msg, $ctx); }
        };
    }
}

if (!function_exists('info')) {
    function info(string $message, array $context = []): void
    {
        Logger::info($message, $context);
    }
}

if (!function_exists('log_error')) {
    function log_error(string $message, array $context = []): void
    {
        Logger::error($message, $context);
    }
}

// ── Strings ──────────────────────────────────────────────────────────────────

if (!function_exists('str_limit')) {
    function str_limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($value) <= $limit) return $value;
        return mb_substr($value, 0, $limit) . $end;
    }
}

if (!function_exists('str_slug')) {
    function str_slug(string $value, string $separator = '-'): string
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[^\pL\pN\s]/u', '', $value);
        $value = preg_replace('/[\s_]+/', $separator, $value);
        return trim($value, $separator);
    }
}

if (!function_exists('str_snake')) {
    function str_snake(string $value): string
    {
        $value = preg_replace('/([a-z])([A-Z])/', '$1_$2', $value);
        return strtolower($value);
    }
}

if (!function_exists('str_camel')) {
    function str_camel(string $value): string
    {
        return lcfirst(str_replace('_', '', ucwords($value, '_')));
    }
}

if (!function_exists('str_uuid')) {
    function str_uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

if (!function_exists('str_random')) {
    function str_random(int $length = 32): string
    {
        return bin2hex(random_bytes(intdiv($length, 2)));
    }
}

// ── Arrays ───────────────────────────────────────────────────────────────────

if (!function_exists('array_get')) {
    function array_get(array $array, string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_only')) {
    function array_only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }
}

if (!function_exists('array_except')) {
    function array_except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }
}

if (!function_exists('array_flatten')) {
    function array_flatten(array $array, int $depth = INF): array
    {
        $result = [];
        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } elseif ($depth === 1) {
                $result = array_merge($result, array_values($item));
            } else {
                $result = array_merge($result, array_flatten($item, $depth - 1));
            }
        }
        return $result;
    }
}

// ── Date & Time ──────────────────────────────────────────────────────────────

if (!function_exists('now')) {
    function now(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }
}

if (!function_exists('today')) {
    function today(): string
    {
        return date('Y-m-d');
    }
}

if (!function_exists('timestamp')) {
    function timestamp(): int
    {
        return time();
    }
}

// ── Misc ─────────────────────────────────────────────────────────────────────

if (!function_exists('value')) {
    function value(mixed $value): mixed
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('tap')) {
    function tap(mixed $value, callable $callback): mixed
    {
        $callback($value);
        return $value;
    }
}

if (!function_exists('dd')) {
    function dd(mixed ...$vars): never
    {
        foreach ($vars as $var) {
            echo '<pre style="background:#1a1d2e;color:#68d391;padding:1rem;border-radius:8px;font-family:monospace;font-size:13px;margin:8px;">';
            var_dump($var);
            echo '</pre>';
        }
        exit(1);
    }
}

if (!function_exists('dump')) {
    function dump(mixed ...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre style="background:#1a1d2e;color:#68d391;padding:1rem;border-radius:8px;font-family:monospace;font-size:13px;margin:8px;">';
            var_dump($var);
            echo '</pre>';
        }
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $root = dirname(__DIR__, 2);
        return $path ? $root . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $root;
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        return base_path('app' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}
