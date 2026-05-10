<?php


use CodeIgniter\Router\RouteCollection;


/**
 * @var RouteCollection $routes
 */


$routes->options('api', static function () {
    return service('response')->setStatusCode(200);
});


$routes->options('api/(:segment)', static function () {
    return service('response')->setStatusCode(200);
});


$routes->options('api/(:segment)/(:segment)', static function () {
    return service('response')->setStatusCode(200);
});


$routes->post('api/login', 'Login::auth');
$routes->post('api/logout', 'Login::logout');
$routes->post('api/verifypin', 'UsuarioController::verifyPin');
$routes->post('api/resendpin', 'UsuarioController::resendPin');


$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->resource('usuarios', ['controller' => 'UsuarioController']);
    $routes->resource('libros', ['controller' => 'LibroController']);
    $routes->resource('categorias', ['controller' => 'CategoriaController']);
    $routes->resource('prestamos', ['controller' => 'PrestamoController']);
    $routes->resource('dashboard', ['controller' => 'DashboardController']);
});
