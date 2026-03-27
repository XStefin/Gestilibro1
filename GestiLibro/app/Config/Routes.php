<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/','Home::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/login/logout', 'Login::logout');

$routes->group('roles', ['filter' => 'auth'], ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'RolController::index');
    $routes->get('create', 'RolController::create');
    $routes->post('store', 'RolController::store');
    $routes->get('edit/(:num)', 'RolController::edit/$1');
    $routes->post('update/(:num)', 'RolController::update/$1');
    $routes->get('delete/(:num)', 'RolController::delete/$1');
});

$routes->group('auditoria', ['filter' => 'auth'], ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'AuditoriaController::index');
});

$routes->group('tareas', ['filter' => 'auth'], ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'TareaController::index');
    $routes->get('create', 'TareaController::create');
    $routes->post('store', 'TareaController::store');
    $routes->get('edit/(:num)', 'TareaController::edit/$1');
    $routes->post('update/(:num)', 'TareaController::update/$1');
    $routes->get('delete/(:num)', 'TareaController::delete/$1');
});

$routes->group('usuarios', ['filter' => 'auth'], ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'UsuarioController::index');                 
    $routes->get('create', 'UsuarioController::create');           
    $routes->post('store', 'UsuarioController::store');            
    $routes->get('edit/(:num)', 'UsuarioController::edit/$1');     
    $routes->post('update/(:num)', 'UsuarioController::update/$1');
    $routes->get('delete/(:num)', 'UsuarioController::delete/$1'); 
});

$routes->group('categorias', ['filter' => 'auth'], ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'CategoriaController::index');
    $routes->get('create', 'CategoriaController::create');
    $routes->post('store', 'CategoriaController::store');
    $routes->get('edit/(:num)', 'CategoriaController::edit/$1');
    $routes->post('update/(:num)', 'CategoriaController::update/$1');
    $routes->get('delete/(:num)', 'CategoriaController::delete/$1');
});

$routes->group('prestamos',['filter' => 'auth'], ['namespace' => 'App\Controllers'], function($routes){
    $routes->get('/', 'PrestamoController::index');
    $routes->get('create', 'PrestamoController::create');
    $routes->post('store', 'PrestamoController::store');
    $routes->get('edit/(:num)', 'PrestamoController::edit/$1');
    $routes->post('update/(:num)', 'PrestamoController::update/$1');
    $routes->get('delete/(:num)', 'PrestamoController::delete/$1');
});

$routes->group('libros',['filter' => 'auth'], function($routes) {
    $routes->get('/', 'LibroController::index');
    $routes->get('create', 'LibroController::create');
    $routes->post('store', 'LibroController::store');
    $routes->get('edit/(:num)', 'LibroController::edit/$1');
    $routes->post('update/(:num)', 'LibroController::update/$1');
    $routes->get('delete/(:num)', 'LibroController::delete/$1');
});







