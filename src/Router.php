<?php

namespace HenriqueBS0\Router;

use Closure;
use HenriqueBS0\Router\Inner\RouteStore;

class Router extends RouteStore
{
    private Closure|array $callbackNotFound;

    public function __construct(string $baseUri = '/')
    {
        RouteStore::$baseUri = $baseUri;

        $this->callbackNotFound = function () {
            http_response_code(404);
            echo '404';
        };
    }

    public function addRoutesGroup(string $routesGroupClass): void
    {
        $routesGroup = new $routesGroupClass();
        $this->addRoutes($routesGroup->getRoutes());
    }

    public function setNotFound(callable|array $callback): void
    {
        if (is_array($callback)) {
            $callback[0] = new $callback[0];
        }

        $this->callbackNotFound = $callback;
    }

    private function getHttpMethod(): string
    {
        $method = isset($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];
        return strtolower($method);
    }

    private function getCurrentUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        return $uri !== '/' ? rtrim($uri, '/') : $uri;
    }

    public function resolve(): void
    {
        $uri = $this->getCurrentUri();
        $httpMethod = $this->getHttpMethod();

        foreach ($this->getHttpMethodRoutes($httpMethod) as $route) {
            if ($route->fitsUri($uri)) {
                $route->run($uri);
                die;
            }
        }

        call_user_func($this->callbackNotFound);
        die;
    }
}