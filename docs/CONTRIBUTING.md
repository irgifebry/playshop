# Panduan Kontribusi (Contributing Guide)

Terima kasih atas minat Anda untuk berkontribusi pada proyek **PLAYSHOP.ID**! Dokumen ini berisi panduan bagi developer yang ingin ikut serta mengembangkan proyek ini.

---

## 🛠 Persiapan Development

1.  **Environment Lokal**
    *   Pastikan PHP >= 7.4.
    *   Aktifkan ekstensi `pdo_mysql` di `php.ini`.
    *   Setel `display_errors = On` saat development untuk memudahkan debugging.

2.  **Versioning**
    *   Gunakan Git untuk kontrol versi.
    *   Jangan pernah commit file kredensial (seperti password database asli) jika proyek ini dipublikasikan ke repo publik. File `config/database.php` sebaiknya masuk `.gitignore` jika berisi password sensitif, namun untuk proyek latihan ini dibiarkan sebagai template.

---

## 📝 Aturan Penulisan Kode (Coding Standards)

Meskipun ini adalah proyek PHP Native sederhana, menjaga konsistensi kode itu penting:

1.  **Format PHP**
    *   Gunakan tag pembuka `<?php` penuh (jangan `<?`).
    *   Nama variabel: `camelCase` (contoh: `$userList`, `$totalPrice`).
    *   Nama fungsi: `snake_case` (contoh: `get_user_by_id()`) atau `camelCase` konsisten.
    *   Indentasi: Gunakan 4 spasi atau 1 tab, asalkan konsisten dalam satu file.

2.  **SQL & Database**
    *   **WAJIB** menggunakan Prepared Statements (PDO) untuk semua query yang menerima input user. Jangan pernah memasukkan variabel langsung ke string SQL.
    *   Contoh Benar:
        ```php
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        ```
    *   Contoh Salah (Rawan SQL Injection):
        ```php
        $pdo->query("SELECT * FROM users WHERE id = '$id'"); // JANGAN LAKUKAN INI
        ```

3.  **HTML & CSS**
    *   Tulis HTML semantik (`<header>`, `<main>`, `<footer>`, `<section>`).
    *   Gunakan class CSS yang deskriptif.

---

## 🐛 Melaporkan Bug

Jika Anda menemukan bug:
1.  Cek apakah bug tersebut sudah dilaporkan di daftar Issues repo (jika ada).
2.  Buka Issue baru dengan detail:
    *   Langkah-langkah untuk mereproduksi bug.
    *   Apa yang diharapkan vs apa yang terjadi.
    *   Screenshot error (jika ada).

---

## ➕ Cara Menambah Fitur Baru

1.  **Pilih Fitur**: Tentukan fitur apa yang ingin dibuat (misal: "Integrasi Payment Gateway X").
2.  **Buat Branch**: `git checkout -b fitur-payment-x`.
3.  **Koding**: Implementasikan fitur tersebut.
    *   Jika mengubah database, buat file migrasi SQL baru atau update instruksi di `docs/DATABASE.md`.
    *   Jika menambah halaman baru, pastikan header dan footer ter-include dengan benar.
    *   **Gambar & Asset**: Gunakan fungsi `asset_url()` untuk semua asset gambar. Jika menambah gambar statis, letakkan di `assets/`. Jika gambar dinamis (admin upload), letakkan di `uploads/` (misal: `uploads/banners/`).
4.  **Uji Coba**: Pastikan tidak ada fitur lain yang rusak.
5.  **Pull Request**: Kirim PR dan jelaskan perubahan Anda.

---

Selamat berkarya! 🚀
