# Router @HenriqueBS0
PHP component to control your application's routes.

Componente PHP para o controle de rotas de sua aplicação.
## Installation
Composer
```bash
composer require henriquebs0/router dev-main
```

## Routes
To start managing routes, just instantiate an object of the <code>Router</code> class in a central point of your application where all requests pass (Ex: the file <strong>index.php</strong>), add the routes and at the end call the <code>resolve()</code> method.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use HenriqueBS0\Router\Router;

/**
 * Instantiating the object
 */
$router = new Router();

/**
 * Adding a route to the '/' path
 */
$router->get('/', function() {
    echo 'Hello World!';
});

/**
 * Resolves the process and forwards to the route
 */
$router->resolve();
```
### Adding Routes

To add routes there are five methods that are directly related to HTTP methods, namely:

- <code>get($path, $callback, $middlewaresClasses): void</code>
- <code>post($path, $callback, $middlewaresClasses): void</code>
- <code>put($path, $callback, $middlewaresClasses): void</code>
- <code>patch($path, $callback, $middlewaresClasses): void</code>
- <code>delete($path, $callback, $middlewaresClasses): void</code>

  - <code>string $path</code>: Path that will be accessed.
  - <code>callable|array $callback</code>: Function to be performed.
  - <code>array $middlewaresClasses</code>: Middleware classes.

```php
<form method="POST">
    <label for="_method">Method</label>
    <select name="_method" id="_method">
        <option value="GET">GET</option>
        <option value="POST">POST</option>
        <option value="PUT">PUT</option>
        <option value="PATCH">PATCH</option>
        <option value="DELETE">DELETE</option>
    </select>
    <input type="submit" value="Send">
</form>
<?php

require_once __DIR__ . '/vendor/autoload.php';

use HenriqueBS0\Router\Router;

class Controller {
    public function get() {
        echo "GET";
    }

    public function post() {
        echo "POST";
    }

    public function put() {
        echo "PUT";
    }

    public function patch() {
        echo "PATCH";
    }

    public function delete() {
        echo "DELETE";
    }
}

$router = new Router();

$router->get('/', [Controller::class, 'get']);
$router->post('/', [Controller::class, 'post']);
$router->put('/', [Controller::class, 'put']);
$router->patch('/', [Controller::class, 'patch']);
$router->delete('/', [Controller::class, 'delete']);

$router->resolve();
```

Note that there is a reserved parameter <code>_method</code>, this is because HTML5 forms only support the <code>GET</code> and <code>POST</code> methods.

### Parameters URL

To inform that a parameter is expected, enclose the name of this parameter in curly braces, these will be passed in order to the route callback.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use HenriqueBS0\Router\Router;

class Controller {
    public function user($id) {
        echo "User id - {$id}";
    }
}

$router = new Router();

/**
 * Expect to receive the 'id' parameter
 */
$router->get('/user/{id}', [Controller::class, 'user']);

$router->resolve();
```

### Group

To facilitate the process of informing paths there is the method <code>group(string $group)</code> that sets a base path for all the next informed routes. As is the previous example using this feature.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use HenriqueBS0\Router\Router;

class Controller {
    public function user($id) {
        echo "User id - {$id}";
    }
}

$router = new Router();

$router->group('/user');

$router->get('/{id}', [Controller::class, 'user']);

$router->resolve();
```

### Not Found

When the URL accessed does not match any route a default method is triggered, it sets the request status to 404, and then prints this value, to change this behavior there is the method <code>setNotFound(callable|array $callback)</code>.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use HenriqueBS0\Router\Router;

$router = new Router();
$router->setNotFound(function() {
    echo 'Not Found.';
});
$router->resolve();
```

## Middlewares

Middlewares are a way to inspect the requests that come to your application. To create a middleware just create a class that extends <code>Middleware</code>.

```php
<?php

use HenriqueBS0\Router\Middleware;

class MiddlewareExample extends Middleware {
    public function checks(): bool
    {
        return true;
    }

    public function invalidated(): void
    {
        echo 'Not authorized';
    }
}
```

The above class is composed of two methods, <code>checks()</code> which checks if the request is authorized and <code>invalidated()</code> which is executed when the request is not authorized (when <code >checks()</code> returns <code>false</code>).

### Associating Middlewares

A middleware can be associated directly with a route, or be associated in a general way, after adding a middleware in a general way, all the routes added after will take it into account.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use HenriqueBS0\Router\Middleware;
use HenriqueBS0\Router\Router;

class MiddlwareGeneralOne extends Middleware {}
class MiddlwareGeneralTwo extends Middleware {}

class MiddlwareRouteOne extends Middleware {}
class MiddlwareRouteTwo extends Middleware {}

$router = new Router();

/**
 * Linking middleware in general
 * All routes added after this action will consider middlewares
 * 'MiddlwareGeneralOne' and 'MiddlwareGeneralTwo'
 */
