<?php

namespace MVC;

class Router {

  public array $rutasGET = [];
  public array $rutasPOST = [];

  public function get(string $url, callable $fn): void {
    $this->rutasGET[$url] = $fn;
  }

  public function post(string $url, callable $fn): void {
    $this->rutasPOST[$url] = $fn;
  }

  public function comprobarRutas(): void {
    $appUrl = $_ENV['APP_URL'] ?? '';

    // ✅ Extraer solo el path de APP_URL para comparar correctamente
    $basePath = parse_url($appUrl, PHP_URL_PATH) ?? '';
    $currentUrl = str_replace(
      $basePath, 
      '', 
      parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    );
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
      $fn = $this->rutasGET[$currentUrl] ?? null;
    } else {
      $fn = $this->rutasPOST[$currentUrl] ?? null;
    }

    if ( $fn ) {
      // Call user fn va a llamar una función cuando no sabemos cual sera
      call_user_func($fn, $this); // This es para pasar argumentos
    } else {
      echo "Página No Encontrada o Ruta no válida 404";
    }
  }

  public function render(string $view, array $datos = []) {
    // Leer lo que le pasamos  a la vista
    foreach ($datos as $key => $value) {
      $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
    }

    ob_start(); // Almacenamiento en memoria durante un momento...

    // entonces incluimos la vista en el layout
    include_once __DIR__ . "/views/$view.php";
    $contenido = ob_get_clean(); // Limpia el Buffer
    // Utilizar el layout de acuerdo a la URL
    $currentUrl = str_replace($_ENV['APP_URL'] ?? '', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    
    if(str_contains($currentUrl, '/dashboard')) {
      include_once __DIR__ . '/views/dashboard-layout.php';
    } else {
      include_once __DIR__ . '/views/layout.php';
    }

  }
}