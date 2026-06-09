<?php

namespace App\Bootstrap;

use Psr\Container\ContainerInterface;

class ServiceProvider
{
    /**
     * Register facades with the container
     */
    public static function registerFacades(ContainerInterface $container)
    {
        \App\Facades\Request::setFacadeApplication($container);
        \App\Facades\Response::setFacadeApplication($container);
        \App\Facades\Auth::setFacadeApplication($container);
        \App\Facades\DB::setFacadeApplication($container);
    }
}
