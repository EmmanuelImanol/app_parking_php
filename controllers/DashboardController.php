<?php

namespace Controllers;

use MVC\Router;

class DashboardController {
  public static function index(Router $router) {
    session_start();
    isAuth();
    isAdmin();
    $router->render('dashboard/index', [
      'titulo' => 'Panel de administración'
    ]);
  }
}