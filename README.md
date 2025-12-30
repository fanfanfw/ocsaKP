# Sistem Informasi Inventaris Alat Ajar

Aplikasi Laravel 10 untuk mengelola inventaris alat ajar dengan dua role: admin dan tentor.

## Persyaratan
- PHP 8.2+
- Composer
- MySQL/MariaDB

## Konfigurasi
1. Pastikan database `rekap_aset` sudah ada dan sesuai dengan `rekap_aset.sql`.
2. Salin `.env` jika belum ada:
   ```bash
   cp .env.example .env
   ```
3. Jalankan:
   ```bash
   composer install
   php artisan key:generate
   ```
4. Atur koneksi database di `.env` (sudah disesuaikan untuk user `fanfan`).

## Menjalankan Aplikasi
1. Buat storage link untuk bukti pengembalian:
   ```bash
   php artisan storage:link
   ```
2. Jalankan server:
   ```bash
   php artisan serve
   ```

## Catatan
- Skema tabel mengikuti `rekap_aset.sql`.
- Migrasi sudah disesuaikan dan aman dijalankan karena akan mengecek tabel terlebih dahulu.
- Login menggunakan `username` + kata sandi dari tabel `users`. Jika password lama masih hash lama, sistem akan meng-upgrade ke bcrypt setelah berhasil login.

## Ekspor Laporan
Menu **Laporan** (admin) menyediakan ekspor CSV untuk aset, peminjaman, dan perawatan.
