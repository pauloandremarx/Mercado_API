<?php


use App\JwtMiddleware;
require 'vendor/autoload.php';


use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
$router = new RouteCollector();


// Configurar os cabeçalhos CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


$jwtMiddleware = new JwtMiddleware(123456789);


$router->filter('auth', function() use ($jwtMiddleware){
    $jwtMiddleware->handle();
});

$router->group(['before' => 'auth'], function($router) use ($jwtMiddleware) {
    $router->post('/products', ['App\Controllers\ProductsController', 'c_store']);
    $router->put('products/{id}', ['App\Controllers\ProductsController', 'c_update']);
    $router->delete('products/{id}', ['App\Controllers\ProductsController', 'c_destroy']);
});
$router->get('/products', ['App\Controllers\ProductsController', 'c_show']);
$router->get('/products/{id}', ['App\Controllers\ProductsController', 'c_find']);

$router->group(['before' => 'auth'], function($router) use ($jwtMiddleware) {
    $router->post('/tax', ['App\Controllers\TaxController', 'c_store']);
    $router->put('tax/{id}', ['App\Controllers\TaxController', 'c_update']);
    $router->delete('tax/{id}', ['App\Controllers\TaxController', 'c_destroy']);
});
$router->get('/tax', ['App\Controllers\TaxController', 'c_show']);
$router->get('/tax/{id}', ['App\Controllers\TaxController', 'c_find']);

$router->group(['before' => 'auth'], function($router) use ($jwtMiddleware) {
    $router->post('/category', ['App\Controllers\CategoryController', 'c_store']);
    $router->put('category/{id}', ['App\Controllers\CategoryController', 'c_update']);
    $router->delete('category/{id}', ['App\Controllers\CategoryController', 'c_destroy']);
});
$router->get('/category', ['App\Controllers\CategoryController', 'c_show']);
$router->get('/category/{id}', ['App\Controllers\CategoryController', 'c_find']);

$router->group(['before' => 'auth'], function($router) use ($jwtMiddleware) {
    $router->put('users/{id}', ['App\Controllers\UsersController', 'c_update']);
    $router->delete('users/{id}', ['App\Controllers\UsersController', 'c_destroy']);
});
$router->get('/users', ['App\Controllers\UsersController', 'c_show']);
$router->get('/users/{id}', ['App\Controllers\UsersController', 'c_find']);
$router->post('/users', ['App\Controllers\UsersController', 'c_store']);


$router->group(['prefix' => '/login'], function($router) {
    $router->post('/', ['App\Controllers\Auth', 'login_store']);
    $router->get('/', ['App\Controllers\Auth', 'login_show']);

});


$router->post('/logout', ['App\Controllers\Auth', 'logout']);

$dispatcher = new Dispatcher($router->getData());
// Captura a requisição atual
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Despacha a requisição para o controlador apropriado
$response = $dispatcher->dispatch($requestMethod, $requestUri);

echo  $response;











