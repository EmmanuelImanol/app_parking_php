<?php

namespace Controllers;

use DateTime;
use Model\ClientesEstacionamiento;
use Model\Tarifa;
use Model\Ticket;
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
    session_start();
    isAuth();
    $clienteId = $_SESSION['clienteId'];
    $usuarioId = $_SESSION['id'];

    $registros = Vehiculo::consultarSQL(
      "SELECT * FROM registroVehiculos
       WHERE clienteId = '$clienteId' 
       AND usuarioId = '$usuarioId'
       AND estado = 'activo'"
    );
    echo json_encode($registros);
  }

  public static function entradaVehiculo() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_start();
      isAuth();

      $t0 = microtime(true);
      // Obtenemos los datos del formulario
      $placa = $_POST['placa'];
      $observaciones = $_POST['observaciones'];
      $tipoVehiculo = $_POST['tipoVehiculo'];
      $clienteId = $_SESSION['clienteId'];
      $usuarioId = $_SESSION['id'];

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

      $existeVehiculo = Vehiculo::consultarSQL(
        "SELECT * FROM registroVehiculos 
        WHERE placa = '$placa'
        AND clienteId = '$clienteId' 
        AND estado = 'activo' 
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
        'clienteId' => $clienteId,
        'usuarioId' => $usuarioId,
        'placa' => $placa,
        'observaciones' => $observaciones,
        'tipoVehiculo' => $tipoVehiculo,
        'horaEntrada' => $horaEntrada,
        'horaSalida' => $horaSalida,
        'tarifaId' => $tarifa->id,
        'estado' => $estado
      ]);

      $t1 = microtime(true);
      error_log('Validaciones: ' . round(($t1 - $t0) * 1000) . 'ms');

      $resultado = $vehiculo->guardar();

      $t2 = microtime(true);
      error_log('Guardar vehículo: ' . round(($t2 - $t1) * 1000) . 'ms');
      
      if(!$resultado['resultado']) {
        echo json_encode(['resultado' => false, 'mensaje' => 'Error al guardar el vehículo']);
        return;
      }

      $ultimoTicket = Ticket::consultarSQL("SELECT id FROM tickets ORDER BY id DESC LIMIT 1");
      $numero = $ultimoTicket ? (int)$ultimoTicket[0]->id + 1 : 1;
      $folio = 'PKG-' . date('Y') . '-' . str_pad($numero, 5, '0', STR_PAD_LEFT);

      // Generar Ticket
      $ticket = new Ticket([
        'tipoTicket' => 'Cobro por hora',
        'clienteId' => $clienteId,
        'registroId' => $resultado['id'],
        'folio' => $folio,
      ]);

      $ticketGuardado = $ticket->guardar();

      $t3 = microtime(true);
      error_log('Guardar ticket: ' . round(($t3 - $t2) * 1000) . 'ms');

      if(!$ticketGuardado['resultado']) {
        echo json_encode(['resultado' => false, 'mensaje' => 'Vehículo guardado pero error al generar ticket']);
        return;
      }

      // Datos del cliente para el encabezado del ticket
      $cliente = ClientesEstacionamiento::find($clienteId);

      $t4 = microtime(true);
      error_log('Buscar cliente: ' . round(($t4 - $t3) * 1000) . 'ms');

      error_log('TOTAL: ' . round(($t4 - $t0) * 1000) . 'ms');

      echo json_encode([
        'resultado' => true,
        'mensaje' => 'Vehiculo registrado correctamente',
        'ticket' => [
          'folio' => $folio,
          'codigoQR' => $ticket->codigoQR,
          'tipoTicket' => $ticket->tipoTicket,
          'placa' => $placa,
          'tipoVehiculo' => $tipoVehiculo,
          'horaEntrada' => $horaEntrada,
          'tarifa' => $tarifa->horaTarifa,
          'nombreSucursal' => $cliente->nombreSucursal,
          'direccion' => $cliente->direccionEstacionamiento,
          'rfc' => $cliente->rfc,
          'regimenFiscal' => $cliente->regimenFiscal,
          'representacionLegal' => $cliente->representacionLegal,
          'telefonoContacto' => $cliente->telefonoContacto
        ]
      ]);
    }
  }

  public static function salidaVehiculo() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_start();
      isAuth();
      $clienteId = $_SESSION['clienteId'];

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
        return;
      }

      if((int)$vehiculo->clienteId !== (int)$clienteId) {
        echo json_encode([
          'reslutado' => false,
          'mensaje' => 'No autorizado'
        ]);
        return;
      }

      // Calcular total
      $horaEntrada = new DateTime($vehiculo->horaEntrada);
      $horaSalida = new DateTime();
      $diferencia = $horaEntrada->diff($horaSalida);
      
      $horas = ceil($diferencia->h + ($diferencia->i / 60));
      $tarifa = Tarifa::find($vehiculo->tarifaId);
      $total = $horas * $tarifa->horaTarifa;

      // Tiempo legible para el ticket: "2 hrs 33 min"
      $tiempoFormateado = '';
      if($diferencia->h > 0) {
        $tiempoFormateado .= $diferencia->h . ' hr' . ($diferencia->h > 1 ? 's' : '');
      }
      if($diferencia->i > 0) {
        $tiempoFormateado .= ' ' . $diferencia->i . ' min';
      }
      if($tiempoFormateado === '') {
        $tiempoFormateado = 'Menos de 1 min';
      }

      // Actualizar Vehiculo
      $vehiculo->horaSalida = $horaSalida->format('Y-m-d H:i:s');
      $vehiculo->totalPagado = $total;
      $vehiculo->estado = 'pagado';

      $resultado = $vehiculo->actualizar();

      if(!$resultado) {
        echo json_encode(['resultado' => false, 'mensaje' => 'Error al registrar pago']);
        return;
      }

      $ticketExistente = Ticket::where('registroId', $vehiculo->id);
      
      if($ticketExistente) {
        $ticketExistente->estadoTicket = 'leido';
        $ticketExistente->actualizar();
        $ticketUsado = $ticketExistente;
      } else {
        $ultimoTicket = Ticket::consultarSQL("SELECT id FROM tickets ORDER BY id DESC LIMIT 1");
        $numero = $ultimoTicket ? (int)$ultimoTicket[0]->id + 1 : 1;
        $folio = 'PKG-' . date('Y') . '-' . str_pad($numero, 5, '0', STR_PAD_LEFT); 

        $ticketNuevo = new Ticket([
          'clienteId' => $clienteId,
          'tipoTicket' => 'Cobro por hora',
          'registroId' => $vehiculo->id,
          'folio' => $folio
        ]);
        $ticketNuevo->guardar();
        $ticketUsado = $ticketNuevo;
      }

      // Datos del cliente para el encabezado
      $cliente = ClientesEstacionamiento::find($clienteId);

      echo json_encode([
        'resultado' => true,
        'mensaje'   => 'Pago registrado correctamente',
        'ticket' => [
          'nombreSucursal' => $cliente->nombreSucursal,
          'direccion' => $cliente->direccionEstacionamiento,
          'tipoTicket' => $ticketUsado->tipoTicket,
          'rfc' => $cliente->rfc,
          'regimenFiscal' => $cliente->regimenFiscal,
          'representacionLegal' => $cliente->representacionLegal,
          'telefonoContacto' => $cliente->telefonoContacto,
          'folio' => $ticketUsado->folio,
          'codigoQR' => $ticketUsado->codigoQR,
          'placa' => $vehiculo->placa,
          'tipoVehiculo' => $vehiculo->tipoVehiculo,
          'observaciones' => $vehiculo->observaciones,
          'horaEntrada' => $vehiculo->horaEntrada,
          'horaSalida' => $vehiculo->horaSalida,
          'tiempo' => trim($tiempoFormateado),
          'tarifa' => $tarifa->horaTarifa,
          'total' => $total,
        ]
      ]);
    }
  }
}