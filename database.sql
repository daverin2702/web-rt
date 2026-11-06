CREATE DATABASE IF NOT EXISTS db_rt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_rt;

CREATE TABLE warga (
  id INT AUTO_INCREMENT PRIMARY KEY,
  no_kk VARCHAR(20) NOT NULL UNIQUE,
  nik_kepala VARCHAR(20) NOT NULL,
  nama_kk VARCHAR(100) NOT NULL,
  alamat VARCHAR(200) NOT NULL,
  jml_anggota TINYINT NOT NULL DEFAULT 1,
  status_hunian ENUM('pemilik','penyewa') NOT NULL DEFAULT 'pemilik',
  telp VARCHAR(20),
  darurat_nama VARCHAR(100),
  darurat_telp VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  role ENUM('admin','rt','warga') NOT NULL,
  warga_id INT NULL,
  FOREIGN KEY (warga_id) REFERENCES warga(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pengumuman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  isi TEXT NOT NULL,
  kategori ENUM('kegiatan','kerja bakti','siskamling','keamanan','lainnya') NOT NULL DEFAULT 'kegiatan',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tagihan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  warga_id INT NOT NULL,
  jenis VARCHAR(60) NOT NULL,
  periode VARCHAR(7) NOT NULL,
  alokasi VARCHAR(80) NOT NULL DEFAULT 'Kas umum',
  nominal INT NOT NULL,
  status ENUM('belum','lunas') NOT NULL DEFAULT 'belum',
  metode VARCHAR(40),
  no_struk VARCHAR(30),
  tgl_bayar DATETIME NULL,
  FOREIGN KEY (warga_id) REFERENCES warga(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE kas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE NOT NULL,
  tipe ENUM('masuk','keluar') NOT NULL,
  kategori VARCHAR(60) NOT NULL,
  alokasi VARCHAR(80) NOT NULL DEFAULT 'Kas umum',
  keterangan VARCHAR(200),
  nominal INT NOT NULL,
  tagihan_id INT NULL
) ENGINE=InnoDB;

CREATE TABLE surat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  warga_id INT NOT NULL,
  jenis ENUM('Pengantar RT/RW','Keterangan Domisili','Pengantar KTP','Pengantar KK') NOT NULL,
  keperluan VARCHAR(250) NOT NULL,
  status ENUM('diajukan','disetujui','ditolak') NOT NULL DEFAULT 'diajukan',
  catatan VARCHAR(250),
  no_surat VARCHAR(40),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (warga_id) REFERENCES warga(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE aduan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  warga_id INT NOT NULL,
  kategori VARCHAR(40) NOT NULL,
  judul VARCHAR(150) NOT NULL,
  deskripsi TEXT NOT NULL,
  status ENUM('baru','diproses','selesai') NOT NULL DEFAULT 'baru',
  tanggapan VARCHAR(300),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (warga_id) REFERENCES warga(id) ON DELETE CASCADE
) ENGINE=InnoDB;
