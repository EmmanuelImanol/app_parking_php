<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class LoginController {
  public static function login(Router $router) {
    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $usuario = new Usuario($_POST);
      $alertas = $usuario->validarLogin();

      if(empty($alertas)) {
        // Verificar que el usuario exista
        $usuario = Usuario::where('email', $usuario->email);

        if(!$usuario) {
          Usuario::setAlerta('error', 'El usuario no existe');
        } else {
          // El Usuario existe
          if(password_verify($_POST['password'], $usuario->password)) {
            // Iniciar Sesión
            session_start();
            $_SESSION['id'] = $usuario->id;
            $_SESSION['clienteId'] = $usuario->clienteId;
            $_SESSION['nombre'] = $usuario->nombre;
            $_SESSION['email'] = $usuario->email;
            $_SESSION['rol'] = $usuario->rol;
            $_SESSION['login'] = true;

            // Redireccionar
            if($_SESSION['rol'] === 'admin') {
              header('Location: ' . base_url('/dashboard'));
            } else {
              header('Location: ' . base_url('/dashboard/cobroporhora'));
            }

          } else {
            Usuario::setAlerta('error', 'Password Incorrecto');
          }
        }
      }
    }

    $alertas = Usuario::getAlertas();

    $router->render('auth/login', [
      'alertas' => $alertas,
    ]);
  }

  public static function logout() {
    session_start();
    $_SESSION = [];
    header('Location: ' . base_url('/'));
  }
}