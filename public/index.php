<?php

include_once __DIR__ . '/../includes/app.php';

use Controllers\CobroPorHoraController;
use Controllers\ConfiguracionController;
use Controllers\DashboardController;
use Controllers\LoginController;
use Controllers\TarifasController;
use Controllers\UsuariosController;
use MVC\Router;

$router = new Router();

$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/dashboard/usuarios', [UsuariosController::class, 'index']);
// API para usuarios
$router->get('/api/usuarios', [UsuariosController::class, 'obtenerUsuarios']);
$router->post('/api/usuario', [UsuariosController::class, 'crear']);
$router->post('/api/usuario/actualizar', [UsuariosController::class, 'actualizar']);
$router->post('/api/usuario/eliminar', [UsuariosController::class, 'eliminar']);


$router->get('/dashboard/cobroporhora', [CobroPorHoraController::class, 'index']);
$router->get('/api/cobroporhora', [CobroPorHoraController::class, 'obtenerRegistrosEntrada']);
$router->post('/api/cobroporhora/entrada', [CobroPorHoraController::class, 'entradaVehiculo']);
$router->post('/api/cobroporhora/salida', [CobroPorHoraController::class, 'salidaVehiculo']);

// API Tarifas
$router->get('/api/tarifas', [TarifasController::class, 'obtenerTarifas']);

$router->get('/dashboard/configuracion', [ConfiguracionController::class, 'index']);
$router->get('/api/configuracion/estacionamiento', [ConfiguracionController::class, 'obtenerDatosConfiguracion']);
$router->get('/api/configuracion/tarifas', [ConfiguracionController::class, 'obtenerDatosTarifas']);
$router->post('/api/configuracion/actualizar', [ConfiguracionController::class, 'actualizarDatosConfiguracion']);
$router->post('/api/tarifas/actualizar', [ConfiguracionController::class, 'actualizarTarifa']);

$router->comprobarRutas();