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

  public static function crear(Router $router) {
    $alertas = [];
    // Instanciar usuario
    $usuario = new Usuario;
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
          if($resultado) {
            Usuario::setAlerta('exito', 'Usuario creado correctamente');
            header('Location: /dashboard/usuarios');
          }
        }

      }
    }

    $alertas = Usuario::getAlertas();

    $router->render('dashboard/usuarios/index', [
      'usuario' => $usuario,
      'alertas' => $alertas
    ]);
  }
}