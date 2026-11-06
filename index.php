<?php require 'config.php';
if (user()) { header('Location: dashboard.php'); exit; } ?>
<!doctype html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Selamat datang | <?= NAMA_RT ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet">
</head><body>
<section class="hero">
  <nav class="container d-flex justify-content-between align-items-center py-3">
    <span class="fw-bold"><?= LOKASI ?></span>
    <div class="dropdown">
      <button class="btn btn-warning dropdown-toggle fw-semibold" data-bs-toggle="dropdown">Masuk</button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="login.php?role=warga"><i class="bi bi-house-door me-2"></i>Warga</a></li>
        <li><a class="dropdown-item" href="login.php?role=rt"><i class="bi bi-person-badge me-2"></i>Pengurus RT</a></li>
        <li><a class="dropdown-item" href="login.php?role=admin"><i class="bi bi-shield-lock me-2"></i>Administrator</a></li>
      </ul>
    </div>
  </nav>
  <div class="container py-5">
    <div class="plate mb-4"><?= NAMA_RT ?></div>
    <h1 class="display-5 fw-bold col-lg-8">Urusan lingkungan, kas, dan surat dalam satu tempat.</h1>
    <p class="lead col-lg-6 opacity-75">Bayar iuran, minta surat pengantar, baca pengumuman, dan lihat ke mana uang kas dipakai tanpa perlu mengetuk pintu pengurus.</p>
    <a href="login.php?role=warga" class="btn btn-warning btn-lg fw-semibold">Masuk sebagai warga</a>
  </div>
</section>
<section class="container py-5 fitur">
  <div class="row g-4">
    <?php foreach ([
      ['bi-bank','Kas RT yang terbuka','Pemasukan, pengeluaran, dan dana khusus seperti saung atau playground bisa dilihat semua warga.'],
      ['bi-people','Data warga terpusat','Kepala keluarga, jumlah anggota, status hunian, dan kontak darurat tercatat rapi.'],
      ['bi-envelope-paper','Surat pengantar mandiri','Ajukan surat RT/RW, domisili, atau pengantar KTP/KK dari rumah.'],
      ['bi-megaphone','Pengumuman & siskamling','Jadwal kerja bakti, piket ronda, dan laporan keamanan di satu papan.'],
      ['bi-wallet2','Bayar iuran online','Transfer bank atau e-wallet, riwayat tagihan, dan struk digital otomatis.'],
      ['bi-chat-left-text','Aduan & aspirasi','Laporkan lampu jalan mati atau jalan rusak, lalu pantau statusnya.'],
    ] as [$i,$t,$d]): ?>
    <div class="col-md-6 col-lg-4 d-flex gap-3"><i class="bi <?= $i ?>"></i><div><h2 class="h6 mb-1"><?= $t ?></h2><p class="text-secondary mb-0"><?= $d ?></p></div></div>
    <?php endforeach; ?>
  </div>
</section>
<footer class="container pb-4 text-secondary small"><?= NAMA_RT ?> &middot; <?= LOKASI ?></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
