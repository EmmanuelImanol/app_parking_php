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
$router->post('/dashboard/usuarios', [UsuariosController::class, 'crear']);
$router->get('/dashboard/cobroporhora', [CobroPorHoraController::class, 'index']);

$router->comprobarRutas();