<?php

namespace Controllers;

use DateTime;
use Model\Tarifa;
use Model\Vehiculo;
use MVC\Router;

class CobroPorHoraController {
  public static function index(Router $router) {
    session_start();
    isAuth();
    $tarifas = Tarifa::all();
    $router->render('dashboard/cobroporhora/index', [
      'titulo' => 'Cobro por hora',
      'tarifas' => $tarifas // las pasa a la vista
    ]);
  }

  public static function obtenerRegistrosEntrada() {
    $registros = Vehiculo::belongsTo('estado', 'activo');
    echo json_encode($registros);
  }

  public static function entradaVehiculo() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_start();
      // Obtenemos los datos del formulario
      $placa = $_POST['placa'];
      $observaciones = $_POST['observaciones'];
      $tipoVehiculo = $_POST['tipoVehiculo'];

      // Generamos los datos automaticos
      $horaEntrada = date('Y-m-d H:i:s');
      $estado = 'activo';
      $horaSalida = null;

      // Obtenemos la tarifaId
      $tarifa = Tarifa::where('tipo', $_POST['tipoVehiculo']);
      if (!$tarifa) {
        echo json_encode([
          'resultado' => false,
          'mensaje' => 'Tipo de vehiculo no válido'
        ]);
        return;
      }

      $existeVehiculo = Vehiculo::where('placa', $placa);
      $existeVehiculo = Vehiculo::consultarSQL(
        "SELECT * FROM registroVehiculos 
        WHERE placa = '$placa' AND estado = 'activo' 
        LIMIT 1"
      );
      if($existeVehiculo) {
        echo json_encode([
          'resultado' => false,
          'mensaje' => 'El vehiculo ya esta adentro'
        ]);
        return;
      }
      
      $vehiculo = new Vehiculo([
        'placa' => $placa,
        'observaciones' => $observaciones,
        'tipoVehiculo' => $tipoVehiculo,
        'horaEntrada' => $horaEntrada,
        'horaSalida' => $horaSalida,
        'tarifaId' => $tarifa->id,
        'estado' => $estado
      ]);

      $resultado = $vehiculo->guardar();
      if($resultado['resultado']) {
        echo json_encode([
          'resultado' => true,
          'mensaje' => 'Vehiculo registrado correctamente',
          'id' => $resultado['id']
        ]);
      } else {
        echo json_encode([
          'resultado' => false,
          'mensaje' => 'Error al guardar en la base de datos'
        ]);
      }
    }
  }

  public static function salidaVehiculo() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'] ?? null;
      if(!$id) {
        echo json_encode([
          'resultado' => false,
          'mensaje' => 'ID no válido'
        ]);
        return;
      }

      $vehiculo = Vehiculo::find($id);

      if(!$vehiculo) {
        echo json_encode([
          'resultado' => false,
          'mensaje' => 'Vehiculo no encontrado'
        ]);
      }

      // Calcular total
      $horaEntrada = new DateTime($vehiculo->horaEntrada);
      $horaSalida = new DateTime();
      $diferencia = $horaEntrada->diff($horaSalida);
      $horas = ceil($diferencia->h + ($diferencia->i / 60));
      $tarifa = Tarifa::find($vehiculo->tarifaId);
      $total = $horas * $tarifa->horaTarifa;

      $vehiculo->horaSalida = $horaSalida->format('Y-m-d H:i:s');
      $vehiculo->totalPagado = $total;
      $vehiculo->estado = 'pagado';

      $resultado = $vehiculo->actualizar();

      if($resultado) {
        echo json_encode([
          'resultado' => true,
          'mensaje' => 'Pago registrado correctamente',
          'total' => $total,
          'horas' => $horas
        ]);
      } else {
        echo json_encode(['resultado' => false, 'mensaje' => 'Error al registrar pago']);
      }
    }
  }
}