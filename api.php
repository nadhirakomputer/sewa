<?php
require __DIR__ . '/db.php';
require __DIR__ . '/lib_foto.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

function out($d, $c = 200) { http_response_code($c); echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }
function q($sql, $p = []) { global $pdo; $s = $pdo->prepare($sql); $s->execute($p); return $s; }
function tgl($s) {
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$s) || !strtotime($s)) out(['error' => 'Format tanggal tidak valid.'], 422);
  return $s;
}

try {
  $pdo = db();
  $a = $_GET['action'] ?? '';
  if (empty($_SESSION['admin'])) out(['error' => 'Sesi habis. Silakan masuk lagi.'], 401);
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') out(['error' => 'Permintaan ditolak.'], 403);
  $d = json_decode(file_get_contents('php://input'), true) ?: [];
  $t = fn($k) => trim((string)($d[$k] ?? ''));
  $n = fn($k) => max(0, (int)($d[$k] ?? 0));

  if ($a === 'all') out([
    'items' => q("SELECT id,kode,nama,jenis AS kat,tarif_per_hari AS rate,denda_per_hari AS fine,catatan AS cat,foto FROM barang ORDER BY nama")->fetchAll(),
    'customers' => q("SELECT id,nama,telepon AS hp,no_identitas AS idn,alamat,foto FROM pelanggan ORDER BY nama")->fetchAll(),
    'rentals' => q("SELECT id,barang_id AS itemId,pelanggan_id AS custId,tgl_sewa AS start,tgl_jatuh_tempo AS due,tgl_kembali AS returned,tarif_per_hari AS rate,denda_per_hari AS fine FROM sewa ORDER BY id")->fetchAll(),
  ]);
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(['error' => 'Metode tidak diizinkan.'], 405);

  switch ($a) {
    case 'item_add': case 'item_update':
      if ($t('nama') === '' || $t('kat') === '') out(['error' => 'Nama dan jenis barang wajib diisi.'], 422);
      $p = [$t('kode') ?: null, $t('nama'), $t('kat'), $n('rate'), $n('fine'), $t('cat')];
      if ($a === 'item_add') { q("INSERT INTO barang (kode,nama,jenis,tarif_per_hari,denda_per_hari,catatan) VALUES (?,?,?,?,?,?)", $p); $id = (int)$pdo->lastInsertId(); }
      else { q("UPDATE barang SET kode=?,nama=?,jenis=?,tarif_per_hari=?,denda_per_hari=?,catatan=? WHERE id=?", array_merge($p, [$n('id')])); $id = $n('id'); }
      foto_set('barang', $id, $d);
      break;
    case 'item_delete':
      $o = q("SELECT foto FROM barang WHERE id=?", [$n('id')])->fetchColumn();
      q("DELETE FROM barang WHERE id=?", [$n('id')]); hapus_file('barang', $o); break;
    case 'cust_add': case 'cust_update':
      if ($t('nama') === '') out(['error' => 'Nama pelanggan wajib diisi.'], 422);
      $p = [$t('nama'), $t('hp'), $t('idn'), $t('alamat')];
      if ($a === 'cust_add') { q("INSERT INTO pelanggan (nama,telepon,no_identitas,alamat) VALUES (?,?,?,?)", $p); $id = (int)$pdo->lastInsertId(); }
      else { q("UPDATE pelanggan SET nama=?,telepon=?,no_identitas=?,alamat=? WHERE id=?", array_merge($p, [$n('id')])); $id = $n('id'); }
      foto_set('pelanggan', $id, $d);
      break;
    case 'cust_delete':
      $o = q("SELECT foto FROM pelanggan WHERE id=?", [$n('id')])->fetchColumn();
      q("DELETE FROM pelanggan WHERE id=?", [$n('id')]); hapus_file('pelanggan', $o); break;
    case 'rent_add':
      $s = tgl($t('start')); $u = tgl($t('due'));
      if (strtotime($u) < strtotime($s)) out(['error' => 'Tanggal kembali tidak boleh sebelum tanggal sewa.'], 422);
      if (q("SELECT COUNT(*) FROM sewa WHERE barang_id=? AND tgl_kembali IS NULL", [$n('itemId')])->fetchColumn()) out(['error' => 'Barang sedang disewa.'], 422);
      $r = q("INSERT INTO sewa (barang_id,pelanggan_id,tgl_sewa,tgl_jatuh_tempo,tarif_per_hari,denda_per_hari) SELECT id,?,?,?,tarif_per_hari,denda_per_hari FROM barang WHERE id=?", [$n('custId'), $s, $u, $n('itemId')]);
      if (!$r->rowCount()) out(['error' => 'Barang tidak ditemukan.'], 422);
      break;
    case 'rent_return':
      $r = q("SELECT tgl_sewa FROM sewa WHERE id=?", [$n('id')])->fetch();
      if (!$r) out(['error' => 'Data sewa tidak ditemukan.'], 404);
      $k = tgl($t('returned'));
      if (strtotime($k) < strtotime($r['tgl_sewa'])) out(['error' => 'Tanggal kembali tidak boleh sebelum tanggal sewa.'], 422);
      q("UPDATE sewa SET tgl_kembali=? WHERE id=?", [$k, $n('id')]);
      break;
    case 'rent_delete': q("DELETE FROM sewa WHERE id=?", [$n('id')]); break;
    case 'reset':
      foreach (['barang', 'pelanggan'] as $t2) foreach (q("SELECT foto FROM $t2 WHERE foto IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN) as $f) hapus_file($t2, $f);
      q("DELETE FROM sewa"); q("DELETE FROM pelanggan"); q("DELETE FROM barang"); break;
    default: out(['error' => 'Aksi tidak dikenal.'], 404);
  }
  out(['ok' => true]);
} catch (PDOException $e) {
  $c = $e->errorInfo[1] ?? 0;
  $m = [1062 => 'Kode barang sudah dipakai.', 1451 => 'Data ini masih punya riwayat sewa.'][$c]
    ?? (in_array($c, [1049, 1045, 2002]) ? 'Tidak bisa terhubung ke database. Periksa config.php dan pastikan MySQL aktif.' : 'Kesalahan database.');
  out(['error' => $m], 500);
}
