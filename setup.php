<?php require 'config.php';
// Jalankan sekali setelah database.sql diimpor, lalu HAPUS file ini.
if (q('SELECT COUNT(*) FROM users')->fetchColumn() > 0) die('Sudah pernah dijalankan. Hapus setup.php.');
q("INSERT INTO warga (no_kk,nik_kepala,nama_kk,alamat,jml_anggota,status_hunian,telp,darurat_nama,darurat_telp) VALUES
 ('3578010101010001','3578010101800001','Budi Santoso','Jl. Mawar No. 12',4,'pemilik','081234567890','Siti Santoso','081298765432'),
 ('3578010101010002','3578010101850002','Andi Wijaya','Jl. Melati No. 7',3,'penyewa','081211112222','Rina Wijaya','081233334444')");
$h = fn($p) => password_hash($p, PASSWORD_DEFAULT);
q("INSERT INTO users (username,password,nama,role,warga_id) VALUES (?,?,?,?,NULL),(?,?,?,?,NULL),(?,?,?,?,1)",
  ['admin',$h('admin123'),'Administrator','admin','rt',$h('rt12345'),'Ketua RT','rt','budi',$h('warga123'),'Budi Santoso','warga']);
q("INSERT INTO pengumuman (judul,isi,kategori) VALUES
 ('Kerja bakti bersihkan saluran air','Minggu pagi pukul 06.00, kumpul di pos ronda. Bawa sapu dan cangkul.','kerja bakti'),
 ('Jadwal piket siskamling bulan ini','Jadwal lengkap ditempel di pos ronda. Mohon tukar jadwal lewat ketua RT.','siskamling')");
q("INSERT INTO kas (tanggal,tipe,kategori,alokasi,keterangan,nominal) VALUES
 (CURDATE(),'masuk','Saldo awal','Kas umum','Saldo awal pembukuan',1000000),
 (CURDATE(),'masuk','Donasi','Pembangunan saung','Donasi awal warga',500000)");
q("INSERT INTO tagihan (warga_id,jenis,periode,alokasi,nominal) VALUES (1,'Iuran bulanan',?, 'Kas umum',50000),(2,'Iuran bulanan',?, 'Kas umum',50000)", [date('Y-m'), date('Y-m')]);
echo 'Selesai. Akun: admin/admin123, rt/rt12345, budi/warga123. Segera hapus setup.php dan ganti semua kata sandi.';
