<?php

function debuguear(mixed $variable) : void {
  echo "<pre>";
  var_dump($variable);
  echo "</pre>";
  exit;
}

function isAuth() : void {
  if(!isset($_SESSION['login'])) {
    header('Location: /');
  }
}

function pagina_actual(string $url): bool {
  return $_SERVER['PATH_INFO'] === $url;
}