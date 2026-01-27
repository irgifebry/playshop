# Arsitektur Sistem Playshop

Dokumentasi ini menjelaskan bagaimana **PLAYSHOP.ID** dibangun secara teknis, termasuk struktur kode, alur data, dan prinsip desain yang digunakan.

---

## 1. Pendekatan Pengembangan (Architecture Style)

Playshop dibangun menggunakan **PHP Native (Procedural & Functional)**.

*   **Tanpa Framework**: Tidak menggunakan framework besar seperti Laravel atau CodeIgniter.
*   **Tanpa Router Engine**: Routing didasarkan pada file fisik (`.php`). URL `http://site.com/about.php` langsung mengakses file `about.php`.
*   **Pattern Sederhana**: Logika bisnis, query database, dan tampilan (HTML) seringkali berada dalam satu file atau dipisahkan secara minimal menggunakan `includes`.

Pilihan ini diambil untuk memudahkan pemahaman bagi pemula yang baru belajar PHP dasar tanpa perlu mempelajari konsep MVC yang kompleks terlebih dahulu.

---

## 2. Struktur Direktori & Peran File

### Root Directory (`/`)
Berisi halaman-halaman yang dapat diakses publik (User Interface).
*   `index.php`: Halaman muka (Homepage).
*   `checkout.php`: Halaman transaksi.
*   `game-detail.php`: Detail produk game.
*   File halaman statis: `about.php`, `contact.php`, `faq.php`, dll.

### `/config`
Berisi konfigurasi global.
*   `database.php`: Menginisialisasi koneksi PDO ke database MySQL. File ini di-`require` oleh hampir semua file lain yang butuh akses data.

### `/includes`
Berisi komponen UI yang digunakan berulang kali (Partial Views) dan fungsi bantuan.
*   `header.php`: Navigasi bagian atas.
*   `footer.php`: Bagian bawah halaman.
*   `db_utils.php`: Kumpulan fungsi helper (misal: `app_base_url_path`, `asset_url`).

### `/admin`
Merupakan area khusus administrator.
*   Struktur di dalamnya mirip dengan root, namun dilindungi oleh sesi login admin.
*   Berisi file CRUD untuk Games, Products, Users, dll.

### `/api`
Menyediakan endpoint data dalam format JSON.
*   Digunakan oleh JavaScript frontend atau aplikasi pihak ketiga (jika ada).
*   Contoh: `api/games.php` mengembalikan daftar game dalam format JSON.

### `/assets`, `/css`, `/js`
Resource statis untuk frontend.
*   CSS dipisahkan menjadi `style.css` (umum) dan `mobile-optimization.css` (khusus responsif).

### `/uploads`
Direktori tempat menyimpan file yang diunggah user/admin, seperti ikon game atau banner.

---

## 3. Alur Kerja Aplikasi (Request Lifecycle)

Contoh alur ketika user membuka halaman `index.php`:

1.  **Request Masuk**: Browser meminta `index.php`.
2.  **Inisialisasi**:
    *   `session_start()` dipanggil untuk memulai sesi user.
    *   `require_once 'config/database.php'` menghubungkan ke database.
    *   `require_once 'includes/db_utils.php'` memuat fungsi bantuan.
3.  **Logika Bisnis & Query**:
    *   Script melakukan query SQL (misal: `SELECT * FROM banners`).
    *   Script melakukan query SQL (misal: `SELECT * FROM games`).
4.  **Rendering Tampilan**:
    *   Script memuat `includes/header.php`.
    *   Looping data hasil query untuk merender HTML (daftar banner, grid game).
    *   Script memuat `includes/footer.php`.
5.  **Response**: HTML dikirim kembali ke browser.

---

## 4. Keamanan Dasar

Meskipun sederhana, aplikasi menerapkan beberapa praktik keamanan dasar:
*   **PDO Prepared Statements**: Mencegah SQL Injection pada query database.
*   **`htmlspecialchars()`**: Mencegah XSS (Cross-Site Scripting) saat menampilkan output ke HTML.
*   **Password Hashing**: Menggunakan fungsi bawaan PHP untuk password user (jika diimplementasikan penuh).

## 5. Pengembangan Frontend

*   **CSS Variables**: Menggunakan variabel CSS (`:root`) untuk konsistensi warna dan tema.
*   **Responsive Design**: Menggunakan Media Queries untuk menyesuaikan tampilan di Mobile, Tablet, dan Desktop.

---

## 6. Tips Maintainability

Untuk developer yang ingin mengembangkan lebih lanjut:
*   Hindari menulis query SQL mentah di tengah-tengah HTML jika memungkinkan. Pindahkan logika pengambilan data ke bagian atas file.
*   Gunakan `includes/` untuk elemen yang muncul di lebih dari 2 halaman.
*   Selalu gunakan `asset_url()` saat memanggil gambar atau link CSS agar path tidak rusak saat aplikasi dipindahkan ke subfolder.
