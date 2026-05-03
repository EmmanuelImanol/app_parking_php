<?php

namespace Model;

class Usuario extends ActiveRecord {
  // Base de datos
  protected static string $tabla = 'usuarios';
  protected static array $columnasDB = [
    'id',
    'nombre',
    'email',
    'rol',
    'password'
  ];

  public string $nombre;
  public string $email;
  public string $rol;
  public string $password;
  public string $password_confirm;

  public function __construct(array $args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->nombre = $args['nombre'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->rol = $args['rol'] ?? 'cajero';
    $this->password = $args['password'] ?? '';
    $this->password_confirm = $args['password_confirm'] ?? '';
  }

  public function validarLogin() {
    if(!$this->email) {
      self::$alertas['error'][] = 'El email es obligatorio';
    }
    if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      self::$alertas['error'][] = 'El email no es válido';
    }
    if(!$this->password) {
      self::$alertas['error'][] = 'La contraseña es obligatoria';
    }

    return self::$alertas;
  }

  // Validación para usuarios nuevos
  public function validarNuevoUsuario() {
    if(!$this->nombre) {
      self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
    }
    if(!$this->email) {
      self::$alertas['error'][] = 'El email del usuario es obligatorio';
    }
    if(!$this->password) {
      self::$alertas['error'][] = 'El password no puede ir vacio';
    }
    if(strlen($this->password) < 6) {
      self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
    }

    if($this->password !== $this->password_confirm) {
      self::$alertas['error'][] = 'Los passwords deben de ser iguales';
    }

    return self::$alertas;
  }

  // Hashea el password
  public function hashPassword() {
    $this->password = password_hash($this->password, PASSWORD_BCRYPT);
  }
}