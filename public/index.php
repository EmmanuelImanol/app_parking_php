<?php

include_once __DIR__ . '/../includes/app.php';

use Controllers\CobroPorHoraController;
use Controllers\DashboardController;
use Controllers\LoginController;
use Controllers\UsuariosController;
use MVC\Router;

$router = new Router();

$router->get('/', function($router) {
  LoginController::login($router);
});
$router->post('/', function($router) {
  LoginController::login($router);
});
$router->get('/logout', function($router) {
  LoginController::logout();
});

$router->get('/dashboard', function($router) {
  DashboardController::index($router);
});

$router->get('/dashboard/usuarios', function($router) {
  UsuariosController::index($router);
});
// API para usuarios
$router->get('/api/usuarios', function($router) {
  UsuariosController::obtenerUsuarios();
});
$router->post('/api/usuario', function($router) {
  UsuariosController::crear();
});
$router->post('/api/usuario/actualizar', function($router) {
  UsuariosController::actualizar();
});
$router->post('/api/usuario/eliminar', function($router) {
  UsuariosController::eliminar();
});


$router->get('/dashboard/cobroporhora', function($router) {
  CobroPorHoraController::index($router);
});

$router->comprobarRutas();