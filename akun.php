<?php require 'config.php'; require 'layout.php'; need(['admin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (isset($_POST['hapus'])) {
    if ($_POST['hapus'] == user()['id']) flash('Akun yang sedang dipakai tidak bisa dihapus.', 'danger');
    else { q('DELETE FROM users WHERE id=?', [$_POST['hapus']]); flash('Akun dihapus.'); }
  } elseif (isset($_POST['reset'])) {
    q('UPDATE users SET password=? WHERE id=?', [password_hash($_POST['pw'], PASSWORD_DEFAULT), $_POST['reset']]); flash('Kata sandi diganti.');
  } else {
    $w = $_POST['role'] === 'warga' ? ($_POST['warga_id'] ?: null) : null;
    try { q('INSERT INTO users (username,password,nama,role,warga_id) VALUES (?,?,?,?,?)',
      [trim($_POST['username']), password_hash($_POST['password'], PASSWORD_DEFAULT), trim($_POST['nama']), $_POST['role'], $w]); flash('Akun dibuat.'); }
    catch (PDOException $x) { flash('Username sudah dipakai.', 'danger'); }
  }
  header('Location: akun.php'); exit;
}
head('Akun Pengguna'); ?>
<div class="panel mb-4"><h2 class="h5">Buat akun</h2>
<form method="post" class="row g-2"><?= csrf_field() ?>
  <div class="col-md-3"><input name="nama" class="form-control" placeholder="Nama lengkap" required></div>
  <div class="col-md-2"><input name="username" class="form-control" placeholder="Username" required></div>
  <div class="col-md-2"><input name="password" type="password" class="form-control" placeholder="Kata sandi" required minlength="6"></div>
  <div class="col-md-2"><select name="role" class="form-select"><option value="warga">Warga</option><option value="rt">Pengurus RT</option><option value="admin">Administrator</option></select></div>
  <div class="col-md-3"><select name="warga_id" class="form-select"><option value="">Keluarga (untuk akun warga)</option>
    <?php foreach (q('SELECT id,nama_kk FROM warga ORDER BY nama_kk') as $w) echo "<option value='{$w['id']}'>".e($w['nama_kk'])."</option>"; ?></select></div>
  <div class="col-12"><button class="btn btn-primary">Buat akun</button></div>
</form></div>
<div class="panel table-responsive"><table class="table align-middle"><thead><tr><th>Nama</th><th>Username</th><th>Peran</th><th>Ganti kata sandi</th><th></th></tr></thead><tbody>
<?php foreach (q('SELECT * FROM users ORDER BY role,nama') as $a): ?>
<tr><td><?= e($a['nama']) ?></td><td><?= e($a['username']) ?></td><td><?= e($a['role']) ?></td>
<td><form method="post" class="d-flex gap-1"><?= csrf_field() ?><input name="pw" type="password" minlength="6" class="form-control form-control-sm" placeholder="Sandi baru" required><button name="reset" value="<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary">Ganti</button></form></td>
<td><form method="post" onsubmit="return confirm('Hapus akun ini?')"><?= csrf_field() ?><button name="hapus" value="<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php foot();
