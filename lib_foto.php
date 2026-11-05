<?php
// Penyimpanan foto. Foto barang: uploads/barang/ (publik, tampil di halaman depan).
// Foto pelanggan: uploads/pelanggan/ (privat, hanya bisa dilihat admin lewat foto.php).
define('UPLOAD_BASE', __DIR__ . '/uploads/');

function foto_dir($t) {
  $d = UPLOAD_BASE . $t . '/';
  if (!is_dir($d)) {
    mkdir($d, 0755, true);
    if ($t === 'pelanggan') file_put_contents($d . '.htaccess', "Require all denied\n");
    else file_put_contents($d . 'index.html', '');
  }
  return $d;
}
function hapus_file($t, $f) { if ($f) @unlink(foto_dir($t) . basename($f)); }

function simpan_foto($t, $data) {
  $pre = 'data:image/jpeg;base64,';
  if (strncmp($data, $pre, strlen($pre)) !== 0) out(['error' => 'Format foto tidak valid.'], 422);
  $bin = base64_decode(substr($data, strlen($pre)), true);
  if (!$bin || strlen($bin) > 2000000 || !@getimagesizefromstring($bin)) out(['error' => 'Foto tidak valid atau terlalu besar (maks 2 MB).'], 422);
  if (function_exists('imagecreatefromstring') && ($im = @imagecreatefromstring($bin))) { // simpan ulang agar bersih
    ob_start(); imagejpeg($im, null, 85); $bin = ob_get_clean();
  }
  $f = $t[0] . '_' . bin2hex(random_bytes(8)) . '.jpg';
  file_put_contents(foto_dir($t) . $f, $bin);
  return $f;
}

// $t = 'barang' atau 'pelanggan' (sama dengan nama tabel)
function foto_set($t, $id, $d) {
  $old = q("SELECT foto FROM $t WHERE id=?", [$id])->fetchColumn();
  $new = $old;
  if (!empty($d['foto'])) $new = simpan_foto($t, $d['foto']);
  elseif (!empty($d['hapus_foto'])) $new = null;
  if ($new !== $old) { q("UPDATE $t SET foto=? WHERE id=?", [$new, $id]); hapus_file($t, $old); }
}
