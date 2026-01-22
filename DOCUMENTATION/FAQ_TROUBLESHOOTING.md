# ❓ PLAYSHOP FAQ & Troubleshooting

Kumpulan pertanyaan umum dan solusi masalah untuk Playshop.

---

## 📋 Tabel Isi

- [Installation Issues](#installation-issues)
- [Login & Authentication](#login--authentication)
- [Database Issues](#database-issues)
- [Payment & Orders](#payment--orders)
- [File & Upload](#file--upload)
- [Email & Notifications](#email--notifications)
- [Performance Issues](#performance-issues)
- [API Issues](#api-issues)
- [General Questions](#general-questions)

---

## 🔧 Installation Issues

### Q: Error "Connection refused" saat setup
**Symptom:** 
```
Error: Connection failed: SQLSTATE[HY000] [2002]
```

**Causes & Solutions:**
1. MySQL tidak running
   ```bash
   # Windows
   # Buka XAMPP Control Panel
   # Click "Start" pada MySQL
   
   # Linux
   sudo systemctl start mysql
   ```

2. Wrong credentials di `config/database.php`
   ```php
   // Verifikasi:
   $host = 'localhost';      // Biasanya localhost
   $dbname = 'playshop_db';  // Nama database
   $username = 'root';        // MySQL user
   $password = '';            // Password (kosong untuk XAMPP default)
   ```

3. Database tidak ada
   ```bash
   # Create database
   mysql -u root < database/schema.sql
   ```

---

### Q: "Database not found" error
**Solution:**
```bash
# Step 1: Check database exists
mysql -u root -e "SHOW DATABASES;"

# Step 2: If not exists, create it
mysql -u root < database/schema.sql

# Step 3: Verify via phpMyAdmin
# Open http://localhost/phpmyadmin
# Check "playshop_db" exists in left sidebar
```

---

### Q: Blank white page saat akses website
**Solutions:**

1. **Enable error reporting:**
   ```php
   // Edit config/database.php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

2. **Check Apache error log:**
   ```bash
   # Windows
   C:\xampp\apache\logs\error.log
   
   # Linux/Mac
   /Applications/XAMPP/logs/apache_error.log
   ```

3. **Check PHP error log:**
   ```bash
   # Windows
   C:\xampp\php\logs\php_error.log
   
   # Linux/Mac  
   /Applications/XAMPP/logs/php_error.log
   ```

4. **Verify file permissions:**
   ```bash
   chmod -R 755 /var/www/playshop
   chmod -R 775 /var/www/playshop/uploads
   ```

---

### Q: 404 Not Found error
**Cause:** Apache mod_rewrite tidak enabled

**Solution:**
```bash
# Linux/Mac
sudo a2enmod rewrite
sudo systemctl restart apache2

# Windows (check .htaccess exists)
# File playshop/.htaccess should exist
# If not, create it with:
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /playshop/
</IfModule>
```

---

### Q: Permission denied error
**Solution:**
```bash
# Check current permissions
ls -la /var/www/playshop

# Fix permissions
sudo chmod -R 755 /var/www/playshop
sudo chmod -R 775 /var/www/playshop/uploads
sudo chmod -R 775 /var/www/playshop/storage

# Fix ownership
sudo chown -R www-data:www-data /var/www/playshop
```

---

## 🔐 Login & Authentication

### Q: "Email or password incorrect" tapi password sudah benar
**Solutions:**

1. **Check email is registered:**
   ```bash
   # Via MySQL
   mysql -u root playshop_db -e "SELECT id, email FROM users WHERE email='your@email.com';"
   
   # Via phpMyAdmin
   # Click users table
   # Search for your email
   ```

2. **Reset password:**
   ```php
   // If forgot password, update directly
   $hashed = password_hash('new_password123', PASSWORD_BCRYPT);
   // UPDATE users SET password='$hashed' WHERE email='your@email.com';
   ```

3. **Check user status:**
   ```bash
   mysql -u root playshop_db -e "SELECT email, status FROM users WHERE email='your@email.com';"
   # Make sure status = 'active' not 'banned'
   ```

---

### Q: Session lost setelah refresh page
**Causes & Solutions:**

1. **Check session config:**
   ```php
   // Must be at top of every page
   if (session_status() === PHP_SESSION_NONE) {
       session_start();
   }
   ```

2. **Check PHP session settings:**
   ```bash
   # View session settings
   php -i | grep session
   
   # Check session.save_path exists and is writable
   mkdir -p /var/lib/php/sessions
   chmod 1777 /var/lib/php/sessions
   ```

3. **Clear old sessions:**
   ```bash
   # Remove old session files
   find /var/lib/php/sessions -type f -mtime +1 -delete
   ```

---

### Q: Can't login to admin panel
**Check:**

1. **Is account admin?**
   ```bash
   mysql -u root playshop_db -e "SELECT * FROM admins WHERE email='admin@playshop.id';"
   ```

2. **Check admin exists:**
   ```php
   // If admin table doesn't exist, create one:
   // CREATE TABLE admins LIKE users;
   ```

3. **Reset admin password:**
   ```bash
   # In MySQL:
   mysql -u root playshop_db
   UPDATE admins SET password=SHA2('newpassword', 256) WHERE email='admin@playshop.id';
   ```

---

### Q: "You must be logged in" error
**Solution:**
```php
// Check session_start() is called at top
session_start();

// Then check login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
```

---

## 🗄️ Database Issues

### Q: "Table doesn't exist" error
**Solution:**
```bash
# Re-import schema
mysql -u root playshop_db < database/schema.sql

# Or via phpMyAdmin
# 1. Select database "playshop_db"
# 2. Go to "Import"
# 3. Choose "database/schema.sql"
# 4. Click "Go"
```

---

### Q: Database table is corrupted
**Symptoms:** Random errors, can't insert data, slow queries

**Solutions:**
```bash
# Check table integrity
mysqlcheck -u root -p playshop_db

# Repair corrupted table
mysql -u root playshop_db -e "REPAIR TABLE users;"

# Or via command line
mysqlcheck -u root -p --auto-repair playshop_db
```

---

### Q: Database runs out of space
**Solutions:**

1. **Check disk space:**
   ```bash
   df -h
   ```

2. **Archive old data:**
   ```sql
   -- Archive transactions older than 6 months
   CREATE TABLE transactions_archive LIKE transactions;
   INSERT INTO transactions_archive 
   SELECT * FROM transactions 
   WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
   
   DELETE FROM transactions 
   WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
   ```

3. **Clean up logs:**
   ```bash
   # Remove log files
   rm /var/log/playshop/php_error.log.*
   truncate -s 0 /var/log/playshop/php_error.log
   ```

---

### Q: Slow database queries
**Solutions:**

1. **Add indexes:**
   ```sql
   CREATE INDEX idx_email ON users(email);
   CREATE INDEX idx_status ON transactions(status);
   CREATE INDEX idx_game ON products(game_id);
   ```

2. **Analyze query:**
   ```sql
   EXPLAIN SELECT * FROM transactions WHERE user_id = 5;
   ```

3. **Optimize tables:**
   ```bash
   mysqlcheck -u root -p --optimize playshop_db
   ```

---

## 💰 Payment & Orders

### Q: Order stuck in "pending" status
**Solutions:**

1. **Check payment gateway:**
   - Verify payment credentials
   - Check API endpoint
   - Test payment gateway manually

2. **Manual verification:**
   ```bash
   # In admin panel:
   # Orders > Order Detail
   # Click "Verify Payment"
   # Confirm payment received
   ```

3. **Update status manually:**
   ```sql
   UPDATE transactions 
   SET status = 'completed' 
   WHERE order_id = 'ORD-1234567890';
   ```

---

### Q: Payment gateway not working
**Checks:**

1. **Verify credentials:**
   ```php
   // Check includes/payment_gateway_dummy.php
   // API keys correct?
   // Endpoint URL correct?
   ```

2. **Check cURL enabled:**
   ```bash
   php -m | grep curl
   
   # If not enabled, enable in php.ini
   extension=php_curl.dll  # Windows
   extension=curl.so       # Linux
   ```

3. **Test cURL:**
   ```bash
   curl -v https://api.payment-gateway.com/test
   ```

4. **Check firewall:**
   ```bash
   # Port 443 (HTTPS) should be open
   netstat -an | grep 443
   ```

---

### Q: Duplicate order created
**Solution:**
```bash
# Find duplicate
mysql -u root playshop_db -e "
SELECT order_id, COUNT(*) FROM transactions 
GROUP BY order_id HAVING COUNT(*) > 1;
";

# Delete duplicate (keep one)
DELETE FROM transactions WHERE id IN (
  SELECT id FROM (
    SELECT id FROM transactions 
    WHERE order_id = 'ORD-DUPLICATE' 
    LIMIT 1 OFFSET 1
  ) t
);
```

---

## 📁 File & Upload

### Q: File upload fails with "Permission denied"
**Solution:**
```bash
# Fix folder permissions
chmod -R 775 uploads/
chmod -R 775 storage/

# Fix ownership
chown -R www-data:www-data uploads/
chown -R www-data:www-data storage/
```

---

### Q: Uploaded image not showing
**Checks:**

1. **Verify file exists:**
   ```bash
   ls -la uploads/games/
   ```

2. **Check file permissions:**
   ```bash
   # Should be readable
   chmod 644 uploads/games/image.jpg
   ```

3. **Check image path in database:**
   ```bash
   mysql -u root playshop_db -e "SELECT image_path FROM games WHERE id=1;"
   ```

4. **Verify path in HTML:**
   ```html
   <!-- Should be: -->
   <img src="uploads/games/image.jpg">
   
   <!-- Not: -->
   <img src="/var/www/playshop/uploads/games/image.jpg">
   ```

---

### Q: Upload file size limit exceeded
**Solution:**
```php
// Edit php.ini
upload_max_filesize = 20M     # Max file size
post_max_size = 25M           # Max post size

// Restart Apache
sudo systemctl restart apache2
```

---

### Q: Folder uploads doesn't exist
**Solution:**
```bash
# Create uploads folder
mkdir -p uploads/games
mkdir -p uploads/images

# Set permissions
chmod -R 775 uploads/
```

---

## 📧 Email & Notifications

### Q: Emails not sending
**Checks:**

1. **Check SMTP settings:**
   ```php
   // includes/email.php
   echo "SMTP Host: " . $email_config['smtp_host'];
   echo "SMTP Port: " . $email_config['smtp_port'];
   ```

2. **Test email:**
   ```bash
   php -r "
   require_once 'includes/email.php';
   send_email('test@example.com', 'Test', 'This is a test');
   echo 'Email sent!';
   "
   ```

3. **Check error log:**
   ```bash
   tail -f /var/log/playshop/php_error.log
   ```

4. **Verify firewall:**
   ```bash
   # Port 587 (SMTP) should be open
   netstat -an | grep 587
   ```

---

### Q: Gmail emails not sending
**Solution:**

1. **Enable 2-Step Verification** in Google Account

2. **Create App Password:**
   - Go to https://myaccount.google.com/apppasswords
   - Select Mail & Windows Computer
   - Copy app password

3. **Update config:**
   ```php
   $email_config = [
       'smtp_host' => 'smtp.gmail.com',
       'smtp_port' => 587,
       'smtp_user' => 'your-email@gmail.com',
       'smtp_pass' => 'xxxx xxxx xxxx xxxx',  // App password, not Gmail password
       'from_email' => 'your-email@gmail.com',
       'from_name' => 'PLAYSHOP'
   ];
   ```

---

### Q: WhatsApp notifications not working
**Checks:**

1. **Verify phone number:**
   ```php
   // includes/whatsapp.php
   // Phone format: 628123456789 (no + or 0)
   ```

2. **Check API credentials:**
   ```php
   // API token correct?
   // API endpoint correct?
   ```

3. **Test WhatsApp:**
   ```bash
   php -r "
   require_once 'includes/whatsapp.php';
   send_whatsapp('628123456789', 'Test message');
   "
   ```

---

## ⚡ Performance Issues

### Q: Website loading very slow
**Checks:**

1. **Check CPU/Memory usage:**
   ```bash
   top -b -n 1
   free -h
   ```

2. **Analyze slow queries:**
   ```sql
   SELECT * FROM mysql.slow_log;
   SHOW PROCESSLIST;
   ```

3. **Enable query caching:**
   ```php
   // Use APCu
   $cache_key = 'games_list';
   if (apcu_exists($cache_key)) {
       $games = apcu_fetch($cache_key);
   } else {
       $games = getAllGames();
       apcu_store($cache_key, $games, 3600);
   }
   ```

4. **Upgrade server resources**

---

### Q: High memory usage
**Solutions:**

1. **Increase PHP memory limit:**
   ```php
   // php.ini
   memory_limit = 512M
   ```

2. **Restart Apache:**
   ```bash
   sudo systemctl restart apache2
   ```

3. **Kill hung processes:**
   ```bash
   ps aux | grep php
   kill -9 <PID>
   ```

---

## 🔌 API Issues

### Q: API endpoint returns 500 error
**Solution:**

1. **Check error log:**
   ```bash
   tail -f /var/log/playshop/php_error.log
   ```

2. **Test endpoint with verbose:**
   ```bash
   curl -v http://localhost/playshop/api/games.php
   ```

3. **Check database connection:**
   ```bash
   mysql -u root playshop_db -e "SELECT COUNT(*) FROM games;"
   ```

---

### Q: API authentication fails
**Solution:**

1. **Check session:**
   ```bash
   # Session must exist on server
   # Use browser with cookies enabled
   curl -c cookies.txt -b cookies.txt http://localhost/playshop/api/login.php
   ```

2. **Send credentials:**
   ```bash
   curl -X POST http://localhost/playshop/api/login.php \
     -H "Content-Type: application/json" \
     -d '{"email":"test@example.com","password":"test123"}'
   ```

---

### Q: JSON decode error in API
**Cause:** Invalid JSON in request

**Solution:**
```bash
# Validate JSON before sending
# Use -H "Content-Type: application/json"
# Quote strings properly

# Wrong:
curl -d '{name: John}'

# Right:
curl -d '{"name": "John"}' \
  -H "Content-Type: application/json"
```

---

## ❓ General Questions

### Q: Apa sistem pembayaran yang didukung?
**A:** 
- Bank Transfer (manual verification)
- E-Wallet (dummy gateway, dapat diintegrasikan dengan Midtrans, Xendit, etc)
- Custom payment gateway (di `includes/payment_gateway_dummy.php`)

---

### Q: Bagaimana cara custom domain?
**A:**
1. Update `includes/email.php` - FROM address
2. Update `.htaccess` - domain rewrite rules
3. Update admin settings - website URL
4. Update config - base URL

---

### Q: Bagaimana cara backup database?
**A:**
```bash
mysqldump -u root playshop_db > backup-$(date +%Y%m%d).sql

# Restore
mysql -u root playshop_db < backup-20240109.sql
```

---

### Q: Berapa banyak user yang bisa di-support?
**A:**
- Development: Unlimited
- Dengan server standar 2GB RAM: ~1000 concurrent users
- Dengan optimization: ~10000+ users
- Skalabilitas tergantung database tuning & server resources

---

### Q: Bagaimana cara integrasi payment gateway?
**A:**
1. Edit `includes/payment_gateway_dummy.php`
2. Replace dummy implementation dengan real API
3. Update webhook handler
4. Test thoroughly sebelum production

---

### Q: Bisa gak untuk reseller?
**A:**
Ya! Bisa di-customize untuk:
- Multi-vendor (setiap reseller punya dashboard)
- Commission system
- Payout management

Hubungi tim development untuk customization.

---

### Q: Bagaimana monitoring production?
**A:**
1. Setup error logging
2. Monitor server resources
3. Use third-party monitoring (New Relic, DataDog)
4. Setup alerts
5. Regular backups

---

## 🆘 Still Having Issues?

### Checklist
- [ ] Baca dokumentasi lengkap di [README.md](README.md)
- [ ] Check error logs
- [ ] Verify database connection
- [ ] Test with simple query
- [ ] Search ini FAQ
- [ ] Google error message
- [ ] Check server resources

### Contact Support
```
📧 Email: support@playshop.id
💬 WhatsApp: +628123456789
🌐 Website: https://playshop.id
```

---

**Last Updated**: January 22, 2026

**Version**: 1.0.0

---

*Semoga dokumentasi ini membantu! Jika ada pertanyaan, jangan ragu untuk menghubungi support.* 🚀
