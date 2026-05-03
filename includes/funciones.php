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

function base_url(string $path = ''): string {
  return ($_ENV['APP_URL'] ?? '') . $path;
}

function pagina_actual(string $url): bool {
  $currentUrl = str_replace($_ENV['APP_URL'] ?? '', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
  return $currentUrl === $url;
}