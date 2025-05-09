<?php

namespace App\Facades;

use Psr\Http\Message\ServerRequestInterface;

class Request extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ServerRequestInterface::class;
    }

    public static function all()
    {
        return static::getFacadeRoot()->getParsedBody();
    }

    public static function get($key, $default = null)
    {
        $data = static::all();
        return $data[$key] ?? $default;
    }

    public static function has($key)
    {
        $data = static::all();
        return isset($data[$key]);
    }

    public static function only(array $keys)
    {
        $data = static::all();
        return array_intersect_key($data, array_flip($keys));
    }

    public static function except(array $keys)
    {
        $data = static::all();
        return array_diff_key($data, array_flip($keys));
    }

    public static function header($key, $default = null)
    {
        return static::getFacadeRoot()->getHeaderLine($key) ?: $default;
    }

    public static function method()
    {
        return static::getFacadeRoot()->getMethod();
    }

    public static function isMethod($method)
    {
        return strtoupper($method) === static::method();
    }
} 