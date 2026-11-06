<?php require 'config.php';
$role = $_GET['role'] ?? 'warga';
if (!in_array($role, ['admin','rt','warga'])) $role = 'warga';
$judul = ['admin'=>'Login Administrator','rt'=>'Login Pengurus RT','warga'=>'Login Warga'][$role];
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $u = q('SELECT * FROM users WHERE username=? AND role=?', [trim($_POST['username']), $role])->fetch();
  if ($u && password_verify($_POST['password'], $u['password'])) {
    session_regenerate_id(true);
    $_SESSION['user'] = ['id'=>$u['id'],'nama'=>$u['nama'],'role'=>$u['role'],'warga_id'=>$u['warga_id']];
    header('Location: dashboard.php'); exit;
  }
  $err = 'Username atau kata sandi salah, atau akun ini bukan akun '.['admin'=>'administrator','rt'=>'pengurus RT','warga'=>'warga'][$role].'.';
}
?>
<!doctype html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $judul ?> | <?= NAMA_RT ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet">
</head><body>
<div class="login-wrap"><div class="panel" style="width:100%;max-width:400px">
  <div class="plate mb-3"><?= NAMA_RT ?></div>
  <h1 class="h4 mb-3"><?= $judul ?></h1>
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <form method="post"><?= csrf_field() ?>
    <label class="form-label">Username</label><input name="username" class="form-control mb-3" required autofocus>
    <label class="form-label">Kata sandi</label><input name="password" type="password" class="form-control mb-3" required>
    <button class="btn btn-primary w-100">Masuk</button>
  </form>
  <div class="small mt-3 d-flex justify-content-between">
    <a href="index.php">Kembali ke beranda</a>
    <span>
      <?php foreach (['warga'=>'Warga','rt'=>'RT','admin'=>'Admin'] as $k=>$v) if ($k !== $role) echo "<a class='ms-2' href='login.php?role=$k'>$v</a>"; ?>
    </span>
  </div>
</div></div></body></html>
