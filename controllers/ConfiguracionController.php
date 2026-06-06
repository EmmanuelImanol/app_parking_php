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

  public static function crearTarifa() {
    session_start();
    isAuth();
    isAdmin();

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $clienteId = $_SESSION['clienteId'];

      $tarifa = new Tarifa([
        'clienteId' => $clienteId,
        'tipo' => strtolower(trim($_POST['tipo'] ?? '')),
        'horaTarifa' => (float)($_POST['horaTarifa'] ?? 0),
      ]);

      $alertas = $tarifa->validarNuevaTarifa();
      if(!empty($alertas)) {
        echo json_encode(['resultado' => false, 'alertas' => $alertas]);
        return;
      }

      $existe = Tarifa::consultarSQL(
        "SELECT id FROM tarifas
         WHERE tipo = '{$tarifa->tipo}'
         AND clienteId = '$clienteId'
         LIMIT 1"
      );

      if($existe) {
        echo json_encode([
          'resultado' => false,
          'alertas'   => ['error' => ["Ya existe una tarifa para '{$tarifa->tipo}'"]]
        ]);
        return;
      }

      $resultado = $tarifa->guardar();

      echo json_encode([
        'resultado' => $resultado['resultado'],
        'mensaje' => $resultado['resultado'] ? 'Tarifa creada correctamente' : 'Error al crear tarifa',
        'tarifa' => [
          'id' => $resultado['id'],
          'tipo' => $tarifa->tipo,
          'horaTarifa' => $tarifa->horaTarifa,
          'activo' => 1,
        ]
      ]);
    }
  }

  public static function actualizarTarifa() {
    session_start();
    isAuth();
    isAdmin();

    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $clienteId = $_SESSION['clienteId'];

      $rawInput = file_get_contents('php://input');
      error_log('Input recibido: ' . $rawInput); // ← ve qué llega

      $datos = json_decode(file_get_contents('php://input'), true);

      if(!$datos) {
        echo json_encode(['resultado' => false, 'mensaje' => 'Datos no válidos']);
        return;
      }

      error_log('Datos decodificados: ' . print_r($datos, true)); // ← ve la estructura

      foreach($datos as $tarifaData) {
        error_log('Procesando tarifa ID: ' . ($tarifaData['id'] ?? 'sin id')); // ← ve cada tarifa
        $tarifaDB = Tarifa::find($tarifaData['id']);

        if(!$tarifaDB || (int)$tarifaDB->clienteId !== (int)$clienteId) {
          echo json_encode(['resultado' => false, 'mensaje' => 'Tarifa no autorizada']);
          return;
        }

        $tarifaDB->tipo = strtolower(trim($tarifaData['tipo'] ?? ''));
        $tarifaDB->horaTarifa = (float)($tarifaData['horaTarifa'] ?? 0);

        // Conectar las validaciones del modelo
        $alertas = $tarifaDB->validarActualizarTarifa();
        if(!empty($alertas)) {
          echo json_encode([
            'resultado' => false,
            'alertas' => $alertas
          ]);
          return;
        }

        $resultado = $tarifaDB->actualizar();
        error_log('Resultado actualizar: ' . print_r($resultado, true)); // ← ve si actualizar falla
      }

      echo json_encode([
        'resultado' => true,
        'mensaje'   => 'Tarifas actualizadas correctamente'
      ]);
    }
  }

  public static function toggleTarifa() {
    session_start();
    isAuth();
    isAdmin();

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $clienteId = $_SESSION['clienteId'];
      $id = $_POST['id'] ?? null;

      if(!$id) {
        echo json_encode(['resultado' => false, 'mensaje' => 'ID no válido']);
        return;
      }

      $tarifa = Tarifa::find($id);

      if(!$tarifa || (int)$tarifa->clienteId !== (int)$clienteId) {
        echo json_encode(['resultado' => false, 'mensaje' => 'No autorizado']);
        return;
      }

      $tarifa->activo = $tarifa->activo ? 0 : 1;
      $resultado = $tarifa->actualizar();

      echo json_encode([
        'resultado' => $resultado,
        'activo' => $tarifa->activo,
        'mensaje' => $tarifa->activo ? 'Tarifa activada correctamente' : 'Tarifa desactivada correctamente'
      ]);
    }
  }
}