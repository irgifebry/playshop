# Struktur Database Playshop

Dokumentasi ini menjelaskan skema database `playshop_db`. Database ini menggunakan **MySQL/MariaDB**.

Untuk membuat database ini secara otomatis, import file `database/schema.sql`.

---

## Ringkasan Tabel (14 Tabel)

| Nama Tabel | Deskripsi |
| :--- | :--- |
| `users` | Menyimpan data pengguna yang terdaftar (member). |
| `games` | Menyimpan daftar game atau kategori layanan top-up. |
| `products` | Daftar nominal/item yang bisa dibeli untuk setiap game. |
| `transactions` | Mencatat riwayat pembelian/top-up. |
| `vouchers` | Kode promo diskon. |
| `banners` | Gambar slider promosi di halaman depan. |
| `contacts` | Pesan yang masuk lewat formulir "Hubungi Kami". |
| `settings` | Konfigurasi situs dinamis (key-value store). |
| `payment_methods` | Daftar metode pembayaran (E-Wallet, Bank, VA). |
| `deposits` | Riwayat top-up saldo website user. |
| `api_providers` | Konfigurasi koneksi ke provider PPOB/top-up pihak ketiga. |
| `posts` | Artikel/blog untuk halaman Blog & News. |
| `testimonials` | Ulasan pelanggan yang ditampilkan di website. |
| `notifications_log` | Log sistem untuk tracking aktivitas transaksi. |

---

## Detail Tabel

### 1. `users`
Tabel untuk menyimpan data akun pengguna.
*   `id`: Primary Key.
*   `name`: Nama lengkap.
*   `email`: Email login (Unique).
*   `phone`: Nomor telepon.
*   `password`: Password terenkripsi.
*   `balance`: Saldo website user (untuk fitur deposit).
*   `status`: Status akun (`active`/`banned`).

### 2. `games`
Kategori game yang tersedia.
*   `id`: Primary Key.
*   `name`: Nama Game (contoh: Mobile Legends).
*   `icon`: Emoji atau ikon teks.
*   `image_path`: Path gambar logo game.
*   `description`: Deskripsi singkat game.
*   `how_to_topup`: Teks instruksi cara top up.
*   `faq`: FAQ khusus game.
*   `color_start`, `color_end`: Warna gradasi untuk kartu game di UI.
*   `min_price`: Label "Mulai dari Rp..." di tampilan.
*   `category`: Kategori game (MOBA, Action, RPG, PC, dll).
*   `is_active`: Status aktif/nonaktif.

### 3. `products`
Item spesifik yang dijual di dalam game.
*   `id`: Primary Key.
*   `game_id`: Foreign Key ke tabel `games`.
*   `name`: Nama produk (contoh: 50 Diamond).
*   `price`: Harga jual.
*   `stock`: Jumlah stok (NULL = Unlimited).
*   `is_active`: Status aktif/nonaktif.

### 4. `transactions`
Riwayat order.
*   `id`: Primary Key.
*   `order_id`: String unik order (contoh: TRX-12345).
*   `game_id`, `product_id`: Referensi item yang dibeli.
*   `account_user_id`: ID user web yang login (FK ke users).
*   `account_email`: Email user yang melakukan transaksi.
*   `game_user_id`: ID akun game target (Mandatory).
*   `game_zone_id`: ID Server game (NULL jika tidak ada).
*   `payment_method`: Metode pembayaran yang dipilih.
*   `subtotal`, `admin_fee`, `discount_amount`: Detail harga.
*   `voucher_code`: Kode voucher yang dipakai (jika ada).
*   `amount`: Total akhir yang harus dibayar.
*   `status`: `pending`, `success`, `failed`.

### 5. `vouchers`
Kupon diskon.
*   `code`: Kode unik (contoh: PLAYSHOP20).
*   `type`: Tipe potongan (`percentage` atau `fixed`).
*   `amount`: Besaran potongan.
*   `description`: Deskripsi voucher.
*   `expired_date`: Tanggal kadaluarsa.
*   `usage_limit`: Batas maksimal pemakaian (NULL = unlimited).
*   `used_count`: Jumlah sudah dipakai.

### 6. `banners`
Slider gambar di homepage.
*   `image_path`: Lokasi file gambar.
*   `title`, `description`: Judul dan deskripsi banner.
*   `link_url`: Link tujuan saat banner diklik.
*   `sort_order`: Urutan tampil.
*   `is_active`: Status aktif/nonaktif.
*   `start_date`, `end_date`: Periode tayang.

### 7. `payment_methods`
Daftar metode pembayaran.
*   `name`: Nama tampilan (contoh: BCA Virtual Account).
*   `code`: Kode internal (contoh: BCA_VA).
*   `type`: Kategori (E-Wallet, Bank Transfer, VA, Store).
*   `fee_flat`: Biaya admin tetap.
*   `fee_percent`: Biaya admin persentase.
*   `image_path`: Logo metode pembayaran.
*   `is_active`: Status aktif/nonaktif.

### 8. `deposits`
Riwayat top-up saldo website. Dikelola via `admin/deposits.php`.
*   `user_id`: FK ke tabel users.
*   `payment_method_id`: FK ke tabel payment_methods.
*   `amount`: Jumlah deposit.
*   `status`: `pending`, `success`, `failed`. Memerlukan verifikasi Admin.

### 9. `api_providers`
Konfigurasi provider top-up pihak ketiga.
*   `name`: Nama provider (contoh: Digiflazz, VIP Reseller).
*   `api_key`, `secret_key`: Kredensial API.
*   `endpoint`: URL endpoint API.
*   `balance`: Saldo di provider.
*   `is_active`: Status koneksi aktif/nonaktif.

### 10. `posts`
Artikel blog.
*   `title`: Judul artikel.
*   `slug`: URL-friendly identifier.
*   `content`: Isi artikel (HTML/Text).
*   `image_path`: Gambar thumbnail.

### 11. `testimonials`
Ulasan pelanggan.
*   `name`: Nama pelanggan.
*   `rating`: Rating 1-5.
*   `comment`: Komentar ulasan.
*   `is_shown`: Tampilkan di website (1/0).

### 12. `notifications_log`
Log sistem. Bisa dipantau via `admin/logs.php`.
*   `message`: Pesan log (contoh: [TRANSAKSI SUKSES] Order ID: xxx).
*   `created_at`: Waktu log dibuat.

---

## Relasi Antar Tabel (ERD)

*   **Games** --(1:N)--> **Products**
    *   Satu game memiliki banyak opsi produk (nominal top-up).
*   **Games** --(1:N)--> **Transactions**
    *   Transaksi terikat pada game tertentu.
*   **Products** --(1:N)--> **Transactions**
    *   Transaksi terikat pada produk tertentu.
*   **Users** --(1:N)--> **Transactions**
    *   User yang login bisa memiliki banyak riwayat transaksi.
*   **Users** --(1:N)--> **Deposits**
    *   User bisa memiliki banyak riwayat deposit.
*   **Payment Methods** --(1:N)--> **Deposits**
    *   Deposit terikat pada metode pembayaran tertentu.

---

## Catatan Penting
*   Semua kolom `created_at` dan `updated_at` diisi otomatis oleh database (MySQL Timestamp).
*   Foreign Key Constraint diterapkan dengan aksi `ON DELETE CASCADE` atau `SET NULL` sesuai kebutuhan logis.
*   Tabel `notifications_log` diisi otomatis saat transaksi sukses (via `success.php`).
