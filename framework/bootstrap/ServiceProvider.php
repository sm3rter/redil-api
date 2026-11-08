<?php

namespace Framework\Bootstrap;

use Psr\Container\ContainerInterface;

class ServiceProvider
{
    /**
     * Register facades with the container
     */
    public static function registerFacades(ContainerInterface $container)
    {
        \Framework\Facades\Request::setFacadeApplication($container);
        \Framework\Facades\Response::setFacadeApplication($container);
        \Framework\Facades\Auth::setFacadeApplication($container);
        \Framework\Facades\DB::setFacadeApplication($container);
    }
}
