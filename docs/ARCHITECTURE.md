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
*   `promo-detail.php`: Detail promo dari banner klik.
*   `payment.php`: Halaman pembayaran dengan instruksi transfer.
*   `success.php`: Halaman konfirmasi pembayaran (status: menunggu verifikasi admin).
*   `check-order.php`: Halaman tracking status pesanan.
*   `history.php`: Riwayat transaksi user (dengan filter dan animasi).
*   `profile.php`: Halaman profil user.
*   File halaman statis: `about.php`, `contact.php`, `faq.php`, `blog.php`, `testimonials.php`, dll.

### `/config`
Berisi konfigurasi global.
*   `database.php`: Menginisialisasi koneksi PDO ke database MySQL. File ini di-`require` oleh hampir semua file lain yang butuh akses data.

### `/includes`
Berisi komponen UI yang digunakan berulang kali (Partial Views) dan fungsi bantuan.
*   `header.php`: Navigasi bagian atas.
*   `footer.php`: Bagian bawah halaman.
*   `db_utils.php`: Kumpulan fungsi helper (misal: `app_base_url_path`, `asset_url`, `db_has_column`).
*   `email.php`: Fungsi dummy untuk simulasi pengiriman email.
*   `whatsapp.php`: Fungsi dummy untuk simulasi notifikasi WhatsApp.
*   `upload.php`: Fungsi helper untuk upload file.

### `/admin`
Merupakan area khusus administrator.
*   Struktur di dalamnya mirip dengan root, namun dilindungi oleh sesi login admin.
*   `dashboard.php`: Overview statistik dan transaksi terbaru.
*   `transaction-detail.php`: Detail transaksi dengan kemampuan update status.
*   `contacts.php`: Kelola pesan masuk dari user.
*   `deposits.php`: Verifikasi manual deposit saldo.
*   `logs.php`: Monitoring log sistem (notifications_log).
*   File CRUD: `games.php`, `products.php`, `users.php`, `banners.php`, `vouchers.php`, `payment-methods.php`, `providers.php`, `posts.php`, `testimonials.php`, `reports.php`, `settings.php`.

### `/api`
Menyediakan endpoint data dalam format JSON.
*   Digunakan oleh JavaScript frontend atau aplikasi pihak ketiga (jika ada).
*   Contoh: `api/games.php` mengembalikan daftar game dalam format JSON.

### `/assets`, `/css`, `/js`
Resource statis untuk frontend.
*   CSS dipisahkan menjadi `style.css` (umum) dan `mobile-optimization.css` (khusus responsif).

### `/uploads`
Direktori tempat menyimpan file yang diunggah user/admin, seperti ikon game, banner, atau gambar artikel.

### `/database`
Berisi file skema database.
*   `schema.sql`: Definisi tabel dan seed data awal.

### `/docs`
Dokumentasi proyek.
*   `ARCHITECTURE.md`: Dokumentasi arsitektur (file ini).
*   `DATABASE.md`: Dokumentasi struktur database.
*   `API.md`: Dokumentasi endpoint API.
*   `CONTRIBUTING.md`: Panduan kontribusi.

---

## 3. Alur Kerja Transaksi (Transaction Flow)

### Flow Pembelian:
```
1. User pilih game (index.php / game-detail.php)
       ↓
2. User pilih produk & isi User ID (checkout.php)
       ↓
3. User pilih metode pembayaran (checkout.php)
       ↓
4. User melihat instruksi pembayaran (payment.php)
       ↓
5. User klik "Sudah Bayar" → Masuk ke halaman konfirmasi (success.php)
   Status transaksi: PENDING (menunggu admin)
       ↓
6. User dapat tracking pesanan (check-order.php)
       ↓
7. Admin verifikasi pembayaran (admin/transaction-detail.php)
   Admin ubah status ke SUCCESS
       ↓
8. Sistem otomatis:
   - Kurangi stok produk
   - Update counter voucher (jika dipakai)
   - Catat log ke notifications_log
       ↓
9. User cek status → Pesanan BERHASIL ✅
```

### Flow Deposit Saldo:
```
1. User pilih nominal & metode pembayaran (deposit.php)
       ↓
2. User melihat instruksi (rekening/QRIS) (deposit-pay.php)
       ↓
3. User klik "Saya Sudah Bayar"
       ↓
4. Sistem menampilkan status "Menunggu Verifikasi"
       ↓
5. Admin cek mutasi & verifikasi (admin/deposits.php)
   Admin klik "Setujui/Approve"
       ↓
6. Sistem otomatis:
   - Tambah balance ke tabel users
   - Ubah status deposit ke SUCCESS
   - Catat log ke notifications_log
       ↓
7. Saldo User bertambah ✅
```

---

## 4. Alur Kerja Request (Request Lifecycle)

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

## 5. Keamanan Dasar

Meskipun sederhana, aplikasi menerapkan beberapa praktik keamanan dasar:
*   **PDO Prepared Statements**: Mencegah SQL Injection pada query database.
*   **`htmlspecialchars()`**: Mencegah XSS (Cross-Site Scripting) saat menampilkan output ke HTML.
*   **Password Hashing**: Menggunakan `password_hash()` dan `password_verify()` untuk password user.
*   **Session-based Auth**: Admin dan user login menggunakan session PHP.

## 6. Pengembangan Frontend

*   **CSS Variables**: Menggunakan variabel CSS (`:root`) untuk konsistensi warna dan tema.
*   **Responsive Design**: Menggunakan Media Queries untuk menyesuaikan tampilan di Mobile, Tablet, dan Desktop.
*   **Animasi**: Page entrance animations, slide transitions untuk filter, dan micro-interactions.
*   **Scroll Management**: Smart scroll position handling (reset saat navigasi navbar, preserve saat filter dalam halaman).

---

## 7. Fitur Logging & Monitoring

*   **`notifications_log`**: Tabel untuk mencatat aktivitas penting:
    - [PEMBAYARAN DIKIRIM]: Saat user masuk ke success.php.
    - [ADMIN KONFIRMASI SUKSES]: Saat admin mengubah status via transaction-detail.php.
    - [TRANSAKSI SUKSES]: Log legacy untuk transaksi yang berhasil otomatis.

---

## 8. Tips Maintainability

Untuk developer yang ingin mengembangkan lebih lanjut:
*   Hindari menulis query SQL mentah di tengah-tengah HTML jika memungkinkan. Pindahkan logika pengambilan data ke bagian atas file.
*   Gunakan `includes/` untuk elemen yang muncul di lebih dari 2 halaman.
*   Selalu gunakan `asset_url()` saat memanggil gambar atau link CSS agar path tidak rusak saat aplikasi dipindahkan ke subfolder.
*   Gunakan `db_has_column()` untuk backward compatibility jika menambah kolom baru.
