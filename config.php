<?php
session_start();
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'db_rt');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
const NAMA_RT = 'RT 05 / RW 02', LOKASI = 'Kelurahan Contoh, Surabaya';
try {
  $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
} catch (PDOException $x) {
  die('Koneksi database gagal. Periksa pengaturan di config.php dan pastikan database.sql sudah diimpor.');
}
function q($sql, $p = []) { global $pdo; $s = $pdo->prepare($sql); $s->execute($p); return $s; }
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rp($n) { return 'Rp '.number_format((float)$n, 0, ',', '.'); }
function tgl($d) { return $d ? date('d M Y', strtotime($d)) : '-'; }
function user() { return $_SESSION['user'] ?? null; }
function need($roles) {
  if (!user() || !in_array(user()['role'], $roles)) { header('Location: index.php'); exit; }
}
function flash($m, $t = 'success') { $_SESSION['f'] = [$m, $t]; }
function flash_show() {
  if (!isset($_SESSION['f'])) return '';
  [$m, $t] = $_SESSION['f']; unset($_SESSION['f']);
  return "<div class='alert alert-$t alert-dismissible'>".e($m)."<button class='btn-close' data-bs-dismiss='alert'></button></div>";
}
function csrf() { return $_SESSION['t'] ??= bin2hex(random_bytes(16)); }
function csrf_field() { return "<input type='hidden' name='t' value='".csrf()."'>"; }
function csrf_check() { if (($_POST['t'] ?? '') !== ($_SESSION['t'] ?? '-')) die('Token tidak valid. Muat ulang halaman.'); }
function badge($s) {
  $c = ['lunas'=>'success','disetujui'=>'success','selesai'=>'success','belum'=>'warning','diajukan'=>'warning','baru'=>'warning','diproses'=>'info','ditolak'=>'danger'];
  return "<span class='badge text-bg-".($c[$s] ?? 'secondary')."'>".e(ucfirst($s))."</span>";
}
function saldo() { return q("SELECT COALESCE(SUM(IF(tipe='masuk',nominal,-nominal)),0) FROM kas")->fetchColumn(); }
function wid() { return user()['warga_id'] ?? 0; }
function pengurus() { return in_array(user()['role'], ['admin','rt']); }
