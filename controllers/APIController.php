<?php

namespace Controllers;

use Model\Usuario;

class APIController {
  public static function index() {
    $usuarios = Usuario::all();

    $usuarios = array_map(function($usuario) {
      unset($usuario->password, $usuario->password_confirm);
      return $usuario;
    }, $usuarios);

    echo json_encode($usuarios);
  }
}