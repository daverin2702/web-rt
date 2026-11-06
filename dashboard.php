<?php require 'config.php'; require 'layout.php'; need(['admin','rt','warga']);
$u = user(); head('Dasbor');
if (pengurus()) {
  echo '<div class="row g-3 mb-4">';
  stat('Kepala keluarga', q('SELECT COUNT(*) FROM warga')->fetchColumn(), 'warga.php');
  stat('Total jiwa', q('SELECT COALESCE(SUM(jml_anggota),0) FROM warga')->fetchColumn(), 'warga.php');
  stat('Saldo kas', rp(saldo()), 'kas.php');
  stat('Tagihan belum lunas', q("SELECT COUNT(*) FROM tagihan WHERE status='belum'")->fetchColumn(), 'tagihan.php');
  stat('Surat menunggu', q("SELECT COUNT(*) FROM surat WHERE status='diajukan'")->fetchColumn(), 'surat.php');
  stat('Aduan belum selesai', q("SELECT COUNT(*) FROM aduan WHERE status<>'selesai'")->fetchColumn(), 'aduan.php');
  echo '</div>';
} else {
  $t = q("SELECT COUNT(*) c, COALESCE(SUM(nominal),0) s FROM tagihan WHERE warga_id=? AND status='belum'", [wid()])->fetch();
  echo '<div class="row g-3 mb-4">';
  stat('Tagihan belum dibayar', $t['c'].' ('.rp($t['s']).')', 'tagihan.php');
  stat('Saldo kas RT', rp(saldo()), 'kas.php');
  stat('Surat diajukan', q("SELECT COUNT(*) FROM surat WHERE warga_id=? AND status='diajukan'", [wid()])->fetchColumn(), 'surat.php');
  stat('Aduan saya yang berjalan', q("SELECT COUNT(*) FROM aduan WHERE warga_id=? AND status<>'selesai'", [wid()])->fetchColumn(), 'aduan.php');
  echo '</div>';
}
?>
<div class="row g-3">
  <div class="col-lg-7"><div class="panel">
    <h2 class="h5">Pengumuman terbaru</h2>
    <?php foreach (q('SELECT * FROM pengumuman ORDER BY id DESC LIMIT 4') as $p): ?>
      <div class="border-top py-2"><span class="badge text-bg-secondary"><?= e($p['kategori']) ?></span>
      <strong class="ms-1"><?= e($p['judul']) ?></strong><div class="small text-secondary"><?= tgl($p['created_at']) ?></div></div>
    <?php endforeach; ?>
  </div></div>
  <div class="col-lg-5"><div class="panel">
    <h2 class="h5">Kas terakhir</h2>
    <?php foreach (q('SELECT * FROM kas ORDER BY tanggal DESC, id DESC LIMIT 5') as $k): ?>
      <div class="d-flex justify-content-between border-top py-2"><span><?= e($k['keterangan'] ?: $k['kategori']) ?><div class="small text-secondary"><?= e($k['alokasi']) ?></div></span>
      <span class="<?= $k['tipe']==='masuk' ? 'text-success' : 'text-danger' ?>"><?= $k['tipe']==='masuk' ? '+' : '-' ?><?= rp($k['nominal']) ?></span></div>
    <?php endforeach; ?>
  </div></div>
</div>
<?php foot();
