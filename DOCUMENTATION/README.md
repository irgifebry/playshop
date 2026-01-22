# 🎮 PLAYSHOP.ID - Platform Top Up Game Online

Playshop adalah platform e-commerce modern untuk menjual top up game online dengan fitur lengkap termasuk admin panel, sistem pembayaran, dan integrasi WhatsApp.

## 📋 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Requirement Sistem](#requirement-sistem)
- [Instalasi](#instalasi)
- [Struktur Project](#struktur-project)
- [Database](#database)
- [API Documentation](#api-documentation)
- [Penggunaan](#penggunaan)
- [Admin Panel](#admin-panel)
- [Troubleshooting](#troubleshooting)

---

## ✨ Fitur Utama

### User Features
- ✅ Registrasi & Login (Email/Password)
- ✅ Profile Management
- ✅ Browsing Game & Produk
- ✅ Sistem Checkout
- ✅ Payment Gateway Integration
- ✅ Voucher/Promo Code
- ✅ Riwayat Transaksi
- ✅ Cek Status Order

### Admin Features
- ✅ Dashboard dengan Analytics
- ✅ Manajemen Games
- ✅ Manajemen Products
- ✅ Manajemen Users
- ✅ Diskon & Vouchers
- ✅ Laporan Penjualan
- ✅ Manajemen Banners
- ✅ Settings Website

### Integration
- 💬 WhatsApp Notification
- 📧 Email Notification
- 💳 Payment Gateway (Dummy/Custom)
- 📱 Responsive Design

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla) |
| **Backend** | PHP 7.4+ |
| **Database** | MySQL 5.7+ |
| **Server** | Apache (XAMPP) |
| **Additional** | cURL, PDO |

---

## 💻 Requirement Sistem

### Minimum Requirements
- **OS**: Windows/Linux/MacOS
- **PHP**: 7.4 atau lebih tinggi
- **MySQL**: 5.7 atau lebih tinggi
- **Apache**: 2.4 atau lebih tinggi
- **RAM**: 512MB
- **Disk**: 100MB

### Recommended
- PHP 8.0+
- MySQL 8.0+
- RAM 2GB+
- SSD Storage

### PHP Extensions Required
```
- PDO MySQL
- cURL
- JSON
- Session
- Filter
```

---

## 🚀 Instalasi

### Step 1: Setup XAMPP

1. Download XAMPP dari [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP pada folder default
3. Buka XAMPP Control Panel dan start **Apache** dan **MySQL**

### Step 2: Clone/Download Project

```bash
# Masuk ke folder htdocs XAMPP
cd C:\xampp\htdocs

# Clone project (atau download & extract)
git clone <repository-url> playshop
cd playshop
```

### Step 3: Setup Database

1. Buka phpMyAdmin: http://localhost/phpmyadmin
2. Buat database baru atau import schema
3. Jalankan script SQL:

```bash
# Menggunakan command line MySQL
mysql -u root -p < database/schema.sql

# Atau copy-paste ke phpMyAdmin
```

### Step 4: Konfigurasi Database

Edit file `config/database.php`:

```php
<?php
$host = 'localhost';
$dbname = 'playshop_db';        // Sesuaikan nama database
$username = 'root';              // Username MySQL
$password = '';                  // Password MySQL (kosong untuk XAMPP default)
```

### Step 5: Set Permissions (Linux/Mac)

```bash
# Berikan permission untuk folder upload
chmod -R 755 uploads/
chmod -R 755 storage/
```

### Step 6: Akses Website

- **Website**: http://localhost/playshop
- **Admin Panel**: http://localhost/playshop/admin/login.php
- **phpMyAdmin**: http://localhost/phpmyadmin

---

## 📁 Struktur Project

```
playshop/
├── admin/                      # Admin Panel
│   ├── dashboard.php          # Dashboard
│   ├── games.php              # Manajemen Games
│   ├── products.php           # Manajemen Products
│   ├── users.php              # Manajemen Users
│   ├── discounts.php          # Manajemen Diskon
│   ├── banners.php            # Manajemen Banners
│   ├── reports.php            # Laporan Penjualan
│   ├── settings.php           # Pengaturan Website
│   ├── transaction-detail.php # Detail Transaksi
│   └── sidebar.php            # Sidebar Component
│
├── api/                       # REST API
│   ├── games.php              # Get Games List
│   ├── products.php           # Get Products
│   ├── order-create.php       # Create Order
│   ├── order.php              # Get Order Detail
│   ├── login.php              # User Login API
│   ├── register.php           # User Register API
│   └── status.php             # Check Order Status
│
├── config/                    # Konfigurasi
│   └── database.php           # Database Connection
│
├── database/                  # Database Schema
│   └── schema.sql             # SQL Schema
│
├── includes/                  # Helper Functions
│   ├── auth.php               # Authentication
│   ├── db_utils.php           # Database Utilities
│   ├── email.php              # Email Sender
│   ├── upload.php             # File Upload
│   ├── voucher.php            # Voucher Logic
│   ├── whatsapp.php           # WhatsApp Integration
│   └── payment_gateway_dummy.php  # Payment Gateway
│
├── uploads/                   # File Storage
│   └── games/                 # Game Images
│
├── storage/                   # Storage Directory
│
├── css/
│   └── style.css              # Main Stylesheet
│
├── js/
│   └── script.js              # Main JavaScript
│
├── public pages/              # Public Pages
│   ├── index.php              # Homepage
│   ├── game-detail.php        # Game Detail
│   ├── checkout.php           # Checkout Page
│   ├── payment.php            # Payment Page
│   ├── success.php            # Success Page
│   ├── check-order.php        # Check Order Status
│   ├── login.php              # Login Page
│   ├── register.php           # Register Page
│   ├── profile.php            # User Profile
│   ├── history.php            # Transaction History
│   ├── promo.php              # Promo Page
│   ├── faq.php                # FAQ Page
│   ├── about.php              # About Page
│   ├── contact.php            # Contact Page
│   ├── blog.php               # Blog Page
│   ├── testimonials.php       # Testimonials
│   ├── partnership.php        # Partnership
│   └── privacy.php            # Privacy Policy
│
├── .git/                      # Git Repository
├── assets/                    # Additional Assets
├── README.md                  # Dokumentasi (File ini)
└── .gitignore                 # Git Ignore Rules
```

---

## 🗄 Database

### Database Name
```
playshop_db
```

### Main Tables

#### 1. **users** - Data User
```sql
- id: INT (Primary Key)
- name: VARCHAR(120)
- email: VARCHAR(190) UNIQUE
- phone: VARCHAR(40)
- password: VARCHAR(255) (hashed)
- status: ENUM('active', 'banned')
- created_at, updated_at: TIMESTAMP
```

#### 2. **games** - Daftar Game
```sql
- id: INT (Primary Key)
- name: VARCHAR(100)
- icon: VARCHAR(10) (emoji)
- image_path: VARCHAR(255)
- description: TEXT
- how_to_topup: TEXT
- faq: TEXT
- color_start, color_end: VARCHAR(7) (hex color)
- min_price: INT
- is_active: TINYINT(1)
- created_at, updated_at: TIMESTAMP
```

#### 3. **products** - Produk Top Up
```sql
- id: INT (Primary Key)
- game_id: INT (Foreign Key)
- name: VARCHAR(100)
- price: INT
- stock: INT (NULL = unlimited)
- is_active: TINYINT(1)
- created_at, updated_at: TIMESTAMP
```

#### 4. **transactions** - Riwayat Transaksi
```sql
- id: INT (Primary Key)
- order_id: VARCHAR(50) UNIQUE
- game_id: INT
- product_id: INT
- user_id: VARCHAR(190)
- zone_id: VARCHAR(100)
- account_user_id: INT
- account_email: VARCHAR(190)
- game_user_id: VARCHAR(100)
- game_zone_id: VARCHAR(100)
- quantity: INT
- amount: INT
- voucher_id: INT (nullable)
- discount_amount: INT
- final_amount: INT
- status: ENUM('pending', 'paid', 'processing', 'completed', 'failed', 'cancelled')
- payment_method: VARCHAR(50)
- notes: TEXT
- created_at, updated_at: TIMESTAMP
```

#### 5. **vouchers** - Kode Promo
```sql
- id: INT (Primary Key)
- code: VARCHAR(50) UNIQUE
- type: ENUM('percentage', 'fixed')
- amount: INT
- description: VARCHAR(255)
- expired_date: DATE
- status: ENUM('active', 'inactive')
- usage_limit: INT (nullable)
- used_count: INT
- created_at, updated_at: TIMESTAMP
```

#### 6. **banners** - Banner Homepage
```sql
- id: INT
- title, image_path, link: VARCHAR
- start_date, end_date: DATE
- is_active: TINYINT(1)
- sort_order: INT
```

---

## 🔌 API Documentation

### Base URL
```
http://localhost/playshop/api/
```

### 1. Get All Games
```http
GET /api/games.php
```
**Response:**
```json
{
  "ok": true,
  "data": [
    {
      "id": 1,
      "name": "Mobile Legends",
      "icon": "🎮",
      "image_path": "uploads/games/ml.jpg",
      "color_start": "#10b981",
      "color_end": "#059669",
      "min_price": 5000,
      "is_active": 1
    }
  ]
}
```

### 2. Get Products by Game
```http
GET /api/products.php?game_id=1
```
**Response:**
```json
{
  "ok": true,
  "data": [
    {
      "id": 1,
      "game_id": 1,
      "name": "12 Diamonds",
      "price": 10000,
      "stock": null,
      "is_active": 1
    }
  ]
}
```

### 3. User Register
```http
POST /api/register.php
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "081234567890",
  "password": "password123",
  "confirm_password": "password123"
}
```

### 4. User Login
```http
POST /api/login.php
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### 5. Create Order
```http
POST /api/order-create.php
Content-Type: application/json

{
  "game_id": 1,
  "product_id": 1,
  "game_user_id": "123456",
  "game_zone_id": "7",
  "quantity": 1,
  "voucher_code": "PROMO10"
}
```

### 6. Check Order Status
```http
GET /api/status.php?order_id=ORD-1234567890
```

---

## 📖 Penggunaan

### User Flow

#### 1. Registrasi Akun
```
1. Buka register.php
2. Isi form dengan email, nama, nomor, password
3. Click "Daftar"
4. Verifikasi email (jika diaktifkan)
```

#### 2. Login
```
1. Buka login.php
2. Input email & password
3. Click "Masuk"
4. Redirect ke homepage (sudah login)
```

#### 3. Beli Top Up
```
1. Dari homepage, pilih game
2. Klik "Beli Sekarang"
3. Pilih nominal/produk
4. Input ID Game & Server
5. Klik "Checkout"
6. Pilih metode pembayaran
7. Selesaikan pembayaran
8. Transaksi diproses (WhatsApp notification dikirim)
```

#### 4. Cek Status Order
```
1. Buka check-order.php
2. Input Order ID atau Email
3. Lihat status transaksi
```

### Admin Flow

#### 1. Login Admin
```
1. Buka /admin/login.php
2. Input email admin & password
3. Masuk ke dashboard
```

#### 2. Tambah Game
```
1. Dashboard > Games
2. Click "Tambah Game"
3. Isi form (nama, deskripsi, harga minimum, etc)
4. Upload gambar
5. Click "Simpan"
```

#### 3. Tambah Produk
```
1. Dashboard > Products
2. Pilih Game
3. Click "Tambah Produk"
4. Isi nama & harga
5. Click "Simpan"
```

#### 4. Kelola Diskon
```
1. Dashboard > Discounts
2. Click "Tambah Diskon"
3. Isi kode, tipe (%, fixed), amount
4. Set tanggal expired & limit pemakaian
5. Click "Simpan"
```

#### 5. Lihat Laporan
```
1. Dashboard > Reports
2. Filter by date range, game, status
3. Download Excel report (jika ada fitur)
```

---

## 🔐 Admin Panel

### Login Admin
- **URL**: http://localhost/playshop/admin/login.php
- **Default Credentials** (Sesuaikan saat setup):
  - Email: `admin@playshop.id`
  - Password: `admin123` (Ubah segera!)

### Admin Functions

| Menu | Fungsi |
|------|--------|
| Dashboard | Overview penjualan & analytics |
| Games | CRUD games & kategori |
| Products | CRUD produk top up |
| Users | Manajemen user, ban account |
| Discounts | CRUD voucher & promo |
| Banners | Manajemen banner homepage |
| Reports | Laporan transaksi & analytics |
| Settings | Konfigurasi website |
| Transactions | Detail setiap transaksi |

---

## 🛠 Development

### Setup Development Environment

1. **Install PHP Debugger**
   ```bash
   # Xdebug untuk VS Code
   composer require --dev php-xdebug
   ```

2. **Enable Error Reporting** (di `config/database.php`)
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

3. **Database Backup**
   ```bash
   mysqldump -u root playshop_db > backup.sql
   ```

### Common Tasks

#### Add New Page
1. Create file di root (e.g., `new-page.php`)
2. Include config & session
3. Design layout & add styling

#### Add New Admin Feature
1. Create file di `admin/` folder
2. Add `require_admin_login()`
3. Update sidebar menu di `admin/sidebar.php`

#### Add New API Endpoint
1. Create file di `api/` folder
2. Header: `application/json`
3. Return JSON response

---

## ⚙️ Konfigurasi

### Email Configuration (includes/email.php)
```php
$email_config = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_user' => 'your-email@gmail.com',
    'smtp_pass' => 'your-app-password',
    'from_email' => 'noreply@playshop.id',
    'from_name' => 'PLAYSHOP.ID'
];
```

### WhatsApp Integration (includes/whatsapp.php)
```php
$whatsapp_config = [
    'api_url' => 'https://api.whatsapp.com',
    'phone' => '628123456789', // Nomor WhatsApp
    'token' => 'your-token'
];
```

### Payment Gateway (includes/payment_gateway_dummy.php)
```php
// Dummy payment gateway
// Untuk production, replace dengan real gateway (Midtrans, Xendit, etc)
```

---

## 🐛 Troubleshooting

### Problem: Database Connection Error
```
Error: Connection failed: SQLSTATE[HY000] [2002]
```
**Solution:**
1. Pastikan MySQL running di XAMPP Control Panel
2. Check `config/database.php` - correct host, username, password
3. Pastikan database `playshop_db` sudah di-create

### Problem: White Blank Page
```
Solusi:
```
1. Enable error reporting di `config/database.php`
2. Check Apache error log: `C:\xampp\apache\logs\error.log`
3. Verify all required PHP extensions loaded

### Problem: 404 Not Found
```
Solution:
```
1. Check `.htaccess` configuration
2. Verify Apache `mod_rewrite` enabled
3. Check file paths are correct

### Problem: Upload Error
```
Solution:
```
1. Check folder permissions (755 untuk Linux/Mac)
2. Verify `uploads/` folder exists
3. Check disk space available
4. Verify PHP `upload_max_filesize`

### Problem: Session Lost After Page Reload
```
Solution:
```
1. Check `php.ini` session settings
2. Verify `session_start()` di setiap page
3. Check cookie settings (SECURE, HTTPONLY)

### Problem: Payment Not Processing
```
Solution:
```
1. Check payment gateway API credentials
2. Verify cURL enabled di PHP
3. Check network/firewall blocking
4. Review API response di error logs

---

## 📞 Support

### Resources
- **Documentation**: README.md (file ini)
- **Issues**: Cek Troubleshooting section
- **Database**: Check phpMyAdmin untuk inspect data
- **Logs**: Check Apache logs untuk debugging

### Contact
- 📧 Email: support@playshop.id
- 💬 WhatsApp: +628123456789
- 🌐 Website: https://playshop.id

---

## 📜 License

Proprietary Software. All rights reserved.

---

## ✍️ Changelog

### Version 1.0.0 (Initial Release)
- ✅ Basic marketplace functionality
- ✅ User authentication
- ✅ Admin panel
- ✅ Payment integration
- ✅ WhatsApp notification

---

**Last Updated**: January 22, 2026

**Maintained by**: Playshop Development Team
