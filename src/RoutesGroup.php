<?php

namespace HenriqueBS0\Router;

use HenriqueBS0\Router\Inner\RouteStore;

abstract class RoutesGroup extends RouteStore
{
    public function __construct()
    {
        $this->setRoutes();
    }

    abstract protected function setRoutes(): void;
}