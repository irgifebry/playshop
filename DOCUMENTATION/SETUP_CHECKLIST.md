# ✅ PLAYSHOP Setup Checklist

Checklist lengkap untuk memastikan Playshop siap digunakan.

---

## 🔧 Pre-Installation Checklist

### System Requirements
- [ ] OS yang support (Windows/Linux/Mac)
- [ ] XAMPP/Apache installed
- [ ] MySQL installed (5.7+)
- [ ] PHP installed (7.4+)
- [ ] Internet connection

### Software Verification
```bash
# Verify PHP installed
php -v

# Verify MySQL installed
mysql --version

# Verify Apache running
# Windows: Check XAMPP Control Panel
# Linux: sudo systemctl status apache2
```

---

## 📦 Installation Checklist

### Step 1: Download & Extract
- [ ] Project downloaded/cloned
- [ ] Extracted to `C:\xampp\htdocs\playshop`
- [ ] All files present (check folder structure)

### Step 2: Database Setup
- [ ] MySQL service started
- [ ] Database `playshop_db` created
- [ ] Schema imported (`database/schema.sql`)
- [ ] Tables created (verify via phpMyAdmin)

```bash
# Verification commands:
mysql -u root -e "SHOW DATABASES;" | grep playshop
mysql -u root playshop_db -e "SHOW TABLES;"
```

### Step 3: Configuration
- [ ] `config/database.php` updated with correct credentials
- [ ] Host: localhost ✓
- [ ] Database: playshop_db ✓
- [ ] Username: root ✓
- [ ] Password: (empty for XAMPP default) ✓

### Step 4: Permissions (Linux/Mac)
- [ ] `chmod -R 755 playshop/` ✓
- [ ] `chmod -R 775 uploads/` ✓
- [ ] `chmod -R 775 storage/` ✓

### Step 5: Apache Configuration
- [ ] Apache mod_rewrite enabled
- [ ] `.htaccess` file exists
- [ ] Virtual host configured (if needed)

---

## 🌐 Website Access Checklist

### Test Homepage
- [ ] http://localhost/playshop/ loads
- [ ] Homepage displays correctly
- [ ] Navigation menu visible
- [ ] Games list showing
- [ ] No error messages

### Test Navigation
- [ ] Home link works
- [ ] Games section working
- [ ] Promo page loads
- [ ] FAQ page loads
- [ ] Contact page loads
- [ ] About page loads

### Test User Pages
- [ ] Login page accessible
- [ ] Register page accessible
- [ ] Contact form accessible
- [ ] Check Order page accessible

---

## 👤 User Account Checklist

### Create Test Account
```
Email: test@example.com
Password: test123
Name: Test User
Phone: 081234567890
```

- [ ] Registration page works
- [ ] Email validation working
- [ ] Account created successfully
- [ ] Can login with new account
- [ ] Profile page accessible

### Test Default Account
```
Email: user@test.com
Password: test123
```

- [ ] Can login
- [ ] Profile visible
- [ ] History visible (empty if new)

---

## 🎮 Game & Product Checklist

### Verify Games Data
- [ ] Games exist in database
```bash
mysql -u root playshop_db -e "SELECT COUNT(*) FROM games;"
```

- [ ] At least 1 game visible on homepage
- [ ] Game detail page loads
- [ ] Products showing for each game

### Verify Products Data
- [ ] Products exist in database
```bash
mysql -u root playshop_db -e "SELECT COUNT(*) FROM products;"
```

- [ ] Products showing in game detail
- [ ] Product prices visible
- [ ] Can select product

---

## 💳 Checkout Flow Checklist

### Test Purchase Flow
1. [ ] Login as test user
2. [ ] Select game from homepage
3. [ ] Click "Buy Now"
4. [ ] Choose product/nominal
5. [ ] Enter game user ID
6. [ ] Enter server/zone
7. [ ] Click "Checkout"
8. [ ] Checkout page loads correctly
9. [ ] Product info displayed
10. [ ] Total price calculated
11. [ ] Payment method selectable
12. [ ] Click "Pay Now"
13. [ ] Payment page loads
14. [ ] Order created successfully
15. [ ] Success page shows
16. [ ] Order ID displayed

### Verify Order Created
```bash
mysql -u root playshop_db -e "SELECT * FROM transactions ORDER BY created_at DESC LIMIT 1;"
```

---

## 📊 Admin Panel Checklist

### Access Admin Panel
```
URL: http://localhost/playshop/admin/login.php
Email: admin@playshop.id
Password: admin123
```

- [ ] Admin login page loads
- [ ] Can login with admin credentials
- [ ] Dashboard displays
- [ ] No permission errors

### Admin Dashboard
- [ ] Dashboard visible
- [ ] Analytics/stats showing
- [ ] Recent orders listed
- [ ] Navigation menu visible

### Admin Menus
- [ ] Dashboard accessible ✓
- [ ] Users page accessible ✓
- [ ] Games page accessible ✓
- [ ] Products page accessible ✓
- [ ] Orders page accessible ✓
- [ ] Discounts page accessible ✓
- [ ] Reports page accessible ✓
- [ ] Settings page accessible ✓
- [ ] Banners page accessible ✓

### Admin Operations (Optional)
- [ ] Can add new game
- [ ] Can add new product
- [ ] Can create discount code
- [ ] Can view orders
- [ ] Can view users

---

## 🔑 Security Checklist

### Passwords
- [ ] Admin password changed from default
- [ ] Test user password set
- [ ] No passwords in code/comments

### Database
- [ ] Database credentials correct
- [ ] MySQL user created (not root if possible)
- [ ] User has only necessary privileges

