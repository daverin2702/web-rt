CARA PASANG (XAMPP/Laragon, PHP 7.4+ dan MySQL/MariaDB)
1. Salin folder ini ke htdocs (mis. htdocs/website-rt).
2. Buka phpMyAdmin, tab Import, pilih database.sql.
3. Sesuaikan DB_USER/DB_PASS dan NAMA_RT/LOKASI di config.php.
4. Buka http://localhost/website-rt/setup.php sekali, lalu HAPUS setup.php.
5. Buka http://localhost/website-rt/ dan masuk:
   admin / admin123   |   rt / rt12345   |   budi / warga123
   Segera ganti semua kata sandi lewat menu Akun Pengguna.
Catatan: Bootstrap dimuat dari CDN, jadi perlu internet saat dibuka.

CARA TES DI GITHUB CODESPACES
1. Unggah seluruh isi folder ini ke repositori GitHub (folder .devcontainer harus ada di akar repositori).
2. Klik Code > Codespaces > Create codespace on main.
3. Tunggu build selesai. PHP dan MariaDB menyala otomatis, database.sql diimpor, dan akun contoh dibuat.
4. Buka tab Ports, klik tautan port 8080 (Website RT).
Kalau database perlu diulang dari nol: jalankan "docker compose -f .devcontainer/docker-compose.yml down -v" dari luar container, atau buat Codespace baru.
Konfigurasi ini hanya untuk pengujian. Jangan dipakai untuk data warga sungguhan.
