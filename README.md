# KiosDigital PPOB - PHP Native Deployment Guide

Aplikasi PPOB siap pakai dengan integrasi **Tripay** (Payment Gateway) dan **Digiflazz** (Supplier).

## Fitur Utama
- Jual Pulsa, Paket Data, Token PLN, Top Up Game.
- Sistem Saldo User (Deposit Manual & Otomatis).
- Dashboard Multi-Role (Owner, Admin, Staff, User).
- Cetak Struk / Invoice Profesional.
- Sinkronisasi Produk Otomatis dari Digiflazz.

## Persyaratan Server
- Hosting / cPanel (Support PHP 7.4 atau 8.x).
- MySQL / MariaDB.
- Extension: cURL, MySQLi, JSON (Standar hosting).

## Instruksi Instalasi (cPanel)

1. **Upload File**:
   - Masuk ke **cPanel File Manager**.
   - Buka folder `public_html`.
   - Upload seluruh folder/file dari project ini ke sana.

2. **Buat Database**:
   - Buka **MySQL® Database Wizard**.
   - Buat database baru (misal: `u123_kiosdigital`).
   - Buat user baru dan hubungkan ke database dengan **All Privileges**.

3. **Import SQL**:
   - Buka **phpMyAdmin**.
   - Pilih database yang baru dibuat.
   - Klik tab **Import**, pilih file `/database/database.sql` dari komputer Anda.
   - Klik **Go**.

4. **Konfigurasi Database**:
   - Edit file `config/database.php`.
   - Isi `$host`, `$user`, `$pass`, dan `$db` sesuai data database Anda di cPanel.

5. **Konfigurasi API**:
   - Edit file `config/tripay.php` (Masukkan API Key & Merchant Code dari Tripay).
   - Edit file `config/digiflazz.php` (Masukkan Username & API Key dari Digiflazz).
   - Pastikan URL Callback sudah benar (Gunakan HTTPS).

6. **Izin Folder (Permissions)**:
   - Pastikan folder `uploads/` memiliki permission `755` atau `777` agar bisa menyimpan bukti transfer.
   - Pastikan folder `logs/` writable jika ingin menyimpan catatan error.

## Akun Default
Setelah instalasi, Anda bisa login menggunakan:

- **Owner**: `owner@kiosdigital.test` / `owner123`
- **Admin**: `admin@kiosdigital.test` / `admin123`
- **User**: `user@kiosdigital.test` / `user123`

*Catatan: Segera ubah password default demi keamanan.*

## Lisensi
Principal PHP Native Developer - 2026
