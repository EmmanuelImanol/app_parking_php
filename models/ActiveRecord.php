<?php

namespace Model;

use PDO;

class ActiveRecord {

  // Base de datos
  protected static PDO $db;
  protected static string $tabla = '';
  protected static array $columnasDB = [];

  // Alertas y Mensajes
  protected static array $alertas = [];

  // ✅ Declarar $id aquí para que todos los modelos lo hereden
  public ?int $id = null;

  public static function setDB(PDO $database): void {
    self::$db = $database;
  }

  public static function setAlerta(string $tipo, string $mensaje): void {
    static::$alertas[$tipo][] = $mensaje;
  }

  public static function getAlertas(): array {
    return static::$alertas;
  }

  public function validar(): array {
    static::$alertas = [];
    return static::$alertas;
  }

  public static function consultarSQL(string $query): array {
    try {
      //code...
      // ✅ CAMBIO 3: PDO->query() funciona igual que MySQLi->query()
      $resultado = self::$db->query($query);

      $array = [];
      // ✅ CAMBIO 4: fetch(PDO::FETCH_ASSOC) reemplaza a fetch_assoc()
      while($registro = $resultado->fetch(PDO::FETCH_ASSOC)) {
        $array[] = static::crearObjeto($registro);
      }
      // ✅ CAMBIO 5: closeCursor() reemplaza a free() para liberar memoria
      $resultado->closeCursor();

      return $array;
    } catch (\PDOException $e) {
      error_log("Error en consultarSQL: " . $e->getMessage());
      return [];

    }
  }

  // Crea el objeto en memoria que es igual al de la BD
  protected static function crearObjeto(array $registro): static {
    $objeto = new static;
    foreach ($registro as $key => $value) {
      if (property_exists($objeto, $key)) {
        $objeto->$key = $value;
      }
    }
    return $objeto;
  }

  // Identificar y unir los atributos de la BD
  public function atributos(): array {
    $atributos = [];
    foreach (static::$columnasDB as $columna) {
      if ($columna === 'id') continue;
      $atributos[$columna] = $this->$columna;
    }
    return $atributos;
  }

  // Sanitizar los datos antes de guardarlos en la BD
  public function sanitizarAtributos(): array {
    $atributos = $this->atributos();
    $sanitizado = [];
    foreach ($atributos as $key => $value) {
      // Si es null o string vacío → NULL real de SQL (sin comillas)
      if (is_null($value) || $value === '') {
        $sanitizado[$key] = 'NULL';
      } else {
        $sanitizado[$key] = self::$db->quote($value);
      }
    }
    return $sanitizado;
  }

  // Sincroniza BD con Objetos en memoria
  public function sincronizar(array $args = []): void {
    foreach ($args as $key => $value) {
      if (property_exists($this, $key) && !is_null($value)) {
        $this->$key = $value;
      }
    }
  }

  // CRUD - Guardar (crear o actualizar)
  public function guardar(): array|bool {
    if (!is_null($this->id)) {
      return $this->actualizar();
    }
    return $this->crear();
  }

  // Todos los registros
  public static function all(): array {
    $query = "SELECT * FROM " . static::$tabla;
    return self::consultarSQL($query);
  }

  // Busca un registro por su id
  public static function find(int $id): ?static {
    $query = "SELECT * FROM " . static::$tabla . " WHERE id = {$id}";
    $resultado = self::consultarSQL($query);
    return array_shift($resultado);
  }

  // Obtener registros con cierta cantidad
  public static function get(int $limite): ?static {
    $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite}";
    $resultado = self::consultarSQL($query);
    return array_shift($resultado);
  }

  // Busca registros por columna y valor
  public static function where(string $columna, mixed $valor): ?static {
    $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}'";
    $resultado = self::consultarSQL($query);
    return array_shift($resultado);
  }

  // Busca todos los registros que pertenecen a un ID
  public static function belongsTo(string $columna, mixed $valor): ?array {
    $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}'";
    $resultado = self::consultarSQL($query);
    return $resultado; 
  }

  // Consulta SQL plana
  public static function SQL(string $query): array {
    return self::consultarSQL($query);
  }

  // Crea un nuevo registro
  public function crear(): array {
    // Los atributos ya vienen con comillas gracias a quote()
    // Ejemplo: ['nombre' => "'Juan'", 'email' => "'juan@mail.com'"]
    $atributos = $this->sanitizarAtributos();

    // ✅ CAMBIO 7: Ya no se agregan comillas en el query porque quote() las incluye
    $query  = "INSERT INTO " . static::$tabla . " (";
    $query .= join(', ', array_keys($atributos));
    $query .= ") VALUES (";
    $query .= join(', ', array_values($atributos)); // Sin comillas extra
    $query .= ")";

    try {
      $resultado = self::$db->query($query);
      return [
        'resultado' => !!$resultado,
        // ✅ CAMBIO 8: lastInsertId() reemplaza a insert_id
        'id' => self::$db->lastInsertId()
      ];
    } catch (\PDOException $e) {
      error_log("Error en crear(): " . $e->getMessage());
      return ['resultado' => false, 'id' => null];
    }
  }

  // Actualizar el registro
  public function actualizar(): bool {
      $atributos = $this->sanitizarAtributos();

      $valores = [];
      foreach ($atributos as $key => $value) {
          // ✅ CAMBIO 9: Sin comillas extra porque quote() ya las incluye
          $valores[] = "{$key}={$value}";
      }

      $query  = "UPDATE " . static::$tabla . " SET ";
      $query .= join(', ', $valores);
      // ✅ CAMBIO 10: quote() para el ID en lugar de escape_string()
      $query .= " WHERE id = " . self::$db->quote($this->id);
      $query .= " LIMIT 1";

      try {
          return (bool) self::$db->query($query);
      } catch (\PDOException $e) {
          error_log("Error en actualizar(): " . $e->getMessage());
          return false;
      }
  }

  // Eliminar un registro por su ID
  public function eliminar(): bool {
    // ✅ CAMBIO 11: quote() para el ID en lugar de escape_string()
    $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->quote($this->id) . " LIMIT 1";

    try {
      return (bool) self::$db->query($query);
    } catch (\PDOException $e) {
      error_log("Error en eliminar(): " . $e->getMessage());
      return false;
    }
  }

}