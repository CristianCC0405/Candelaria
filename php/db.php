<?php
// config de DB - editar con tus credenciales Hostinger
$DB_HOST = 'localhost';
$DB_NAME = 'u444196209_candelaria';
$DB_USER = 'u444196209_cristiancc';
$DB_PASS = 'Cristian_75153782';

try {
  $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (Exception $e) {
  die("DB error: " . $e->getMessage());
}
