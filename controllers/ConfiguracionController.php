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

    $clienteId = $_SESSION['clienteId'];
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
    $clienteId = $_SESSION['clienteId'];
    $configuracion = ClientesEstacionamiento::where('id', $clienteId);
    echo json_encode($configuracion);
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

  public static function actualizarTarifa() {
    session_start();
    isAuth();
    isAdmin();

    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $clienteId = $_SESSION['clienteId'];

      $datos = json_decode(file_get_contents('php://input'), true);

      if(!$datos) {
        echo json_encode(['resultado' => false, 'mensaje' => 'Datos no válidos']);
        return;
      }

      foreach($datos as $tarifaData) {
        // Crear instancia del modelo para usar sus validaciones
        $tarifa = new Tarifa([
          'tipo' => $tarifaData['tipo'] ?? '',
          'horaTarifa' => $tarifaData['horaTarifa'] ?? 0,
        ]);

        // Conectar las validaciones del modelo
        $alertas = $tarifa->validarNuevaTarifa();
        if(!empty($alertas)) {
          echo json_encode([
            'resultado' => false,
            'alertas' => $alertas
          ]);
          return;
        }

        // Busca el registro real en BD y verifica que pertenece al cliente
        $tarifaDB = Tarifa::find($tarifaData['id']);
        if(!$tarifaDB || (int)$tarifaDB->clienteId !== (int)$clienteId) {
          echo json_encode([
            'resultado' => false, 
            'mensaje' => 'Tarifa no autorizada'
          ]);
          return;
        }

        $tarifaDB->tipo = strtolower(trim($tarifaData['tipo']));
        $tarifaDB->horaTarifa = $tarifaData['horaTarifa'];
        $tarifaDB->actualizar();
      }

      echo json_encode([
        'resultado' => true,
        'mensaje'   => 'Tarifas actualizadas correctamente'
      ]);
    }
  }
}