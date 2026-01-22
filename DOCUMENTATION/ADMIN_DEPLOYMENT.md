# 🛠️ PLAYSHOP Admin & Deployment Guide

Panduan lengkap untuk admin dan deployment Playshop ke production.

## 📋 Tabel Isi

- [Admin Panel Overview](#admin-panel-overview)
- [User Management](#user-management)
- [Game Management](#game-management)
- [Product Management](#product-management)
- [Order Management](#order-management)
- [Discount Management](#discount-management)
- [Reports & Analytics](#reports--analytics)
- [Settings](#settings)
- [Deployment](#deployment)
- [Maintenance](#maintenance)
- [Troubleshooting](#troubleshooting)

---

## 🎛️ Admin Panel Overview

### Access Admin Panel
```
URL: http://localhost/playshop/admin/login.php
Email: admin@playshop.id
Password: admin123 (ubah segera!)
```

### Admin Dashboard Features
- 📊 Overview penjualan hari ini/bulan ini
- 📈 Chart performa
- ⏰ Order terbaru
- 👥 User terbaru
- ⚠️ Low stock items
- 💰 Revenue summary

### Dashboard Sections
| Bagian | Info |
|--------|------|
| Total Revenue | Total penjualan |
| Today Sales | Penjualan hari ini |
| Total Orders | Jumlah order |
| Active Users | User aktif |
| Pending Orders | Order yang belum diproses |
| Completed Orders | Order yang sudah selesai |

---

## 👥 User Management

### View All Users
```
Menu: Users > All Users
```

**Features:**
- ✅ List all registered users
- ✅ Filter by status, registration date
- ✅ Search by name/email
- ✅ View user details
- ✅ Ban/unban users
- ✅ Delete user account

### User Status
| Status | Meaning |
|--------|---------|
| Active | User aktif dapat melakukan transaksi |
| Banned | User tidak dapat login atau transaksi |

### How to Ban User
```
1. Users > All Users
2. Cari user yang ingin di-ban
3. Click "Ban" / "Status" button
4. Confirm action
```

### View User Orders
```
1. Users > All Users
2. Click nama user
3. Lihat semua order dari user tersebut
```

---

## 🎮 Game Management

### Add New Game

#### Step 1: Buka Games Menu
```
Admin > Games > Add Game
```

#### Step 2: Isi Form
```
Field:
- Game Name: [required] contoh: "Mobile Legends"
- Description: [optional] deskripsi game
- How to Top Up: [optional] tutorial top up
- FAQ: [optional] pertanyaan umum
- Minimum Price: [required] harga minimum
- Icon: [optional] emoji atau karakter
- Image: [optional] upload gambar
- Color Gradient: [optional] warna accent
```

#### Step 3: Save
```
Click "Save Game" button
Game akan muncul di homepage
```

### Edit Game

```
1. Games > All Games
2. Click game yang ingin diedit
3. Ubah field yang diperlukan
4. Click "Update"
```

### Delete Game

```
1. Games > All Games
2. Klik game
3. Click "Delete"
4. Confirm deletion
```

### Toggle Game Status

```
1. Games > All Games
2. Click toggle "Is Active" untuk hide/show game di homepage
```

---

## 📦 Product Management

### Add Product

#### Step 1: Select Game
```
Admin > Products > Add Product
Pilih game dari dropdown
```

#### Step 2: Isi Form Product
```
Field:
- Product Name: "12 Diamonds" (required)
- Price: 10000 (required)
- Stock: 0 (unlimited) atau angka spesifik (required)
- Description: Optional
- Is Active: Checkbox untuk visibility
```

#### Step 3: Save
```
Click "Add Product"
```

### Manage Stock

```
1. Products > All Products
2. Cari product
3. Edit stock quantity
4. Click "Update Stock"
```

**Note:** 
- Stock = NULL atau 0 = unlimited
- Stock > 0 = limited, akan berkurang per pembelian

### Bulk Import Products

```
1. Products > Bulk Import
2. Upload CSV file dengan format:
   game_id,name,price,stock
   1,12 Diamonds,10000,0
   1,50 Diamonds,45000,0
3. Click "Import"
```

---

## 📋 Order Management

### View All Orders

```
Admin > Orders > All Orders
```

**Filter:**
- By Date Range
- By Game
- By Status (pending, paid, processing, completed, failed)
- By User

### Order Status

| Status | Meaning | Action |
|--------|---------|--------|
| Pending | Menunggu pembayaran | Tunggu atau batalkan |
| Paid | Pembayaran diterima | Process order |
| Processing | Sedang diproses | Monitor progress |
| Completed | Selesai, items delivered | Close order |
| Failed | Pembayaran gagal | Retry or refund |
| Cancelled | Dibatalkan user/admin | Archive |

### Process Order

```
1. Orders > All Orders
2. Click order ID untuk detail
3. Click "Process Order"
4. Confirm status change ke "Processing"
5. Send notification to user
6. After delivery, mark as "Completed"
```

### Manual Payment Verification

```
1. Buka order detail
2. Jika "Pending" dan user sudah transfer:
   - Click "Verify Payment"
   - Verify bukti transfer dari user
   - Click "Confirm Payment"
3. Status akan change ke "Paid"
```

### Refund Order

```
1. Order > Detail
2. Click "Request Refund"
3. Verify refund details
4. Click "Process Refund"
5. Send refund notification to user
```

---

## 💰 Discount Management

### Create Voucher/Promo

#### Step 1: Add New Discount
```
Admin > Discounts > Add Discount
```

#### Step 2: Isi Form
```
Field:
- Voucher Code: [required] contoh "PROMO10"
- Type: [required] 
  - Percentage: diskon X% 
  - Fixed: diskon X nominal
- Amount: [required] angka diskon
- Description: [optional] deskripsi promo
- Expired Date: [optional] tanggal berakhir
- Usage Limit: [optional] max berapa kali
- Status: [required] active/inactive
```

#### Step 3: Save
```
Click "Save Discount"
Voucher siap digunakan
```

### Example Vouchers

#### 10% Discount
```
Code: PLAYSHOP10
Type: Percentage
Amount: 10
Expired: 2024-12-31
Usage Limit: 1000
```

#### Fixed Rp5000 Discount
```
Code: HEMAT5K
Type: Fixed
Amount: 5000
Expired: 2024-12-31
Usage Limit: 500
```

### Deactivate Voucher

```
1. Discounts > All Discounts
2. Click voucher
3. Change Status ke "Inactive"
4. Save
```

### View Voucher Usage

```
1. Discounts > All Discounts
2. Click voucher
3. Lihat:
   - Total Usage
   - Last Used
   - Remaining Limit
```

---

## 📊 Reports & Analytics

### Sales Report

```
Admin > Reports > Sales Report
```

**Available Metrics:**
- Total Revenue (hari, minggu, bulan, tahun)
- Total Orders
- Average Order Value
- Payment Methods Breakdown
- Top Games
- Top Products

### Generate Report

```
1. Reports > Sales Report
2. Select Date Range
3. Select Filters:
   - By Game
   - By Status
   - By Payment Method
4. Click "Generate"
5. Export as PDF/Excel (if available)
```

### Revenue by Game

```
Reports > Revenue by Game
Lihat:
- Game Name
- Total Revenue
- Order Count
- Average Price
```

### User Activity Report

```
Reports > User Activity
Lihat:
- Total Users
- New Users (periode)
- Active Users
- User by Location
- Top Buyers
```

---

## ⚙️ Settings

### General Settings

```
Admin > Settings > General
```

**Options:**
- Website Title
- Website Description
- Website Logo
- Contact Email
- Contact Phone
- Contact WhatsApp
- Website URL

### Email Settings

```
Admin > Settings > Email
```

**Configuration:**
- SMTP Host
- SMTP Port
- SMTP Username
- SMTP Password
- From Email
- From Name
- Signature

**Test Email:**
```
Click "Send Test Email" button
Verification email akan dikirim ke admin email
```

### WhatsApp Settings

```
Admin > Settings > WhatsApp
```

**Configuration:**
- API Endpoint
- API Token
- Business Phone Number
- Enable Notifications
- Messages Template

### Payment Gateway Settings

```
Admin > Settings > Payment Gateway
```

**Options:**
- Payment Method 1: Bank Transfer
- Payment Method 2: E-Wallet
- Test/Production Mode
- API Credentials

---

## 🚀 Deployment

### Pre-Deployment Checklist

- [ ] Database backup sudah dibuat
- [ ] Kode sudah di-review
- [ ] Error logging diaktifkan
- [ ] Security headers dikonfigurasi
- [ ] SSL certificate ready
- [ ] Domain name ready
- [ ] Server resources adequate

### Step 1: Server Setup

#### A. VPS/Server Requirements
```
- OS: Linux (Ubuntu 20.04+ recommended)
- RAM: 2GB minimum
- Disk: 20GB SSD minimum
- Bandwidth: Unlimited (recommended)
```

#### B. Install Required Software
```bash
# Update system
sudo apt update && apt upgrade -y

# Install Apache
sudo apt install apache2 -y

# Install PHP 7.4+
sudo apt install php php-{pdo,mysql,curl,mbstring,json} -y

# Install MySQL
sudo apt install mysql-server -y

# Enable Apache modules
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Step 2: Deploy Application

#### A. Upload Files via FTP/Git
```bash
# Using Git (recommended)
cd /var/www
git clone https://github.com/yourrepo/playshop.git
cd playshop

# Using FTP
# ftp user@server.com
# cd public_html
# put -r playshop /
```

#### B. Set Directory Permissions
```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/playshop

# Set permissions
sudo chmod -R 755 /var/www/playshop
sudo chmod -R 775 /var/www/playshop/uploads
sudo chmod -R 775 /var/www/playshop/storage
```

### Step 3: Database Setup

#### A. Create Database
```bash
mysql -u root -p

# In MySQL console:
CREATE DATABASE playshop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'playshop_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON playshop_db.* TO 'playshop_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### B. Import Schema
```bash
mysql -u playshop_user -p playshop_db < database/schema.sql
```

### Step 4: Configure Application

#### A. Update Database Config
Edit `config/database.php`:
```php
<?php
$host = 'localhost';
$dbname = 'playshop_db';
$username = 'playshop_user';
$password = 'strong_password_here'; // Ubah!

// Production mode - turn off error display
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/playshop/php_error.log');
?>
```

#### B. Create Log Directory
```bash
sudo mkdir -p /var/log/playshop
sudo chown www-data:www-data /var/log/playshop
sudo chmod 755 /var/log/playshop
```

### Step 5: SSL & HTTPS

#### A. Install SSL Certificate (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Request certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal
sudo systemctl enable certbot.timer
```

#### B. Force HTTPS
Edit `.htaccess` or Apache config:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### Step 6: Configure Email

Edit `includes/email.php`:
```php
$email_config = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_user' => 'your-email@gmail.com',
    'smtp_pass' => 'your-app-password', // Google App Password
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'PLAYSHOP.ID'
];
```

### Step 7: Verify Installation

```
1. Check website: https://yourdomain.com
2. Check admin: https://yourdomain.com/admin/login.php
3. Test registration
4. Test order
5. Check emails received
```

---

## 🔧 Maintenance

### Regular Tasks

#### Daily
- ✅ Check order status
- ✅ Monitor for errors
- ✅ Verify payments

#### Weekly
- ✅ Review reports
- ✅ Check low stock items
- ✅ Verify email delivery
- ✅ Check disk space

#### Monthly
- ✅ Database backup
- ✅ Security updates
- ✅ Performance analysis
- ✅ Clean up old logs

### Database Maintenance

```bash
# Backup database
mysqldump -u playshop_user -p playshop_db > backup-$(date +%Y%m%d).sql

# Optimize tables
mysql -u playshop_user -p playshop_db -e "OPTIMIZE TABLE users, products, transactions;"

# Repair tables if corrupted
mysql -u playshop_user -p playshop_db -e "REPAIR TABLE users;"

# Check table integrity
mysqlcheck -u playshop_user -p playshop_db
```

### Log Management

```bash
# View recent errors
tail -f /var/log/playshop/php_error.log

# Check log size
du -sh /var/log/playshop/

# Archive old logs
gzip /var/log/playshop/php_error.log.20231201

# Delete logs older than 30 days
find /var/log/playshop -name "*.log" -mtime +30 -delete
```

### Update Application

```bash
# Using Git
cd /var/www/playshop
git pull origin main

# Fix permissions if needed
sudo chown -R www-data:www-data /var/www/playshop
sudo chmod -R 755 /var/www/playshop
```

---

## 🐛 Troubleshooting

### Problem: 404 Not Found

**Cause:** Apache mod_rewrite not enabled

**Solution:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Problem: Permission Denied

**Cause:** Directory permissions incorrect

**Solution:**
```bash
sudo chmod -R 755 /var/www/playshop
sudo chmod -R 775 /var/www/playshop/uploads
```

### Problem: Database Connection Error

**Cause:** Wrong credentials or database not running

**Solution:**
```bash
# Check MySQL status
sudo systemctl status mysql

# Verify credentials
mysql -u playshop_user -p -h localhost

# Check database exists
mysql -u playshop_user -p -e "SHOW DATABASES;"
```

### Problem: Email Not Sending

**Cause:** SMTP configuration error

**Solution:**
1. Check SMTP settings di Settings > Email
2. Verify SMTP credentials
3. Check firewall port 587 open
4. Enable "Less secure apps" (Gmail)
5. Use App Password (Gmail 2FA)

### Problem: Payment Not Processing

**Cause:** Payment gateway misconfiguration

**Solution:**
1. Verify payment gateway API key
2. Check API endpoint URL
3. Verify webhook configuration
4. Check cURL enabled: `php -m | grep curl`

### Problem: High CPU/Memory Usage

**Cause:** Inefficient queries or large datasets

**Solution:**
1. Optimize database queries
2. Add database indexes
3. Enable caching
4. Archive old data
5. Upgrade server resources

---

## 📱 Monitoring

### Setup Monitoring (Optional)

#### Using New Relic
```
1. Sign up: https://newrelic.com
2. Install New Relic agent
3. Monitor performance
4. Get alerts
```

#### Using DataDog
```
1. Sign up: https://www.datadoghq.com
2. Install agent
3. Monitor logs
4. Create dashboards
```

### Health Check

```
Create health check endpoint:
api/health.php

Returns:
{
  "ok": true,
  "status": "healthy",
  "database": "connected",
  "timestamp": "2024-01-09 12:00:00"
}
```

---

## 🔒 Security Hardening

### Final Security Checklist

- [ ] Change admin password
- [ ] Disable file listing: `Options -Indexes`
- [ ] Hide PHP version
- [ ] Set security headers
- [ ] Enable HTTPS
- [ ] Setup firewall
- [ ] Regular backups
- [ ] Monitor logs
- [ ] Update software regularly
- [ ] Implement rate limiting

### Recommended Apache Config

```apache
# Hide Apache version
ServerTokens Prod
ServerSignature Off

# Security headers
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Disable directory listing
Options -Indexes
```

---

**Last Updated**: January 22, 2026

**Version**: 1.0.0
