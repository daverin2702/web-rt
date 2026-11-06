<?php require 'config.php'; require 'layout.php'; need(['admin','rt']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (isset($_POST['hapus'])) { q('DELETE FROM warga WHERE id=?', [$_POST['hapus']]); flash('Data warga dihapus.'); }
  else {
    $f = ['no_kk','nik_kepala','nama_kk','alamat','jml_anggota','status_hunian','telp','darurat_nama','darurat_telp'];
    $v = array_map(fn($k) => trim($_POST[$k] ?? ''), $f);
    try {
      if (!empty($_POST['id'])) { q('UPDATE warga SET '.implode('=?,', $f).'=? WHERE id=?', [...$v, $_POST['id']]); flash('Data diperbarui.'); }
      else { q('INSERT INTO warga ('.implode(',', $f).') VALUES ('.rtrim(str_repeat('?,', count($f)), ',').')', $v); flash('Data warga ditambahkan.'); }
    } catch (PDOException $x) { flash('Gagal menyimpan. Nomor KK mungkin sudah terdaftar.', 'danger'); }
  }
  header('Location: warga.php'); exit;
}
$ed = isset($_GET['edit']) ? q('SELECT * FROM warga WHERE id=?', [$_GET['edit']])->fetch() : [];
head('Data Warga'); ?>
<div class="panel mb-4"><h2 class="h5"><?= $ed ? 'Ubah data keluarga' : 'Tambah keluarga' ?></h2>
<form method="post" class="row g-2"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($ed['id'] ?? '') ?>">
  <?php foreach ([['no_kk','No. KK'],['nik_kepala','NIK kepala keluarga'],['nama_kk','Nama kepala keluarga'],['alamat','Alamat'],['telp','Telepon'],['darurat_nama','Kontak darurat (nama)'],['darurat_telp','Kontak darurat (telepon)']] as [$k,$l]): ?>
    <div class="col-md-4"><label class="form-label small"><?= $l ?></label><input name="<?= $k ?>" class="form-control" value="<?= e($ed[$k] ?? '') ?>" <?= in_array($k,['telp','darurat_nama','darurat_telp']) ? '' : 'required' ?>></div>
  <?php endforeach; ?>
  <div class="col-md-2"><label class="form-label small">Jumlah anggota</label><input type="number" min="1" name="jml_anggota" class="form-control" value="<?= e($ed['jml_anggota'] ?? 1) ?>" required></div>
  <div class="col-md-2"><label class="form-label small">Status hunian</label><select name="status_hunian" class="form-select">
    <?php foreach (['pemilik','penyewa'] as $s) echo "<option value='$s'".(($ed['status_hunian'] ?? '')===$s?' selected':'').">".ucfirst($s)."</option>"; ?></select></div>
  <div class="col-12"><button class="btn btn-primary">Simpan</button> <?php if ($ed): ?><a href="warga.php" class="btn btn-link">Batal</a><?php endif; ?></div>
</form></div>
<div class="panel table-responsive"><table class="table align-middle">
<thead><tr><th>Kepala keluarga</th><th>Alamat</th><th>Anggota</th><th>Hunian</th><th>Kontak darurat</th><th></th></tr></thead><tbody>
<?php foreach (q('SELECT * FROM warga ORDER BY nama_kk') as $w): ?>
<tr><td><strong><?= e($w['nama_kk']) ?></strong><div class="small text-secondary">KK <?= e($w['no_kk']) ?> &middot; <?= e($w['telp']) ?></div></td>
<td><?= e($w['alamat']) ?></td><td><?= $w['jml_anggota'] ?></td><td><?= ucfirst($w['status_hunian']) ?></td>
<td><?= e($w['darurat_nama']) ?><div class="small text-secondary"><?= e($w['darurat_telp']) ?></div></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="?edit=<?= $w['id'] ?>">Ubah</a>
<form method="post" class="d-inline" onsubmit="return confirm('Hapus keluarga ini beserta tagihan, surat, aduan, dan akunnya?')"><?= csrf_field() ?><button name="hapus" value="<?= $w['id'] ?>" class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php foot();
