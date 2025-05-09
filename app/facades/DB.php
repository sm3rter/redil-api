<?php

namespace App\Facades;

use Illuminate\Database\Capsule\Manager;

class DB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }

    public static function table($table)
    {
        return static::getFacadeRoot()->table($table);
    }

    public static function select($query, $bindings = [])
    {
        return static::getFacadeRoot()->select($query, $bindings);
    }
} 