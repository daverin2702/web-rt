<?php require 'config.php'; require 'layout.php'; need(['admin','rt','warga']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && pengurus()) {
  csrf_check();
  if (isset($_POST['hapus'])) { q('DELETE FROM kas WHERE id=? AND tagihan_id IS NULL', [$_POST['hapus']]); flash('Catatan dihapus.'); }
  else { q('INSERT INTO kas (tanggal,tipe,kategori,alokasi,keterangan,nominal) VALUES (?,?,?,?,?,?)',
    [$_POST['tanggal'], $_POST['tipe'], trim($_POST['kategori']), trim($_POST['alokasi']), trim($_POST['keterangan']), (int)$_POST['nominal']]); flash('Catatan kas disimpan.'); }
  header('Location: kas.php'); exit;
}
$th = (int)($_GET['tahun'] ?? date('Y'));
$sum = q("SELECT COALESCE(SUM(IF(tipe='masuk',nominal,0)),0) m, COALESCE(SUM(IF(tipe='keluar',nominal,0)),0) k FROM kas WHERE YEAR(tanggal)=?", [$th])->fetch();
head('Kas RT '.$th); ?>
<div class="no-print mb-3 d-flex gap-2 align-items-center">
  <form class="d-flex gap-2"><select name="tahun" class="form-select" onchange="this.form.submit()">
    <?php foreach (q('SELECT DISTINCT YEAR(tanggal) y FROM kas UNION SELECT YEAR(CURDATE()) ORDER BY y DESC') as $y) echo "<option".($y['y']==$th?' selected':'').">{$y['y']}</option>"; ?></select></form>
  <button class="btn btn-outline-primary" onclick="print()"><i class="bi bi-printer"></i> Cetak laporan tahunan</button>
</div>
<div class="row g-3 mb-4">
  <?php stat('Pemasukan '.$th, rp($sum['m'])); stat('Pengeluaran '.$th, rp($sum['k'])); stat('Selisih tahun ini', rp($sum['m']-$sum['k'])); stat('Saldo kas saat ini', rp(saldo())); ?>
</div>
<div class="panel mb-4 table-responsive"><h2 class="h5">Dana per alokasi</h2><table class="table"><thead><tr><th>Alokasi</th><th>Masuk</th><th>Keluar</th><th>Sisa</th></tr></thead><tbody>
<?php foreach (q("SELECT alokasi, SUM(IF(tipe='masuk',nominal,0)) m, SUM(IF(tipe='keluar',nominal,0)) k FROM kas GROUP BY alokasi ORDER BY alokasi") as $a): ?>
<tr><td><?= e($a['alokasi']) ?></td><td><?= rp($a['m']) ?></td><td><?= rp($a['k']) ?></td><td><strong><?= rp($a['m']-$a['k']) ?></strong></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php if (pengurus()): ?>
<div class="panel mb-4 no-print"><h2 class="h5">Catat pemasukan / pengeluaran</h2>
<form method="post" class="row g-2"><?= csrf_field() ?>
  <div class="col-md-2"><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
  <div class="col-md-2"><select name="tipe" class="form-select"><option value="keluar">Pengeluaran</option><option value="masuk">Pemasukan</option></select></div>
  <div class="col-md-2"><input name="kategori" class="form-control" placeholder="Kategori" required></div>
  <div class="col-md-2"><input name="alokasi" class="form-control" value="Kas umum" required></div>
  <div class="col-md-2"><input type="number" name="nominal" min="1" class="form-control" placeholder="Nominal" required></div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Simpan</button></div>
  <div class="col-12"><input name="keterangan" class="form-control" placeholder="Keterangan, misalnya beli semen untuk saung"></div>
</form></div>
<?php endif; ?>
<div class="panel table-responsive"><h2 class="h5">Mutasi <?= $th ?></h2><table class="table align-middle"><thead><tr><th>Tanggal</th><th>Keterangan</th><th>Alokasi</th><th class="text-end">Nominal</th><th class="no-print"></th></tr></thead><tbody>
<?php foreach (q('SELECT * FROM kas WHERE YEAR(tanggal)=? ORDER BY tanggal DESC, id DESC', [$th]) as $k): ?>
<tr><td><?= tgl($k['tanggal']) ?></td><td><?= e($k['keterangan'] ?: $k['kategori']) ?><div class="small text-secondary"><?= e($k['kategori']) ?></div></td><td><?= e($k['alokasi']) ?></td>
<td class="text-end <?= $k['tipe']==='masuk' ? 'text-success' : 'text-danger' ?>"><?= $k['tipe']==='masuk' ? '+' : '-' ?><?= rp($k['nominal']) ?></td>
<td class="no-print"><?php if (pengurus() && !$k['tagihan_id']): ?><form method="post" onsubmit="return confirm('Hapus catatan ini?')"><?= csrf_field() ?><button name="hapus" value="<?= $k['id'] ?>" class="btn btn-sm btn-outline-danger">Hapus</button></form><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php foot();
