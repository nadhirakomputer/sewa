<?php
require __DIR__ . '/db.php';
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rp($n) { return 'Rp' . number_format((int)$n, 0, ',', '.'); }
$items = []; $err = '';
try {
  $items = db()->query("SELECT b.nama,b.jenis,b.foto,b.tarif_per_hari,b.denda_per_hari,b.catatan FROM barang b
    WHERE NOT EXISTS (SELECT 1 FROM sewa s WHERE s.barang_id=b.id AND s.tgl_kembali IS NULL)
    ORDER BY b.jenis,b.nama")->fetchAll();
} catch (PDOException $e) { $err = 'Daftar barang belum bisa dimuat. Hubungi kami untuk info ketersediaan.'; }
$jenis = array_values(array_unique(array_column($items, 'jenis')));
$f = $_GET['jenis'] ?? '';
$show = $f === '' ? $items : array_values(array_filter($items, fn($i) => $i['jenis'] === $f));
$wa = '62' . ltrim(preg_replace('/\D/', '', CONTACT_PHONE), '0');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h(SITE_NAME) ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
<header>
  <div><h1><?= h(SITE_NAME) ?></h1><p class="mute" style="margin:4px 0 0">Laptop, printer, dan perlengkapan lain yang tersedia untuk disewa.</p></div>
  <a class="btn g" href="login.php">Masuk admin</a>
</header>

<h2>Tersedia sekarang (<?= count($items) ?>)</h2>
<?php if ($jenis): ?>
<nav class="chips" aria-label="Filter jenis barang">
  <a href="index.php" class="<?= $f === '' ? 'on' : '' ?>">Semua</a>
  <?php foreach ($jenis as $j): ?><a href="?jenis=<?= urlencode($j) ?>" class="<?= $f === $j ? 'on' : '' ?>"><?= h($j) ?></a><?php endforeach; ?>
</nav>
<?php endif; ?>

<?php if ($err): ?><div class="emp"><?= h($err) ?></div>
<?php elseif (!$show): ?><div class="emp">Belum ada barang yang tersedia saat ini. Hubungi kami untuk info terbaru.</div>
<?php else: ?>
<div class="grid">
<?php foreach ($show as $i): ?>
  <article class="card">
    <?php if ($i['foto']): ?><img class="ph" src="uploads/barang/<?= h($i['foto']) ?>" alt="<?= h($i['nama']) ?>" loading="lazy"><?php else: ?><div class="ph">Belum ada foto</div><?php endif; ?>
    <span class="jn"><?= h($i['jenis']) ?></span>
    <h3><?= h($i['nama']) ?></h3>
    <?php if ($i['catatan'] !== ''): ?><div class="mute"><?= h($i['catatan']) ?></div><?php endif; ?>
    <div class="pr"><b><?= rp($i['tarif_per_hari']) ?></b> / hari</div>
    <small>Denda terlambat <?= rp($i['denda_per_hari']) ?> per hari</small>
  </article>
<?php endforeach; ?>
</div>
<?php endif; ?>

<section class="kontak" id="kontak">
  <h2>Hubungi kami untuk menyewa</h2>
  <p><b>Telepon:</b> <a href="tel:<?= h(CONTACT_PHONE) ?>"><?= h(CONTACT_PHONE) ?></a></p>
  <p><b>Alamat:</b> <?= h(CONTACT_ADDRESS) ?></p>
  <div class="act">
    <a class="btn" href="tel:<?= h(CONTACT_PHONE) ?>">Telepon</a>
    <a class="btn g" href="https://wa.me/<?= h($wa) ?>">WhatsApp</a>
  </div>
</section>
<footer>&copy; <?= date('Y') ?> <?= h(SITE_NAME) ?></footer>
</div>
</body>
</html>
