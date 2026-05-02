<?php

namespace Controllers;

use MVC\Router;

class CobroPorHoraController {
  public static function index(Router $router) {
    session_start();
    isAuth();
    $router->render('dashboard/cobroporhora/index', [
      'titulo' => 'Cobro por hora'
    ]);
  }
}