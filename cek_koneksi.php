<?php
// Halaman untuk memeriksa koneksi database. Hanya bisa dibuka dari komputer server.
// Hapus file ini setelah aplikasi berjalan normal.
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) { http_response_code(403); exit('Hanya bisa dibuka dari komputer server.'); }
require __DIR__ . '/db.php';
$rows = [];
function cek($nama, $ok, $info = '') { global $rows; $rows[] = [$nama, $ok, $info]; }

cek('Versi PHP', version_compare(PHP_VERSION, '7.4', '>='), PHP_VERSION . ' (minimal 7.4)');
cek('Ekstensi PDO MySQL', extension_loaded('pdo_mysql'), 'diperlukan untuk koneksi database');
cek('Ekstensi GD', extension_loaded('gd'), 'dipakai merapikan foto yang diunggah');

try {
  $pdo = db();
  cek('Koneksi ke MySQL', true, DB_USER . '@' . DB_HOST . ':' . DB_PORT . ', database ' . DB_NAME);
  foreach (['barang', 'pelanggan', 'sewa', 'admin'] as $t) {
    try { cek("Tabel $t", true, $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn() . ' baris'); }
    catch (PDOException $e) { cek("Tabel $t", false, 'tidak ditemukan, import database.sql'); }
  }
  foreach (['barang', 'pelanggan'] as $t) {
    try { $pdo->query("SELECT foto FROM `$t` LIMIT 1"); cek("Kolom foto di $t", true); }
    catch (PDOException $e) { cek("Kolom foto di $t", false, 'belum ada, import foto.sql'); }
  }
} catch (PDOException $e) {
  cek('Koneksi ke MySQL', false, $e->getMessage() . ' | Periksa config.php dan pastikan MySQL di XAMPP aktif.');
}
$w = is_dir(__DIR__ . '/uploads') ? is_writable(__DIR__ . '/uploads') : is_writable(__DIR__);
cek('Folder bisa ditulis (untuk foto)', $w, 'folder uploads/ dibuat otomatis');
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cek koneksi</title><link rel="stylesheet" href="style.css"></head>
<body><div class="wrap"><h1 style="font-size:22px">Cek koneksi dan pengaturan</h1>
<table style="border-collapse:collapse;width:100%;background:var(--sf)">
<?php foreach ($rows as [$n, $ok, $i]): ?>
<tr><td style="padding:8px;border-bottom:1px solid var(--line)"><?= $ok ? '✔' : '✘' ?> <b><?= htmlspecialchars($n) ?></b></td>
<td style="padding:8px;border-bottom:1px solid var(--line);color:<?= $ok ? 'var(--mute)' : 'var(--bad)' ?>"><?= htmlspecialchars($i) ?></td></tr>
<?php endforeach; ?>
</table>
<p class="mute">Hapus file <code>cek_koneksi.php</code> setelah semuanya ✔.</p></div></body></html>
