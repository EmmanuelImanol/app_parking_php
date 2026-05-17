<?php

namespace Model;

class Ticket extends ActiveRecord {
  protected static string $tabla = 'tickets';
  protected static array $columnasDB = [
    'id',
    'tipoTicket',
    'folio',
    'clienteId',
    'registroId',
    'codigoQR',
    'estadoTicket',
    'fechaGeneracion'
  ];

  public string $tipoTicket;
  public string $folio;
  public ?int $clienteId = null;
  public ?int $registroId = null;
  public string $codigoQR;
  public string $estadoTicket;
  public string $fechaGeneracion;
  
  public function __construct(array $args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->tipoTicket = $args['tipoTicket'] ?? '';
    $this->folio = $args['folio'] ?? '';
    $this->clienteId = $args['clienteId'] ?? null;
    $this->registroId = $args['registroId'] ?? null;
    $this->codigoQR = $args['codigoQR'] ?? self::generarUUID();
    $this->estadoTicket = $args['estadoTicket'] ?? 'impreso';
    $this->fechaGeneracion = $args['fechaGeneracion'] ?? date('Y-m-d H:i:s');
  }

  public static function generarUUID(): string {
    $data = random_bytes(16);
    // Fuerza versión 4 y variante RFC 4122
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }
}