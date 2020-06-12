<?php

$BASE_ROUTE = '/curso-introduccion-php-deploy';

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");
header("Allow: GET, POST, PATCH, OPTIONS, PUT, DELETE");

ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

session_start();

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;
use App\Utils\Response;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
    'port'      => getenv('DB_PORT')
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();

$map = $routerContainer->getMap();

$map->post('auth.login', $BASE_ROUTE . '/auth/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'login',
]);

$map->post('auth.register', $BASE_ROUTE . '/auth/register', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'register',
]);

$map->get('auth.verify', $BASE_ROUTE . '/auth/verify', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'verify',
    'middleware' => 'App\Middleware\AuthMiddleware',
    'middlewareMethod' => 'isAuth'
]);

$map->get('users.getAll', $BASE_ROUTE . '/users', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'getAll',
    'middleware' => 'App\Middleware\AuthMiddleware',
    'middlewareMethod' => 'isAuth'
]);

$map->post('users.createOne', $BASE_ROUTE . '/users', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'createOne',
    'middleware' => 'App\Middleware\AuthMiddleware',
    'middlewareMethod' => 'isAuth'
]);

$map->get('users.getOne', $BASE_ROUTE . '/users/{id}', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'getOne',
    'middleware' => 'App\Middleware\AuthMiddleware',
    'middlewareMethod' => 'isAuth'
]);

$map->patch('users.updateOne', $BASE_ROUTE . '/users/{id}', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'updateOne',
    'middleware' => 'App\Middleware\AuthMiddleware',
    'middlewareMethod' => 'isAuth'
]);

$map->delete('users.deleteOne', $BASE_ROUTE . '/users/{id}', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'deleteOne',
    'middleware' => 'App\Middleware\AuthMiddleware',
    'middlewareMethod' => 'isAuth'
]);


$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);


if (!$route) {
    Response::error("Not found", 404);
} else {
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];
    $middlewareName = isset($handlerData['middleware'])? $handlerData['middleware'] : false;
    $middlewareMethodName = isset($handlerData['middlewareMethod'])? $handlerData['middlewareMethod'] : false;
    
    foreach ($route->attributes as $key => $val) {
        $request = $request->withAttribute($key, $val);
    }
    
    if (!empty($middlewareName) && !empty($middlewareMethodName)) {
        $middleware = new $middlewareName;
        $middleware->$middlewareMethodName($request);
    }

    $controller = new $controllerName;
    $controller->$actionName($request);
}
