<?php

namespace App\Config;

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Config\Config;

class Database
{
    public static function init()
    {
        $capsule = new Capsule;
        $db = Config::get('db', []);

        $capsule->addConnection([
            'driver'    => $db['driver'] ?? 'mysql',
            'host'      => $db['host'] ?? env('DB_HOST', '127.0.0.1'),
            'database'  => $db['database'] ?? env('DB_NAME', ''),
            'username'  => $db['username'] ?? env('DB_USER', 'root'),
            'password'  => $db['password'] ?? env('DB_PASS', ''),
            'charset'   => $db['charset'] ?? 'utf8',
            'collation' => $db['collation'] ?? 'utf8_unicode_ci',
            'prefix'    => $db['prefix'] ?? '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
} 