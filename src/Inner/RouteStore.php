<?php

namespace HenriqueBS0\Router\Inner;

abstract class RouteStore
{
    private array $middlewares = [];
    private string $group = '';
    private array $routes = [
        'get'    => [],
        'post'   => [],
        'put'    => [],
        'patch'  => [],
        'delete' => []
    ];

    public function setMiddlewares(array $middlewaresClasses): void
    {
        $this->clearMiddlewares();
        foreach ($middlewaresClasses as $middlewareClass) {
            $this->addMiddleware($middlewareClass);
        }
    }

    public function addMiddleware(string $middlewareClass): void
    {
        $this->middlewares[] = new $middlewareClass();
    }

    public function clearMiddlewares(): void
    {
        $this->middlewares = [];
    }

    protected function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function group(string $group): void
    {
        $this->group = $group;
    }

    private function getGroup(): string
    {
        return $this->group;
    }

    private function addRoute(string $httpMethod, string $path, callable|array $callback, array $middlewaresClasses = []): void
    {
        $path = $this->getGroup() . $path;

        $path = $path !== '/' ? rtrim($path, '/') : $path;

        $middlewares = [];

        if (is_array($callback)) {
            $callback[0] = new $callback[0];
        }

        foreach ($middlewaresClasses as $middlewareClass) {
            $middlewares[] = new $middlewareClass();
        }

        $middlewares = array_merge($this->middlewares, $middlewares);

        $this->routes[$httpMethod][] = new Route($path, $callback, $middlewares);
    }

    protected function addRoutes(array $routes) {
        foreach ($routes as $httpMethod => $methodRoutes) {
            $numberRoutes = count($methodRoutes);

            for ($index = 0; $index < $numberRoutes; $index++) {
                $methodRoutes[$index]->addMiddlewares($this->middlewares);
            }

            $this->routes[$httpMethod] = array_merge($this->routes[$httpMethod], $methodRoutes);
        }
    }

    public function get(string $path, callable|array $callback, array $middlewaresClasses = []): void
    {
        $this->addRoute('get', $path, $callback, $middlewaresClasses);
    }

    public function post(string $path, callable|array $callback, array $middlewaresClasses = []): void
    {
        $this->addRoute('post', $path, $callback, $middlewaresClasses);
    }

    public function put(string $path, callable|array $callback, array $middlewaresClasses = []): void
    {
        $this->addRoute('put', $path, $callback, $middlewaresClasses);
    }

    public function patch(string $path, callable|array $callback, array $middlewaresClasses = []): void
    {
        $this->addRoute('patch', $path, $callback, $middlewaresClasses);
    }

    public function delete(string $path, callable|array $callback, array $middlewaresClasses = []): void
    {
        $this->addRoute('delete', $path, $callback, $middlewaresClasses);
    }

    protected function getRoutes(): array
    {
        return $this->routes;
    }

    protected function getHttpMethodRoutes(string $httpMethod): array
    {
        return $this->routes[$httpMethod];
    }
}
