<?php

use HenriqueBS0\Router\Inner\Route;
use HenriqueBS0\Router\Middleware;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    static $result;
    static Route $route;
    static Middleware $middleware;

    protected function setUp(): void
    {
        self::$middleware = $this->getMockForAbstractClass(Middleware::class);

        self::$route = new Route('user/{id}/note/{id}', function() {}, [self::$middleware]);

        self::$result = null;
    }

    public function testNotFitsUriByNumberOfInconsistentParts() {
        $this->assertFalse(self::$route->fitsUri('user/01'));
    }

    public function testNotFitsUriByDifferentSignature() {
        $this->assertFalse(self::$route->fitsUri('users/01/note/03'));
    }

    public function testFitsUri() {
        $this->assertTrue(self::$route->fitsUri('user/01/note/03'));
    }

    public function testGetUriParametes() {
        $this->assertSame(['01', '05'], self::$route->getUriParameters('user/01/note/05'));
    }

    public function testAddMiddlewares() {
        self::$route->addMiddlewares([self::$middleware]);

        $expectedMiddlewares = [self::$middleware, self::$middleware];

        $reflectedRoute = new ReflectionClass(Route::class);
        $reflection = $reflectedRoute->getProperty('middlewares');
        $reflection->setAccessible(true);
        $actualMiddlewares = $reflection->getValue(self::$route);

        $this->assertSame($expectedMiddlewares, $actualMiddlewares);
    }

    public function testUnauthorizedRun() {
        self::$route->addMiddlewares([
            new class extends Middleware {
                public function checks(): bool
                {
                    return false;
                }

                public function invalidated(): void {
                    RouteTest::$result = 'Unauthorized Run';
                }
            }
        ]);

        self::$route->run('');

        $this->assertSame('Unauthorized Run', self::$result);
    }

    public function testRun() {
        $idUser = '01';
        $idNote = '99';

        $route = new Route('user/{id}/note/{id}', function($idUser, $idNote) {
            RouteTest::$result = [$idUser, $idNote];
        });

        $route->run("user/{$idUser}/note/{$idNote}");

        $this->assertSame([$idUser, $idNote], self::$result);
    }
}
