<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class UsuariosController {
  public static function index(Router $router) {
    session_start();
    isAuth();
    $router->render('dashboard/usuarios/index', [
      'titulo' => 'Usuarios'
    ]);
  }

  public static function obtenerUsuarios() {
    $usuarios = Usuario::all();

    $usuarios = array_map(function($usuario) {
      unset($usuario->password, $usuario->password_confirm);
      return $usuario;
    }, $usuarios);

    echo json_encode($usuarios);
  }

  public static function crear() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $usuario = new Usuario($_POST);
      $alertas = $usuario->validarNuevoUsuario();

      if(!empty($alertas)) {
        echo json_encode(['resultado' => false, 'alertas' => $alertas]);
        return;
      }

      $existeUsuario = Usuario::where('email', $usuario->email);
      if($existeUsuario) {
        echo json_encode(['resultado' => false, 'alertas' => ['error' => ['El email ya está registrado']]]);
        return;
      }

      $usuario->hashPassword();
      unset($usuario->password_confirm);
      $resultado = $usuario->guardar();

      echo json_encode($resultado);
    }
  }

  public static function actualizar() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {

    }
  }

  public static function eliminar() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'] ?? null;

      if(!$id) {
        echo json_encode(['resultado' => false, 'mensaje' => 'ID no proporcionado']);
        return;
      }

      $usuario = Usuario::find($id);

      if(!$usuario) {
        echo json_encode(['resultado' => false, 'mensaje' => 'Usuario no encontrado']);
        return;
      }

      $resultado = $usuario->eliminar();
      echo json_encode(['resultado' => $resultado]);
    }
  }
}