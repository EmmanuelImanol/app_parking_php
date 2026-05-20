<?php

namespace Model;

class Tarifa extends ActiveRecord {
  protected static string $tabla = 'tarifas';
  protected static array $columnasDB = [
    'id',
    'clienteId',
    'tipo',
    'horaTarifa',
    'activo',
  ];

  public ?int $clienteId;
  public string $tipo;
  public float $horaTarifa;
  public int $activo = 1;

  public function __construct(array $args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->clienteId = $args['clienteId'] ?? null;
    $this->tipo = $args['tipo'] ?? '';
    $this->horaTarifa = $args['horaTarifa'] ?? 0.0;
    $this->activo = $args['activo'] ?? 1;
  }

  public function validarNuevaTarifa() {
    if(!$this->tipo) {
      self::$alertas['error'][] = 'El tipo de tarifa es obligatorio';
    }
    if(!$this->horaTarifa) {
      self::$alertas['error'][] = 'La tarifa es obligatoria';
    }
    if($this->horaTarifa <= 0) {
      self::$alertas['error'][] = 'La tarifa debe ser mayor a 0';
    }

    return self::$alertas;
  }
}