<?php

namespace HenriqueBS0\Router\Inner;

use Closure;

class Route
{
    private RouteModel $routeModel;
    private Closure|array $callback;
    private array $middlewares;

    public function __construct(string $path, callable|array $callback, array $middlewares = [])
    {
        $this->routeModel = new RouteModel($path);
        $this->callback = $callback;
        $this->middlewares = $middlewares;
    }

    public function addMiddlewares(array $middlewares): void
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
    }

    public function fitsUri(string $uri): bool
    {
        $uriParts = $this->routeModel->explodeByBar($uri);

        $numberUriParts = count($uriParts);

        if ($numberUriParts !== $this->routeModel->getNumberPartsPath()) {
            return false;
        }

        foreach ($this->routeModel->getPathParts() as $pathPart) {
            if ($uriParts[$pathPart->getPosition()] !== $pathPart->getValue()) {
                return false;
            }
        }

        return true;
    }

    public function getUriParameters($uri): array
    {
        $parameters = [];

        $uriParts = $this->routeModel->explodeByBar($uri);

        foreach ($this->routeModel->getPositionParametersPath() as $parameter => $position) {
            $parameters[$parameter] = $uriParts[$position];
        }

        return $parameters;
    }

    public function run(string $uri): void
    {
        foreach ($this->middlewares as $middleware) {
            if (!$middleware->checks()) {
                $middleware->invalidated();
                return;
            }
        }

        $callback = $this->callback;
        $params = $this->getUriParameters($uri);

        call_user_func_array($callback, $params);
        return;
    }
}