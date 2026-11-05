<?php
require __DIR__ . '/db.php';
session_start();
if (!empty($_SESSION['admin'])) { header('Location: admin.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $s = db()->prepare('SELECT password_hash FROM admin WHERE username=?');
    $s->execute([trim($_POST['username'] ?? '')]);
    $hash = $s->fetchColumn();
    if ($hash && password_verify($_POST['password'] ?? '', $hash)) {
      session_regenerate_id(true);
      $_SESSION['admin'] = 1;
      header('Location: admin.php'); exit;
    }
    sleep(1);
    $err = 'Username atau password salah.';
  } catch (PDOException $e) { $err = 'Tidak bisa terhubung ke database. Pastikan MySQL aktif dan database sudah diimpor.'; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk admin</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="box">
  <h1 style="font-size:22px">Masuk admin</h1>
  <form method="post" autocomplete="off">
    <label for="u">Username</label><input id="u" name="username" required autofocus>
    <label for="p">Password</label><input id="p" name="password" type="password" required>
    <?php if ($err): ?><p class="err" role="alert"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <button class="btn" style="width:100%;margin-top:18px" type="submit">Masuk</button>
  </form>
  <p style="text-align:center;margin:16px 0 0"><a href="index.php">Kembali ke halaman depan</a></p>
</main>
</body>
</html>
