<?php

namespace Model;

class ClientesEstacionamiento extends ActiveRecord {
  protected static string $tabla = 'clientesEstacionamiento';
  protected static array $columnasDB = [
    'id',
    'nombreSucursal',
    'direccionEstacionamiento',
    'rfc',
    'regimenFiscal',
    'representacionLegal',
    'telefonoContacto',
    'activo',
    'createdAt'
  ];

  public string $nombreSucursal;
  public string $direccionEstacionamiento;
  public string $rfc;
  public string $regimenFiscal;
  public ?string $representacionLegal = null;
  public ?string $telefonoContacto = null;
  public ?int $activo = 1;
  public ?string $createdAt = null;

  public function __construct(array $args = [])
  {
    $this->id                       = $args['id'] ?? null;
    $this->nombreSucursal           = $args['nombreSucursal'] ?? '';
    $this->direccionEstacionamiento = $args['direccionEstacionamiento'] ?? '';
    $this->rfc                      = $args['rfc'] ?? '';
    $this->regimenFiscal            = $args['regimenFiscal'] ?? '';
    $this->representacionLegal      = $args['representacionLegal'] ?? null;
    $this->telefonoContacto         = $args['telefonoContacto'] ?? null;
    $this->activo                   = $args['activo'] ?? 1;
    $this->createdAt                = $args['createdAt'] ?? null;
  }

  public function validarEstacionamiento(): array {
    if(!$this->nombreSucursal) {
      self::$alertas['error'][] = 'El nombre de la sucursal es obligatorio';
    }
    if(!$this->direccionEstacionamiento) {
      self::$alertas['error'][] = 'La dirección es obligatoria';
    }
    if(!$this->rfc) {
      self::$alertas['error'][] = 'El RFC es obligatorio';
    }
    if(!$this->regimenFiscal) {
      self::$alertas['error'][] = 'El régimen fiscal es obligatorio';
    }
    if(!$this->representacionLegal) {
      self::$alertas['error'][] = 'La representación legal es obligatoria';
    }
    if(!$this->telefonoContacto) {
      self::$alertas['error'][] = 'El teléfono de contacto es obligatorio';
    }

    return self::$alertas;
  }
}