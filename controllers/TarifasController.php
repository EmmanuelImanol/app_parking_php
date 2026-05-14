<?php

namespace Controllers;

use Model\Tarifa;

class TarifasController {
  public static function obtenerTarifas() {
    $tarifas = Tarifa::all();
    echo json_encode($tarifas);
  }
}