<?php

$BASE_ROUTE = '/curso-introduccion-php-deploy';

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

session_start();

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;

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

$map->get('index', $BASE_ROUTE . '/', [
    'controller' => 'App\Controllers\IndexController',
    'action' => 'indexAction'
]);

$map->get('users.getAll', $BASE_ROUTE . '/users', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'getAll'
]);

$map->post('users.createOne', $BASE_ROUTE . '/users', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'createOne'
]);

$map->get('users.getOne', $BASE_ROUTE . '/users/{id}', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'getOne'
]);

$map->patch('users.updateOne', $BASE_ROUTE . '/users/{id}', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'updateOne'
]);

$map->delete('users.deleteOne', $BASE_ROUTE . '/users/{id}', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'deleteOne'
]);

// $map->get('addUser', $BASE_ROUTE . '/users/add', [
//     'controller' => 'App\Controllers\UsersController',
//     'action' => 'getAddUser'
// ]);
// $map->post('saveUser', $BASE_ROUTE . '/users/save', [
//     'controller' => 'App\Controllers\UsersController',
//     'action' => 'postSaveUser'
// ]);
// $map->get('loginForm', $BASE_ROUTE . '/login', [
//     'controller' => 'App\Controllers\AuthController',
//     'action' => 'getLogin'
// ]);
// $map->get('logout', $BASE_ROUTE . '/logout', [
//     'controller' => 'App\Controllers\AuthController',
//     'action' => 'getLogout'
// ]);
// $map->post('auth', $BASE_ROUTE . '/auth', [
//     'controller' => 'App\Controllers\AuthController',
//     'action' => 'postLogin'
// ]);
// $map->get('admin', $BASE_ROUTE . '/admin', [
//     'controller' => 'App\Controllers\AdminController',
//     'action' => 'getIndex',
//     'auth' => true
// ]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if (!$route) {
    $status = 404;
    $response = [
        'message' => 'Not found',
        'data' => null,
        'statusCode' => $status,
        'error' => true
    ];
    http_response_code($status);
    echo json_encode($response);
} else {
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];
    // $needsAuth = $handlerData['auth'] ?? false;

    // $sessionUserId = $_SESSION['userId'] ?? null;
    // if ($needsAuth && !$sessionUserId) {
    //     echo 'Protected route';
    //     die;
    // }

    $controller = new $controllerName;
    $controller->$actionName($request);
}
