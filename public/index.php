<?php

include_once __DIR__ . '/../includes/app.php';

use Controllers\CobroPorHoraController;
use Controllers\DashboardController;
use Controllers\LoginController;
use Controllers\UsuariosController;
use MVC\Router;

$router = new Router();

$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/dashboard/usuarios', [UsuariosController::class, 'index']);
// API para usuarios
$router->get('/api/usuarios', function($router) {
  UsuariosController::obtenerUsuarios();
});
$router->post('/api/usuario', [UsuariosController::class, 'crear']);
$router->post('/api/usuario/actualizar', [UsuariosController::class, 'actualizar']);
$router->post('/api/usuario/eliminar', [UsuariosController::class, 'eliminar']);


$router->get('/dashboard/cobroporhora', [CobroPorHoraController::class, 'index']);

$router->comprobarRutas();