<?php

namespace HenriqueBS0\Router;

abstract class Middleware
{
    public function checks(): bool
    {
        return true;
    }

    public function invalidated(): void {}
}