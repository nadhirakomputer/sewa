<?php
// Menampilkan foto pelanggan, hanya untuk admin yang sudah login.
require __DIR__ . '/db.php';
session_start();
if (empty($_SESSION['admin'])) { http_response_code(403); exit; }
$f = basename($_GET['f'] ?? '');
$p = __DIR__ . '/uploads/pelanggan/' . $f;
if (!preg_match('/^p_[a-f0-9]{16}\.jpg$/', $f) || !is_file($p)) { http_response_code(404); exit; }
header('Content-Type: image/jpeg');
header('Cache-Control: private, max-age=86400');
readfile($p);
