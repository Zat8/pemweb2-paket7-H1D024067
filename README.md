# EventUnsoed

## Identitas Mahasiswa
- NIM: `H1D024067'
- Nama: `Izzat Al Haq`
- Paket yang Dikerjakan: `[7-EventUnsoed]`

## Deskripsi Proyek
EventUnsoed adalah aplikasi manajemen event berbasis web yang dibuat untuk mengelola publikasi event, pendaftaran peserta, tiket digital, absensi, dan sertifikat secara terintegrasi.

## Fitur Utama
- Autentikasi pengguna dengan role `admin`, `panitia`, dan `peserta`
- Manajemen kategori event oleh admin
- Manajemen event oleh admin dan panitia
- Halaman publik daftar event beserta fitur pencarian dan filter
- Pendaftaran event oleh peserta
- Tiket digital dengan token dan QR code
- Absensi peserta saat hari-H menggunakan token tiket
- Generate sertifikat otomatis setelah peserta melakukan absensi
- Verifikasi sertifikat secara publik
- Export data kehadiran ke file Excel

## Tech Stack
- PHP 8.3
- Laravel 13
- Laravel Breeze
- MySQL
- Blade Template Engine
- Tailwind CSS
- Vite
- Alpine.js
- `barryvdh/laravel-dompdf` untuk generate PDF sertifikat
- `maatwebsite/excel` untuk export data Excel
- `simplesoftwareio/simple-qrcode` untuk QR code

## Cara Menjalankan Proyek
1. Clone atau salin proyek ini ke folder web server lokal, misalnya `C:\laragon\www\EventUnsoed`.
2. Masuk ke folder proyek.
   ```bash
   cd C:\laragon\www\EventUnsoed
   ```
3. Install dependency PHP.
   ```bash
   composer install
   ```
4. Install dependency frontend.
   ```bash
   npm install
   ```
5. Salin file environment.
   ```bash
   copy .env.example .env
   ```
6. Generate application key.
   ```bash
   php artisan key:generate
   ```
7. Buat database baru, misalnya dengan nama `eventunsoed`.
8. Atur konfigurasi database pada file `.env`, terutama bagian berikut:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=eventunsoed
   DB_USERNAME=root
   DB_PASSWORD=
   ```
9. Jalankan migrasi dan seeder.
   ```bash
   php artisan migrate --seed
   ```
10. Buat symbolic link storage.
   ```bash
   php artisan storage:link
   ```
11. Jalankan Vite development server.
   ```bash
   npm run dev
   ```
12. Jalankan Laravel development server pada terminal lain.
   ```bash
   php artisan serve
   ```
13. Buka aplikasi di browser.
   ```text
   http://127.0.0.1:8000
   ```

## Kredensial Default
### Admin
- Email: `admin@unsoed.ac.id`
- Password: `password`

### User/Peserta
- Email: `peserta@unsoed.ac.id`
- Password: `password`

### Panitia
- Email: `panitia@unsoed.ac.id`
- Password: `password`

## Tautan Video Demo YouTube
- `https://youtu.be/VgH50f7JuQM`

