<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_CHARSET']);

function conectarDB(): PDO {
  $dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $_ENV['DB_HOST'],
    $_ENV['DB_NAME'],
    $_ENV['DB_CHARSET']
  );

  $opciones = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try {
    //code...
    $db = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $opciones);
    return $db;
  } catch (PDOException $e) {
    //throw $th;
    error_log($e->getMessage());
    die('Error al conectar con la base de datos');
  }
}
