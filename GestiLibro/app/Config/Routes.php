<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->post('api/login', 'Login::auth');
$routes->post('api/logout', 'Login::logout');

$routes->group('api', ['namespace' => 'App\Controllers'], function($routes) {

    $routes->resource('usuarios', ['controller' => 'UsuarioController']);
    $routes->resource('libros', ['controller' => 'LibroController']);
    $routes->resource('categorias', ['controller' => 'CategoriaController']);
    $routes->resource('prestamos', ['controller' => 'PrestamoController']);
    $routes->resource('dashboard', ['controller' => 'DashboardController']);
});






