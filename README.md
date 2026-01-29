# PLAYSHOP.ID - Dokumentasi Proyek

Selamat datang di **PLAYSHOP.ID**, sebuah platform top-up game online yang cepat, aman, dan mudah digunakan. Proyek ini dibangun menggunakan **PHP Native** tanpa framework, sehingga sangat cocok untuk pemula yang ingin mempelajari dasar-dasar pengembangan web atau developer yang ingin mengembangkan fitur lebih lanjut.

---

## 📋 Daftar Isi

1. [Tentang Proyek](#-tentang-proyek)
2. [Fitur Utama](#-fitur-utama)
3. [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
4. [Prasyarat Sistem](#-prasyarat-sistem)
5. [Instalasi & Pengaturan](#-instalasi--pengaturan)
6. [Struktur Folder](#-struktur-folder)
7. [Dokumentasi Lanjutan](#-dokumentasi-lanjutan)
8. [Kontribusi](#-kontribusi)
9. [Lisensi](#-lisensi)

---

## 📖 Tentang Proyek

**PLAYSHOP.ID** adalah aplikasi web sederhana namun fungsional untuk melakukan top-up berbagai game populer seperti Mobile Legends, Free Fire, PUBG Mobile, dan lainnya. Aplikasi ini dirancang dengan antarmuka yang modern, responsif, dan mudah dinavigasi.

Tujuan utama proyek ini adalah menyediakan contoh implementasi toko online produk digital (top-up) dengan fitur lengkap mulai dari pemilihan game, checkout, hingga manajemen admin.

---

## ✨ Fitur Utama

### 👤 Pengguna (User)
*   **Beranda Interaktif**: Slider banner promo dan daftar game populer.
*   **Pencarian & Filter**: Mencari game berdasarkan nama atau kategori (RPG, MOBA, dll).
*   **Detail Game**: Halaman khusus untuk setiap game dengan pilihan nominal top-up.
*   **Checkout**: Proses pembelian yang mudah dengan berbagai metode pembayaran (simulasi).
*   **Cek Pesanan**: Melacak status transaksi menggunakan ID pesanan.
*   **Akun Member**: Registrasi, Login, dan Riwayat Transaksi.
*   **Halaman Statis**: Tentang Kami, Karir, Blog, Kontak, FAQ, Syarat & Ketentuan, Kebijakan Privasi.

### 🛡️ Admin Panel (`/admin`)
*   **Dashboard**: Ringkasan statistik penjualan dan pengguna.
*   **Manajemen Game**: Tambah, edit, hapus game (ikon, warna, deskripsi).
*   **Manajemen Produk**: Mengatur nominal top-up dan harga untuk setiap game.
*   **Manajemen Transaksi**: Melihat dan memproses pesanan masuk.
*   **Kelola Saldo**: Verifikasi manual deposit saldo dari user (Approve/Reject).
*   **Manajemen Pesan**: Membaca dan menanggapi pesan dari formulir kontak.
*   **Log Sistem**: Memantau aktivitas penting sistem (Logs).
*   **Manajemen Pengguna**: Mengelola data member.
*   **Manajemen Banner**: Mengganti banner promo di halaman depan.
*   **Laporan**: Melihat laporan penjualan.
*   **Pengaturan Website**: Mengubah informasi kontak dan konfigurasi dasar.

---

## 🛠 Teknologi yang Digunakan

*   **Bahasa Pemrograman**: PHP (Native / Vanilla) vers 7.4 atau lebih baru.
*   **Database**: MySQL / MariaDB.
*   **Frontend**: HTML5, CSS3 (Vanilla + Custom Properties), JavaScript (Vanilla).
*   **Server**: Apache (via XAMPP/WAMP/Laragon).

Tidak ada framework PHP (seperti Laravel/CodeIgniter) atau CSS (seperti Bootstrap/Tailwind) yang digunakan secara berat, menjadikan kode ini ringan dan mudah dipelajari structure-nya.

---

## 💻 Prasyarat Sistem

Sebelum memulai, pastikan komputer Anda telah terinstal:

1.  **Web Server & Database**:
    *   Rekomendasi: [XAMPP](https://www.apachefriends.org/) (Windows/Linux/Mac).
    *   Alternative: WAMP, Laragon, atau MAMP.
2.  **Code Editor**:
    *   Rekomendasi: [VS Code](https://code.visualstudio.com/).
3.  **Browser Modern**: Google Chrome, Firefox, Edge, atau Safari.

---

## 🚀 Instalasi & Pengaturan

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

### 1. Clone atau Download Repository
Letakkan folder proyek ini di dalam direktori web root server Anda (misalnya `htdocs` untuk XAMPP).

```bash
# Contoh struktur direktori
C:\xampp\htdocs\playshop
```

### 2. Buat Database
1.  Buka **phpMyAdmin** (biasanya di `http://localhost/phpmyadmin`).
2.  Buat database baru dengan nama `playshop_db`.
    *   *Catatan: Nama database bisa disesuaikan, tapi pastikan update juga di konfigurasi.*

### 3. Import Struktur Database
1.  Di phpMyAdmin, pilih database `playshop_db` yang baru dibuat.
2.  Pilih tab **Import**.
3.  Upload file `database/schema.sql` yang ada di dalam folder proyek.
4.  Klik **Go/Kirim** untuk menjalankan query dan membuat tabel-tabel yang diperlukan.

### 4. Konfigurasi Koneksi Database
Buka file `config/database.php` dengan text editor dan sesuaikan kredensial database Anda:

```php
// config/database.php
$host = 'localhost';
$dbname = 'playshop_db'; // Sesuaikan jika nama database berbeda
$username = 'root';      // Default XAMPP adalah root
$password = '';          // Default XAMPP adalah kosong
```

### 5. Jalankan Aplikasi
Buka browser dan akses:
*   **Halaman Utama**: `http://localhost/playshop/`
*   **Halaman Admin**: `http://localhost/playshop/admin/` (Login default admin akan dijelaskan di dokumentasi lanjutan atau buat user baru dan ubah role di database jika belum tersedia akun admin default).

---

## 📂 Struktur Folder

Berikut adalah gambaran umum struktur direktori proyek:

```
playshop/
├── admin/              # Halaman-halaman panel admin
├── api/                # Endpoint API sederhana (JSON output)
├── assets/             # Gambar statis, upload user, dll
├── config/             # File konfigurasi (koneksi database)
├── css/                # File CSS (style.css, mobile-optimization.css)
├── database/           # File SQL untuk skema database
├── includes/           # Potongan kode PHP reusable (header, footer, functions)
├── js/                 # File JavaScript utama
├── uploads/            # Folder tujuan upload gambar (game, banner)
├── index.php           # Halaman utama
└── ... (file php lainnya)
```

Untuk detail lebih lengkap, silakan baca dokumentasi di folder `docs/`.

---

## 📚 Dokumentasi Lanjutan

Untuk memahami sistem lebih dalam, kami telah menyediakan dokumentasi terpisah di folder `docs/`:

*   [**Arsitektur Sistem**](docs/ARCHITECTURE.md): Penjelasan alur kerja, routing, dan struktur kode.
*   [**Database Schema**](docs/DATABASE.md): Detail tabel, kolom, dan relasi database.
*   [**API Reference**](docs/API.md): Dokumentasi endpoint API yang tersedia.
*   [**Panduan Kontribusi**](docs/CONTRIBUTING.md): Cara berkontribusi mengembangkan proyek ini.

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Jika Anda ingin menambahkan fitur baru atau memperbaiki bug:

1.  Fork repository ini.
2.  Buat branch fitur baru (`git checkout -b fitur-baru`).
3.  Commit perubahan Anda (`git commit -m 'Menambahkan fitur X'`).
4.  Push ke branch tersebut (`git push origin fitur-baru`).
5.  Buat Pull Request.

---

## 📄 Lisensi

Proyek ini bersifat open-source dan bebas digunakan untuk pembelajaran atau pengembangan lebih lanjut.

---
*Terima Kasih*
