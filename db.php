<?php
require __DIR__ . '/config.php';
date_default_timezone_set('Asia/Jakarta');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);

function db() {
  static $p = null;
  if (!$p) $p = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  return $p;
}
