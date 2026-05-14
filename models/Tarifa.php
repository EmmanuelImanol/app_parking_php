<?php

namespace Model;

class Tarifa extends ActiveRecord {
  protected static string $tabla = 'tarifas';
  protected static array $columnasDB = [
    'id',
    'tipo',
    'horaTarifa',
    'activo',
  ];

  public string $tipo;
  public float $horaTarifa;
  public int $activo = 1;

  public function __construct(array $args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->tipo = $args['tipo'] ?? '';
    $this->horaTarifa = $args['horaTarifa'] ?? 0.0;
    $this->activo = $args['activo'] ?? 1;
  }
}