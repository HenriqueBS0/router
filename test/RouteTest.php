<?php

use HenriqueBS0\Router\Inner\Route;
use HenriqueBS0\Router\Middleware;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    static $resultRun;
    static $resultUnauthorizedRun;

    public function testFitsUri() {
        $route = new Route('user/{id}', function() {});

        $this->assertTrue($route->fitsUri('user/1'));
        $this->assertFalse($route->fitsUri('user'));
        $this->assertFalse($route->fitsUri('users/1'));
    }

    public function testGetUriParametes() {
        $route = new Route('user/{id}/note/{id}', function() {});

        $uri = 'user/1/note/5';

        $expectedParametes = ['1', '5'];

        $this->assertEquals($expectedParametes, $route->getUriParameters($uri));
    }

    public function testAddMiddlewares() {
        $middleware = $this->getMockForAbstractClass(Middleware::class);

        $middlewares = [$middleware, $middleware];

        $route = new Route('', function() {}, $middlewares);

        $route->addMiddlewares($middlewares);

        $expectedMiddlewares = [$middleware, $middleware, $middleware, $middleware];

        $reflectedRoute = new ReflectionClass(Route::class);
        $reflection = $reflectedRoute->getProperty('middlewares');
        $reflection->setAccessible(true);
        $actualMiddlewares = $reflection->getValue($route);

        $this->assertSame($expectedMiddlewares, $actualMiddlewares);

    }

    public function testRun() {
        $route = new Route('user/{id}/note/{id}', function($idUser, $idNote) {
            RouteTest::$resultRun = [$idUser, $idNote];
        });

        $expectedResultRun = ['01', '99'];

        $route->run('user/01/note/99');

        $this->assertSame($expectedResultRun, self::$resultRun);
    }

    public function testUnauthorizedRun() {
        $route = new Route('', function($idUser, $idNote) {
            RouteTest::$resultRun = [$idUser, $idNote];
        });

        $route->addMiddlewares([
            new class extends Middleware {
                public function checks(): bool
                {
                    return false;
                }

                public function invalidated(): void {
                    RouteTest::$resultUnauthorizedRun = false;
                }
            }
        ]);

        $route->run('');

        $this->assertFalse(self::$resultUnauthorizedRun);
    }
}
