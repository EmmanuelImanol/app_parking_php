<?php

namespace Controllers;

use Model\ClientesEstacionamiento;
use Model\Tarifa;
use MVC\Router;

class ConfiguracionController {
  public static function index(Router $router) {
    session_start();
    isAuth();
    isAdmin();

    $clienteId = $_SESSION['id'];
    $tarifas = Tarifa::belongsTo('clienteId', $clienteId);
    
    $router->render('dashboard/configuracion/index', [
      'titulo' => 'Configuracion',
      'tarifas' => $tarifas
    ]);
  }

  public static function obtenerDatosConfiguracion() {
    session_start();
    isAuth();
    isAdmin();
    $clienteId = $_SESSION['id'];
    $configuracion = ClientesEstacionamiento::where('id', $clienteId);
    echo json_encode($configuracion);
  }

  public static function obtenerDatosTarifas() {
      session_start();
      isAuth();
      isAdmin();
      $clienteId = $_SESSION['id'];
      $tarifas = Tarifa::belongsTo('clienteId', $clienteId);
      echo json_encode($tarifas);
  }

  public static function actualizarDatosConfiguracion() {
    session_start();
    isAdmin();
    isAdmin();

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $clienteId = $_SESSION['id'];
      $configuracion = ClientesEstacionamiento::where('id', $clienteId);
      if($configuracion) {
        $configuracion->sincronizar($_POST);
        $alertas = $configuracion->validarEstacionamiento();

        if(empty($alertas)) {
          $resultado = $configuracion->actualizar();
          if($resultado) {
            echo json_encode([
              'resultado' => true,
              'mensaje' => 'Datos del estacionamiento actualizados correctamente',
              'alertas' => []
            ]);
            exit;
          }
        }
        
        echo json_encode([
          'resultado' => false,
          'alertas' => $alertas
        ]);
      }

    }
  }
}