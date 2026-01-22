# 🚀 PLAYSHOP - Quick Start Guide

Panduan singkat untuk setup dan menjalankan Playshop dalam 5 menit.

## ⚡ Quick Setup (5 Menit)

### Prerequisites
- XAMPP sudah terinstall
- Git (opsional)

### Langkah-Langkah

#### 1️⃣ Copy Project ke htdocs
```bash
# Windows
C:\xampp\htdocs\playshop

# Linux/Mac
/Applications/XAMPP/htdocs/playshop
```

#### 2️⃣ Start XAMPP Services
- Buka XAMPP Control Panel
- Click **Start** di Apache
- Click **Start** di MySQL

#### 3️⃣ Create Database
```bash
# Buka Command Prompt di folder project
cd C:\xampp\htdocs\playshop

# Run SQL schema
mysql -u root < database/schema.sql

# Atau via phpMyAdmin: http://localhost/phpmyadmin
# 1. Create database "playshop_db"
# 2. Import "database/schema.sql"
```

#### 4️⃣ Verifikasi Konfigurasi
Cek `config/database.php`:
```php
$host = 'localhost';
$dbname = 'playshop_db';
$username = 'root';
$password = ''; // Kosong untuk XAMPP default
```

#### 5️⃣ Akses Website
```
http://localhost/playshop
```

---

## 🎯 Test User Accounts

### Test User (untuk mencoba fitur)
```
Email: user@test.com
Password: test123
```

### Test Admin (untuk admin panel)
```
Email: admin@playshop.id
Password: admin123
```

**⚠️ IMPORTANT**: Ubah password segera untuk production!

---

## 🎮 Test Flow

### 1. Register Akun Baru
```
1. Klik "Daftar" di navbar
2. Isi form:
   - Nama: John Doe
   - Email: john@test.com
   - No. HP: 081234567890
   - Password: test123
3. Klik "Daftar"
4. Login dengan akun baru
```

### 2. Beli Top Up
```
1. Di homepage, klik salah satu game
2. Pilih nominal (e.g., 12 Diamonds)
3. Input ID Game: 123456
4. Input Server: 7
5. Klik "Checkout"
6. Pilih metode pembayaran
7. Klik "Bayar Sekarang"
```

### 3. Cek Status Order
```
1. Buka "Cek Order" di navbar
2. Input Order ID atau Email
3. Lihat status transaksi
```

### 4. Akses Admin Panel
```
1. Buka http://localhost/playshop/admin/login.php
2. Login dengan email admin & password
3. Jelajahi dashboard
```

---

## 📁 Important Files

| File | Fungsi |
|------|--------|
| `config/database.php` | Database connection |
| `database/schema.sql` | Database structure |
| `includes/auth.php` | Authentication functions |
| `api/` | REST API endpoints |
| `admin/dashboard.php` | Admin dashboard |
| `css/style.css` | Website styling |
| `js/script.js` | JavaScript logic |

---

## 🔧 Common Commands

### View Database
```bash
mysql -u root playshop_db -e "SELECT * FROM users;"
```

### Backup Database
```bash
mysqldump -u root playshop_db > backup.sql
```

### Restore Database
```bash
mysql -u root playshop_db < backup.sql
```

### View Apache Logs
```bash
# Windows
C:\xampp\apache\logs\error.log

# Linux/Mac
/Applications/XAMPP/logs/apache_error.log
```

---

## 🆘 Quick Troubleshooting

### ❌ Error: "Connection refused"
- Pastikan MySQL running di XAMPP Control Panel
- Check `config/database.php` credentials

### ❌ Error: "Database not found"
- Run: `mysql -u root < database/schema.sql`
- Verify di phpMyAdmin

### ❌ Blank White Page
- Check Apache error logs
- Enable error reporting di `config/database.php`
- Verify PHP installed dengan `php -v`

### ❌ Upload Not Working
- Verify `uploads/` folder exists
- Check folder permissions: `chmod -R 755 uploads/`

### ❌ Login Not Working
- Check session start di `config/database.php`
- Clear browser cookies
- Verify user exists di database

---

## 📚 Full Documentation

Lihat [README.md](README.md) untuk dokumentasi lengkap.

---

**Happy Coding! 🚀**
