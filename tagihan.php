<?php require 'config.php'; require 'layout.php'; need(['admin','rt','warga']);
const METODE = ['Transfer BCA','Transfer Mandiri','Transfer BRI','Transfer BNI','GoPay','OVO','DANA','ShopeePay'];
// Titik integrasi payment gateway (Midtrans/Xendit): panggil lunasi() dari callback webhook setelah pembayaran terkonfirmasi.
function lunasi($id, $metode) {
  $t = q("SELECT t.*, w.nama_kk FROM tagihan t JOIN warga w ON w.id=t.warga_id WHERE t.id=? AND t.status='belum'", [$id])->fetch();
  if (!$t) return false;
  $no = 'STR-'.date('ymd').'-'.str_pad($id, 4, '0', STR_PAD_LEFT);
  q("UPDATE tagihan SET status='lunas', metode=?, no_struk=?, tgl_bayar=NOW() WHERE id=?", [$metode, $no, $id]);
  q("INSERT INTO kas (tanggal,tipe,kategori,alokasi,keterangan,nominal,tagihan_id) VALUES (CURDATE(),'masuk',?,?,?,?,?)",
    [$t['jenis'], $t['alokasi'], $t['jenis'].' '.$t['periode'].' - '.$t['nama_kk'], $t['nominal'], $id]);
  return $no;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (pengurus() && isset($_POST['buat'])) {
    $n = 0;
    foreach (q('SELECT id FROM warga')->fetchAll() as $w) {
      q('INSERT INTO tagihan (warga_id,jenis,periode,alokasi,nominal) VALUES (?,?,?,?,?)', [$w['id'], trim($_POST['jenis']), $_POST['periode'], trim($_POST['alokasi']), (int)$_POST['nominal']]); $n++;
    }
    flash("Tagihan dibuat untuk $n keluarga.");
  } elseif (pengurus() && isset($_POST['tunai'])) { lunasi($_POST['tunai'], 'Tunai'); flash('Tagihan ditandai lunas (tunai).'); }
  elseif (!pengurus() && isset($_POST['bayar'])) {
    $t = q('SELECT id FROM tagihan WHERE id=? AND warga_id=?', [$_POST['bayar'], wid()])->fetch();
    $m = in_array($_POST['metode'], METODE) ? $_POST['metode'] : null;
    if ($t && $m && lunasi($t['id'], $m)) flash('Pembayaran berhasil. Struk sudah tersedia.'); else flash('Pembayaran gagal diproses.', 'danger');
  } elseif (!pengurus() && isset($_POST['donasi'])) {
    q("INSERT INTO tagihan (warga_id,jenis,periode,alokasi,nominal) VALUES (?, 'Donasi', ?, ?, ?)", [wid(), date('Y-m'), trim($_POST['alokasi']), max(1000, (int)$_POST['nominal'])]);
    flash('Donasi dibuat. Silakan selesaikan pembayarannya di daftar tagihan.');
  }
  header('Location: tagihan.php'); exit;
}
$alokasi = '<datalist id="al"><option>Kas umum<option>Pembangunan saung<option>Playground<option>Siskamling<option>Kegiatan 17 Agustus</datalist>';
head('Iuran & Pembayaran'); echo $alokasi; ?>
<?php if (pengurus()): ?>
<div class="panel mb-4"><h2 class="h5">Buat tagihan untuk semua keluarga</h2>
<form method="post" class="row g-2"><?= csrf_field() ?>
  <div class="col-md-3"><input name="jenis" class="form-control" value="Iuran bulanan" required></div>
  <div class="col-md-2"><input type="month" name="periode" class="form-control" value="<?= date('Y-m') ?>" required></div>
  <div class="col-md-3"><input name="alokasi" list="al" class="form-control" value="Kas umum" required></div>
  <div class="col-md-2"><input type="number" name="nominal" min="1000" class="form-control" placeholder="Nominal" required></div>
  <div class="col-md-2"><button name="buat" value="1" class="btn btn-primary w-100">Buat tagihan</button></div>
</form></div>
<?php else: ?>
<div class="panel mb-4"><h2 class="h5">Donasi</h2>
<form method="post" class="row g-2"><?= csrf_field() ?>
  <div class="col-md-5"><input name="alokasi" list="al" class="form-control" placeholder="Untuk apa? (mis. Pembangunan saung)" required></div>
  <div class="col-md-4"><input type="number" name="nominal" min="1000" class="form-control" placeholder="Nominal (min. Rp 1.000)" required></div>
  <div class="col-md-3"><button name="donasi" value="1" class="btn btn-primary w-100">Buat donasi</button></div>
</form></div>
<div class="alert alert-info small">Pembayaran di versi ini masih simulasi: tagihan langsung berstatus lunas dan tercatat di kas. Untuk transaksi uang sungguhan, sambungkan payment gateway pada fungsi <code>lunasi()</code>.</div>
<?php endif; ?>
<div class="panel table-responsive"><table class="table align-middle"><thead><tr><th>Periode</th><?php if (pengurus()) echo '<th>Keluarga</th>'; ?><th>Tagihan</th><th>Nominal</th><th>Status</th><th></th></tr></thead><tbody>
<?php $rows = pengurus() ? q('SELECT t.*, w.nama_kk FROM tagihan t JOIN warga w ON w.id=t.warga_id ORDER BY t.id DESC LIMIT 200') : q('SELECT t.*, "" nama_kk FROM tagihan t WHERE warga_id=? ORDER BY t.id DESC', [wid()]);
foreach ($rows as $t): ?>
<tr><td><?= e($t['periode']) ?></td><?php if (pengurus()) echo '<td>'.e($t['nama_kk']).'</td>'; ?>
<td><?= e($t['jenis']) ?><div class="small text-secondary"><?= e($t['alokasi']) ?></div></td><td><?= rp($t['nominal']) ?></td>
<td><?= badge($t['status']) ?><?php if ($t['metode']) echo '<div class="small text-secondary">'.e($t['metode']).'</div>'; ?></td>
<td class="text-nowrap">
<?php if ($t['status'] === 'lunas'): ?><a class="btn btn-sm btn-outline-primary" target="_blank" href="struk.php?id=<?= $t['id'] ?>">Struk</a>
<?php elseif (pengurus()): ?><form method="post" class="d-inline"><?= csrf_field() ?><button name="tunai" value="<?= $t['id'] ?>" class="btn btn-sm btn-outline-success">Lunas (tunai)</button></form>
<?php else: ?><form method="post" class="d-flex gap-1"><?= csrf_field() ?><select name="metode" class="form-select form-select-sm"><?php foreach (METODE as $m) echo "<option>$m</option>"; ?></select><button name="bayar" value="<?= $t['id'] ?>" class="btn btn-sm btn-primary">Bayar</button></form><?php endif; ?>
</td></tr>
<?php endforeach; ?></tbody></table></div>
<?php foot();
