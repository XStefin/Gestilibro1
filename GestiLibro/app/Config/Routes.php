<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Login::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/login/logout', 'Login::logout');

$routes->group('usuarios', ['filter' => 'auth', 'namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'UsuarioController::index');
    $routes->get('create', 'UsuarioController::create');
    $routes->post('store', 'UsuarioController::store');
    $routes->get('edit/(:num)', 'UsuarioController::edit/$1');
    $routes->post('update/(:num)', 'UsuarioController::update/$1');
    $routes->get('delete/(:num)', 'UsuarioController::delete/$1');
});

$routes->group('prestamos', ['filter' => 'auth', 'namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'PrestamoController::index');
    $routes->get('create', 'PrestamoController::create');
    $routes->post('store', 'PrestamoController::store');
    $routes->get('edit/(:num)', 'PrestamoController::edit/$1');
    $routes->post('update/(:num)', 'PrestamoController::update/$1');
    $routes->get('delete/(:num)', 'PrestamoController::delete/$1');
});

$routes->group('libros', ['filter' => 'auth', 'namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'LibroController::index');
    $routes->get('create', 'LibroController::create');
    $routes->post('store', 'LibroController::store');
    $routes->get('edit/(:num)', 'LibroController::edit/$1');
    $routes->post('update/(:num)', 'LibroController::update/$1');
    $routes->get('delete/(:num)', 'LibroController::delete/$1');
});