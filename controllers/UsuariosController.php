<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class UsuariosController {
  public static function index(Router $router) {
    session_start();
    isAuth();
    isAdmin();
    $router->render('dashboard/usuarios/index', [
      'titulo' => 'Usuarios'
    ]);
  }

  public static function obtenerUsuarios() {
    session_start();
    isAuth();
    isAdmin();
    $clienteId = $_SESSION['clienteId'];

    $usuarios = Usuario::belongsTo('clienteId', $clienteId);

    $usuarios = array_map(function($usuario) {
      unset($usuario->password, $usuario->password_confirm);
      return $usuario;
    }, $usuarios ?? []);

    echo json_encode($usuarios);
  }

  public static function crear() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_start();
      isAuth();
      isAdmin();

      $usuario = new Usuario($_POST);

      $usuario->clienteId = $_SESSION['clienteId'];

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
      session_start();
      isAuth();
      isAdmin();

      $id = $_POST['id'] ?? null;
      $usuario = Usuario::find($id);

      if(!$usuario || $usuario->clienteId !== $_SESSION['clienteId']) {
        echo json_encode(['resultado' => false, 'mensaje' => 'Acceso no autorizado']);
        return;
      }

      // Sincronizar datos
      $usuario->nombre = $_POST['nombre'] ?? $usuario->nombre;
      $usuario->email = $_POST['email'] ?? $usuario->email;

      // Validar
      $alertas = $usuario->validarUsuario();
      if(!empty($alertas)) {
        echo json_encode(['resultado' => false, 'alertas' => $alertas]);
        return;
      }

      // Verificar Email Duplicado
      $existeUsuario = Usuario::where('email', $usuario->email);
      if($existeUsuario && $existeUsuario->id !== $usuario->id) {
        echo json_encode(['resultado' => false, 'alertas' => ['error' => ['El email ya está en uso']]]);
        return;
      }

      $resultado = $usuario->actualizar();
      echo json_encode(['resultado' => $resultado]);
    }
  }

  public static function eliminar() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_start();
      isAuth();
      isAdmin();

      $id = $_POST['id'] ?? null;
      $usuario = Usuario::find($id);

      if(!$usuario || $usuario->clienteId !== $_SESSION['clienteId']) {
        echo json_encode(['resultado' => false, 'mensaje' => 'Acceso no autorizado']);
        return;
      }

      if($usuario->rol === 'admin') {
        echo json_encode([
          'resultado' => false,
          'mensaje' => 'No se puede eliminar al adminisrador'
        ]);
        return;
      }

      if((int)$usuario->id === (int)$_SESSION['id']) {
        echo json_encode([
          'resultado' => false,
          'mensaje' => 'No puedes eliminar tu propio usuario'
        ]);
        return;
      }

      $resultado = $usuario->eliminar();
      echo json_encode(['resultado' => $resultado]);
    }
  }
}