$router->setMiddlewares([
    MiddlwareGeneralOne::class,
    MiddlwareGeneralTwo::class
]);

/**
 * Binding middleware specifically to route
 */
$router->get('/', function() {
    'Hello World';
}, [MiddlwareRouteOne::class, MiddlwareRouteTwo::class]);

$router->resolve();
```

#### Methods for dealing with associated middleware in general

- <code>setMiddlewares(array $middlewaresClasses): void</code>
- <code>addMiddleware(string $middlewareClass): void</code>
- <code>clearMiddlewares(): void</code>

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use HenriqueBS0\Router\Middleware;
use HenriqueBS0\Router\Router;

class MiddlwareOne extends Middleware {}
class MiddlwareTwo extends Middleware {}
class MiddlwareThree extends Middleware {}
class MiddlwareFour extends Middleware {}

$router = new Router();

/**
 * Define that the middlewares applied will be
 * 'MiddlwareOne' and 'MiddlwareTwo'
 */
$router->setMiddlewares([
    MiddlwareOne::class,
    MiddlwareTwo::class
]);

/**
 * Clear all general middleware,
 * ie no middleware will be applied
 */

$router->clearMiddlewares();

/**
 * Add general middleware 'MiddlwareThree' and 'MiddlwareFour'
 */

 $router->addMiddleware(MiddlwareThree::class);
 $router->addMiddleware(MiddlwareFour::class);

 /**
  * According to flow the 'MiddlwareThree' and 'MiddlwareFour'
  * middleware will be linked to the route
  */
$router->get('/', function() {
    echo 'Hello World';
});

$router->resolve();
```

## Route Groups

To make the code more organized, you can separate the routes into classes and then add these to the instance of the <code>Router</code> class with the <code>addRoutesGroup(string $routesGroupClass)</code> method, these grouping classes of routes must extend the <code>RoutesGroup</code> class.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use HenriqueBS0\Router\Middleware;
use HenriqueBS0\Router\Router;
use HenriqueBS0\Router\RoutesGroup;

class MiddlewareGeneral extends Middleware {};
class MiddlewareRoutesUser extends Middleware{};

class ControllerUser {
    public function userGet($id) {
        echo "User - {$id} | GET";
    }

    public function userPost() {
        echo 'User | POST';
    }

    public function userPut($id) {
        echo "User - {$id} | PUT";
    }

    public function userPatch($id) {
        echo "User - {$id} | PATCH";
    }

    public function userDelete($id) {
        echo "User - {$id} | DELETE";
    }
}

/**
 * Class with route group
 */

class RoutesUser extends RoutesGroup {

    /**
     * Reserved method for adding routes
     */
    protected function setRoutes(): void
    {
        $this->group('/user');

        $this->setMiddlewares([MiddlewareRoutesUser::class]);

        $this->get('/{id}', [ControllerUser::class, 'userGet']);
        $this->post('/', [ControllerUser::class, 'userPost']);
        $this->put('/{id}', [ControllerUser::class, 'userPut']);
        $this->patch('/{id}', [ControllerUser::class, 'userPatch']);
        $this->delete('/{id}', [ControllerUser::class, 'userDelete']);
    }
}

$router = new Router();

$router->get('/', function() {
    echo 'Hello World!';
});

/**
 * Adding general middleware
 */
$router->addMiddleware(MiddlewareGeneral::class);

/**
 * Adding a generic group
 */
$router->group('/generic');

/**
 * Adding route group
 */
$router->addRoutesGroup(RoutesUser::class);

$router->resolve();
```
Note that classes that extend <code>RoutesGroup</code> have the reserved method <code>setRoutes()</code> where the routes must be defined. The <code>Router</code> class shares methods with the <code>RoutesGroup</code> class that have the same purpose, they are:
- <code>get($path, $callback, $middlewaresClasses): void</code>
- <code>post($path, $callback, $middlewaresClasses): void</code>
- <code>put($path, $callback, $middlewaresClasses): void</code>
- <code>patch($path, $callback, $middlewaresClasses): void</code>
- <code>delete($path, $callback, $middlewaresClasses): void</code>
- <code>group(string $group): void</code>
- <code>setMiddlewares(array $middlewaresClasses): void</code>
- <code>addMiddleware(string $middlewareClass): void</code>
- <code>clearMiddlewares(): void</code>

>External general middleware like the example <code>MiddlewareGeneral</code> is associated with the route group.

>The <code>group</code> method in the scope of the <code>setRoutes</code> method does not interfere with the <code>group</code> method of the <code>Router</code> class instance, the opposite too does not occur.

## License

#### The MIT License (MIT). Please see <a href="https://github.com/HenriqueBS0/router/blob/main/LICENSE">License file</a> for more information.
