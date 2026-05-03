<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class UsuariosController {
  public static function index(Router $router) {
    session_start();
    isAuth();
    $alertas = Usuario::getAlertas();
    $usuarios = Usuario::all();
    $router->render('dashboard/usuarios/index', [
      'titulo' => 'Usuarios',
      'alertas' => $alertas,
      'usuario' => new Usuario,
      'usuarios' => $usuarios
    ]);
  }

  public static function crear(Router $router) {
    $alertas = [];
    // Instanciar usuario
    $usuario = new Usuario;
    $usuarios = Usuario::all();
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $usuario->sincronizar($_POST);
      $alertas = $usuario->validarNuevoUsuario();

      // Verificar que no exista el usuario
      if(empty($alertas)) {
        $existeUsuario = Usuario::where('email', $usuario->email);
        if($existeUsuario) {
          Usuario::setAlerta('error', 'El usuario ya esta registrado');
        } else {
          // Hashear el password
          $usuario->hashPassword();
          // Eliminar passwrod_confirm
          unset($usuario->password_confirm);
          // Crear un nuevo usuario
          $resultado = $usuario->guardar();

          if($resultado['resultado']) {
            Usuario::setAlerta('exito', 'Usuario creado correctamente');
            $usuario = new Usuario;  // ← campos quedan vacíos en el form
          } else {
            Usuario::setAlerta('error', 'Hubo un error');
          }
        }

      }
    }

    $alertas = Usuario::getAlertas();

    $router->render('dashboard/usuarios/index', [
      'titulo' => 'Usuarios',
      'alertas' => $alertas,
      'usuario' => $usuario,
      'usuarios' => $usuarios
    ]);
  }
}