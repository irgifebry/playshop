# Struktur Database Playshop

Dokumentasi ini menjelaskan skema database `playshop_db`. Database ini menggunakan **MySQL/MariaDB**.

Untuk membuat database ini secara otomatis, import file `database/schema.sql`.

---

## Ringkasan Tabel

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

---

## Detail Tabel

### 1. `users`
Tabel untuk menyimpan data akun pengguna.
*   `id`: Primary Key.
*   `name`: Nama lengkap.
*   `email`: Email login (Unique).
*   `password`: Password terenkripsi.
*   `status`: Status akun (`active`/`banned`).
*   `role`: (Implisit) Saat ini belum ada kolom role eksplisit, admin login biasanya dipisahkan atau ditambahkan kolom role nantinya.

### 2. `games`
Kategori game yang tersedia.
*   `id`: Primary Key.
*   `name`: Nama Game (contoh: Mobile Legends).
*   `icon`: Emoji atau ikon teks.
*   `image_path`: Path gambar logo game.
*   `color_start`, `color_end`: Warna gradasi untuk kartu game di UI.
*   `min_price`: Label "Mulai dari Rp..." di tampilan.
*   `how_to_topup`: Teks instruksi cara top up.

### 3. `products`
Item spesifik yang dijual di dalam game.
*   `id`: Primary Key.
*   `game_id`: Foreign Key ke tabel `games`.
*   `name`: Nama produk (contoh: 50 Diamond).
*   `price`: Harga jual.
*   `stock`: Jumlah stok (NULL = Unlimited).

### 4. `transactions`
Riwayat order.
*   `id`: Primary Key.
*   `order_id`: String unik order (contoh: TRX-12345).
*   `game_id`, `product_id`: Referensi item yang dibeli.
*   `user_id`, `zone_id`: ID akun game target (User ID / Server ID game).
*   `account_user_id`: ID user web yang login (jika ada).
*   `payment_method`: Metode pembayaran yang dipilih.
*   `amount`: Total akhir yang harus dibayar.
*   `status`: `pending`, `success`, `failed`.

### 5. `vouchers`
Kupon diskon.
*   `code`: Kode unik (contoh: PLAYSHOP20).
*   `type`: Tipe potongan (`percentage` atau `fixed`).
*   `amount`: Besaran potongan.

### 6. `banners`
Slider gambar di homepage.
*   `image_path`: Lokasi file gambar.
*   `link_url`: Link tujuan saat banner diklik.
*   `is_active`: Status aktif/nonaktif.

---

## Relasi Antar Tabel (ERD Sederhana)

*   **Games** --(1:N)--> **Products**
    *   Satu game memiliki banyak opsi produk (nominal top-up).
*   **Games** --(1:N)--> **Transactions**
    *   Transaksi terikat pada game tertentu.
*   **Products** --(1:N)--> **Transactions**
    *   Transaksi terikat pada produk tertentu.
*   **Users** --(1:N)--> **Transactions**
    *   User yang login bisa memiliki banyak riwayat transaksi.

---

## Catatan Penting
*   Semua kolom `created_at` dan `updated_at` diisi otomatis oleh database (MySQL Timestamp).
*   Foreign Key Constraint diterapkan dengan aksi `ON DELETE CASCADE` atau `SET NULL` sesuai kebutuhan logis.