### Configuration
- [ ] Error reporting configured for production
- [ ] No sensitive info in public files
- [ ] `.htaccess` protects sensitive folders
- [ ] Upload folder doesn't execute code

### Backups
- [ ] Database backup routine planned
- [ ] Backup location identified
- [ ] Restore procedure tested

---

## 📧 Email Configuration (Optional)

### Email Setup
- [ ] SMTP configured
- [ ] Gmail: App password generated
- [ ] Test email sent from admin panel
- [ ] Email received successfully

### WhatsApp Setup (Optional)
- [ ] WhatsApp API configured
- [ ] Business phone number set
- [ ] API credentials verified
- [ ] Test message sent

---

## 🧪 API Testing Checklist

### Test API Endpoints
```bash
# Get games
curl http://localhost/playshop/api/games.php

# Get products
curl http://localhost/playshop/api/products.php?game_id=1

# Register
curl -X POST http://localhost/playshop/api/register.php \
  -H "Content-Type: application/json" \
  -d '{"name":"API Test","email":"apitest@test.com","phone":"081234567890","password":"test123","confirm_password":"test123"}'

# Login
curl -X POST http://localhost/playshop/api/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"apitest@test.com","password":"test123"}'

# Check status
curl http://localhost/playshop/api/status.php?email=test@example.com
```

- [ ] GET /api/games.php returns JSON
- [ ] GET /api/products.php?game_id=1 returns JSON
- [ ] POST /api/register.php creates user
- [ ] POST /api/login.php authenticates user
- [ ] GET /api/status.php returns order status

---

## 📚 Documentation Checklist

### Available Documentation
- [ ] README.md present and readable
- [ ] QUICKSTART.md present
- [ ] API_REFERENCE.md present
- [ ] DEVELOPER_GUIDE.md present
- [ ] ADMIN_DEPLOYMENT.md present
- [ ] FAQ_TROUBLESHOOTING.md present
- [ ] DOCS_INDEX.md present

### Documentation Review
- [ ] Skimmed README.md
- [ ] Checked QUICKSTART.md
- [ ] Reviewed relevant docs for your role

---

## 🎯 Role-Based Final Checklist

### For Users
- [ ] Can register account
- [ ] Can login
- [ ] Can browse games
- [ ] Can view game details
- [ ] Can purchase top up
- [ ] Can check order status
- [ ] Understand how to use platform

### For Admins
- [ ] Can access admin panel
- [ ] Can view dashboard
- [ ] Can manage games
- [ ] Can manage products
- [ ] Can create discounts
- [ ] Can view orders
- [ ] Can view reports
- [ ] Can update settings
- [ ] Understand admin features

### For Developers
- [ ] Project structure understood
- [ ] Development environment setup
- [ ] Can access/modify code
- [ ] Can run API endpoints
- [ ] Can add new features
- [ ] Understand database structure
- [ ] Can deploy to server

---

## 🚀 Go-Live Checklist (Production Deployment)

### Before Deploying
- [ ] Code reviewed
- [ ] All tests passed
- [ ] Database backed up
- [ ] Server requirements met
- [ ] Security audit done

### Deployment
- [ ] Code uploaded to server
- [ ] Database created on server
- [ ] Configuration updated for server
- [ ] SSL certificate installed
- [ ] DNS configured
- [ ] Email service configured
- [ ] Payment gateway configured
- [ ] WhatsApp configured
- [ ] Monitoring setup

### Post-Deployment
- [ ] Website accessible at custom domain
- [ ] HTTPS working
- [ ] All features tested
- [ ] Admin panel working
- [ ] Email sending
- [ ] Payment processing
- [ ] Orders being recorded
- [ ] Database backups automated
- [ ] Error logging active
- [ ] Performance monitoring enabled

---

## 🔄 Regular Maintenance Checklist

### Daily
- [ ] Check order status
- [ ] Monitor for errors
- [ ] Verify payments

### Weekly
- [ ] Review error logs
- [ ] Check disk space
- [ ] Check database integrity
- [ ] Monitor performance

### Monthly
- [ ] Database optimization
- [ ] Security updates
- [ ] Performance analysis
- [ ] Database backup verification
- [ ] Review reports

### Quarterly
- [ ] Security audit
- [ ] Code review
- [ ] Feature planning
- [ ] User feedback review
- [ ] Scalability assessment

---

## ✅ Sign-Off

**Setup Completed By:** ________________________
**Date:** ________________________
**Notes:** ________________________

---

## 📝 Quick Reference

### Folder Locations
```
Project: C:\xampp\htdocs\playshop
Database: playshop_db
Admin: http://localhost/playshop/admin/
phpMyAdmin: http://localhost/phpmyadmin
```

### Important Files
```
config/database.php     - Database connection
database/schema.sql     - Database schema
admin/dashboard.php     - Admin dashboard
includes/auth.php       - Authentication
api/                    - REST API endpoints
```

### Default Credentials
```
User Account:
Email: user@test.com
Password: test123

Admin Account:
Email: admin@playshop.id
Password: admin123

Database:
Username: root
Password: (empty)
```

### Useful Commands
```bash
# Start services
# XAMPP: Click "Start" in Control Panel

# Access MySQL
mysql -u root playshop_db

# Backup database
mysqldump -u root playshop_db > backup.sql

# View error logs
tail -f /var/log/playshop/php_error.log

# Check Apache
sudo systemctl status apache2
```

---

**Setup Complete! 🎉**

You can now use Playshop. Start with:
1. Login as test user
2. Try buying top up
3. Access admin panel
4. Explore features

Need help? Check [DOCS_INDEX.md](DOCS_INDEX.md) for documentation!

---

**Last Updated**: January 22, 2026
