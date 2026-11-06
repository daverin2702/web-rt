<?php require 'config.php'; require 'layout.php'; need(['admin','rt','warga']);
// Cetak surat yang sudah disetujui
if (isset($_GET['cetak'])) {
  $s = q('SELECT s.*, w.nama_kk, w.nik_kepala, w.alamat FROM surat s JOIN warga w ON w.id=s.warga_id WHERE s.id=? AND s.status="disetujui"', [$_GET['cetak']])->fetch();
  if (!$s || (!pengurus() && $s['warga_id'] != wid())) die('Surat tidak ditemukan.');
  ?><!doctype html><html lang="id"><head><meta charset="utf-8"><title>Surat <?= e($s['no_surat']) ?></title>
  <style>body{font-family:Georgia,serif;max-width:700px;margin:40px auto;line-height:1.7}h2{text-align:center;margin:0}</style></head><body onload="print()">
  <h2>SURAT <?= strtoupper(e($s['jenis'])) ?></h2><p style="text-align:center">Nomor: <?= e($s['no_surat']) ?></p>
  <p>Yang bertanda tangan di bawah ini, Ketua <?= NAMA_RT ?>, <?= LOKASI ?>, menerangkan bahwa:</p>
  <p>Nama: <strong><?= e($s['nama_kk']) ?></strong><br>NIK: <?= e($s['nik_kepala']) ?><br>Alamat: <?= e($s['alamat']) ?></p>
  <p>Benar warga kami dan surat ini dibuat untuk keperluan: <?= e($s['keperluan']) ?>.</p>
  <p>Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
  <p style="text-align:right">Surabaya, <?= tgl(date('Y-m-d')) ?><br>Ketua <?= NAMA_RT ?><br><br><br>(................................)</p></body></html><?php exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (pengurus() && isset($_POST['id'])) {
    $no = $_POST['status'] === 'disetujui' ? sprintf('%03d/SP/%s/%s', $_POST['id'], date('m'), date('Y')) : null;
    q('UPDATE surat SET status=?, catatan=?, no_surat=? WHERE id=?', [$_POST['status'], trim($_POST['catatan']), $no, $_POST['id']]); flash('Status surat diperbarui.');
  } elseif (!pengurus()) {
    q('INSERT INTO surat (warga_id,jenis,keperluan) VALUES (?,?,?)', [wid(), $_POST['jenis'], trim($_POST['keperluan'])]); flash('Permintaan surat dikirim.');
  }
  header('Location: surat.php'); exit;
}
head('Surat Pengantar'); ?>
<?php if (!pengurus()): ?>
<div class="panel mb-4"><h2 class="h5">Ajukan surat</h2>
<form method="post" class="row g-2"><?= csrf_field() ?>
  <div class="col-md-4"><select name="jenis" class="form-select"><?php foreach (['Pengantar RT/RW','Keterangan Domisili','Pengantar KTP','Pengantar KK'] as $j) echo "<option>$j</option>"; ?></select></div>
  <div class="col-md-8"><input name="keperluan" class="form-control" placeholder="Keperluan, misalnya melamar kerja atau pindah alamat" required></div>
  <div class="col-12"><button class="btn btn-primary">Kirim permintaan</button></div>
</form></div>
<?php endif; ?>
<div class="panel table-responsive"><table class="table align-middle"><thead><tr><th>Tanggal</th><?php if (pengurus()) echo '<th>Pemohon</th>'; ?><th>Jenis & keperluan</th><th>Status</th><th></th></tr></thead><tbody>
<?php $rows = pengurus() ? q('SELECT s.*, w.nama_kk FROM surat s JOIN warga w ON w.id=s.warga_id ORDER BY s.id DESC') : q('SELECT s.*, "" nama_kk FROM surat s WHERE warga_id=? ORDER BY s.id DESC', [wid()]);
foreach ($rows as $s): ?>
<tr><td><?= tgl($s['created_at']) ?></td><?php if (pengurus()) echo '<td>'.e($s['nama_kk']).'</td>'; ?>
<td><strong><?= e($s['jenis']) ?></strong><div class="small text-secondary"><?= e($s['keperluan']) ?></div><?php if ($s['catatan']) echo '<div class="small">Catatan: '.e($s['catatan']).'</div>'; ?></td>
<td><?= badge($s['status']) ?></td>
<td><?php if (pengurus()): ?>
  <form method="post" class="d-flex gap-1"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $s['id'] ?>">
  <select name="status" class="form-select form-select-sm"><?php foreach (['diajukan','disetujui','ditolak'] as $o) echo "<option".($s['status']===$o?' selected':'').">$o</option>"; ?></select>
  <input name="catatan" class="form-control form-control-sm" placeholder="Catatan" value="<?= e($s['catatan']) ?>"><button class="btn btn-sm btn-primary">Simpan</button></form>
<?php endif; if ($s['status']==='disetujui'): ?><a class="btn btn-sm btn-outline-primary mt-1" target="_blank" href="?cetak=<?= $s['id'] ?>">Cetak</a><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php foot();
