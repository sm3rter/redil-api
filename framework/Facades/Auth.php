<?php

namespace Framework\Facades;

class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'auth';
    }

    public static function user()
    {
        // Implement your user retrieval logic here
        return null;
    }

    public static function check()
    {
        return static::user() !== null;
    }

    public static function id()
    {
        return static::user() ? static::user()->id : null;
    }
} 