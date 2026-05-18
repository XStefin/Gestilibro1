<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->options('(:any)', static function () {
    return service('response')
        ->setHeader('Access-Control-Allow-Origin', 'https://front-gestilibro.vercel.app')
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
        ->setHeader('Access-Control-Allow-Credentials', 'true')
        ->setStatusCode(200);
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
