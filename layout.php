<?php
function head($title) {
  $u = user(); $cur = basename($_SERVER['SCRIPT_NAME']);
  $menu = [
    ['dashboard.php','Dasbor','bi-grid','admin,rt,warga'],
    ['warga.php','Data Warga','bi-people','admin,rt'],
    ['akun.php','Akun Pengguna','bi-person-lock','admin'],
    ['pengumuman.php','Pengumuman','bi-megaphone','admin,rt,warga'],
    ['surat.php','Surat Pengantar','bi-envelope-paper','admin,rt,warga'],
    ['tagihan.php','Iuran & Pembayaran','bi-wallet2','admin,rt,warga'],
    ['kas.php','Kas RT','bi-bank','admin,rt,warga'],
    ['aduan.php','Aduan & Aspirasi','bi-chat-left-text','admin,rt,warga'],
  ];
  $label = ['admin'=>'Administrator','rt'=>'Pengurus RT','warga'=>'Warga'][$u['role']];
?>
<!doctype html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | <?= NAMA_RT ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet">
</head><body>
<div class="d-flex">
  <aside class="offcanvas-md offcanvas-start sidebar no-print" id="sb" tabindex="-1">
    <div class="p-3">
      <div class="plate"><?= NAMA_RT ?></div>
      <div class="small mt-2 opacity-75"><?= e($u['nama']) ?> &middot; <?= $label ?></div>
    </div>
    <nav class="nav flex-column">
      <?php foreach ($menu as [$f,$t,$i,$r]) if (in_array($u['role'], explode(',', $r))): ?>
        <a class="nav-link <?= $cur === $f ? 'active' : '' ?>" href="<?= $f ?>"><i class="bi <?= $i ?> me-2"></i><?= $t ?></a>
      <?php endif; ?>
      <a class="nav-link mt-3" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i>Keluar</a>
    </nav>
  </aside>
  <main class="flex-grow-1 p-3 p-md-4">
    <button class="btn btn-outline-secondary d-md-none mb-3 no-print" data-bs-toggle="offcanvas" data-bs-target="#sb"><i class="bi bi-list"></i> Menu</button>
    <h1 class="h3 mb-3"><?= e($title) ?></h1>
    <?= flash_show() ?>
<?php }
function foot() { ?>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
<?php }
function stat($label, $val, $href = '#') { ?>
  <div class="col-6 col-lg-3"><a href="<?= $href ?>" class="stat d-block text-decoration-none">
    <div class="small text-secondary"><?= e($label) ?></div><div class="fs-4 fw-bold"><?= $val ?></div></a></div>
<?php }
