<?php

namespace Model;

class Vehiculo extends ActiveRecord {
  protected static string $tabla = 'registroVehiculos';
  protected static array $columnasDB = [
    'id',
    'clienteId',
    'usuarioId',
    'placa',
    'observaciones',
    'tipoVehiculo',
    'horaEntrada',
    'horaSalida',
    'tarifaId',
    'totalPagado',
    'estado'
  ];


  public ?int $clienteId;
  public ?int $usuarioId;
  public string $placa;
  public ?string $observaciones;
  public string $tipoVehiculo;
  public string $horaEntrada;
  public ?string $horaSalida;
  public int $tarifaId;
  public ?float $totalPagado;
  public string $estado;

  public function __construct(array $args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->clienteId = $args['clienteId'] ?? null;
    $this->usuarioId = $args['usuarioId'] ?? null;
    $this->placa = strtoupper($args['placa'] ?? '');
    $this->observaciones = $args['observaciones'] ?? '';
    $this->tipoVehiculo = $args['tipoVehiculo'] ?? 'auto';
    $this->horaEntrada = $args['horaEntrada'] ?? '';
    $this->horaSalida = $args['horaSalida'] ?? null;
    $this->tarifaId = $args['tarifaId'] ?? 0;
    $this->totalPagado = $args['totalPagado'] ?? 0.00;
    $this->estado = $args['estado'] ?? 'activo';
  }

  public function validarVehiculo() {
    if(!$this->placa) {
      self::$alertas['error'][] = 'La placa es obligatoria';
    }
    if(!$this->observaciones) {
      self::$alertas['error'][] = 'Debes colocar las observaciones';
    }
    if(!$this->tipoVehiculo) {
      self::$alertas['error'][] = 'El tipo de vehiculo es obligatorio';
    }

    return self::$alertas;
  }
}