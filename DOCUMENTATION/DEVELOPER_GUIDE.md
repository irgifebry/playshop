# 👨‍💻 PLAYSHOP Developer Guide

Panduan untuk developer yang ingin mengembangkan dan memodifikasi Playshop.

## 📚 Tabel Isi

- [Project Structure](#project-structure)
- [Development Setup](#development-setup)
- [Code Standards](#code-standards)
- [Adding Features](#adding-features)
- [Database Changes](#database-changes)
- [Debugging](#debugging)
- [Best Practices](#best-practices)
- [Common Tasks](#common-tasks)

---

## 📁 Project Structure

### Root Level Files
```
playshop/
├── index.php              # Homepage
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # User logout
├── profile.php            # User profile
├── history.php            # Order history
├── checkout.php           # Checkout page
├── payment.php            # Payment page
├── success.php            # Success page
├── check-order.php        # Order status checker
├── game-detail.php        # Game details
├── promo.php              # Promo page
├── .gitignore             # Git ignore rules
└── README.md              # Main documentation
```

### /admin Folder - Admin Panel
```
admin/
├── login.php              # Admin login
├── dashboard.php          # Admin dashboard
├── sidebar.php            # Sidebar component (included in all pages)
├── games.php              # Manage games (CRUD)
├── products.php           # Manage products (CRUD)
├── users.php              # Manage users
├── discounts.php          # Manage vouchers
├── banners.php            # Manage banners
├── reports.php            # View reports
├── settings.php           # Website settings
├── transaction-detail.php # Transaction details
└── logout.php             # Admin logout
```

### /api Folder - REST API
```
api/
├── games.php              # GET - List all games
├── products.php           # GET - List products by game
├── login.php              # POST - User login
├── register.php           # POST - User registration
├── order-create.php       # POST - Create order
├── order.php              # GET - Get order detail
└── status.php             # GET - Check order status
```

### /config Folder - Configuration
```
config/
└── database.php           # Database connection & config
```

### /includes Folder - Helper Functions
```
includes/
├── auth.php               # Authentication helpers
├── db_utils.php           # Database utilities
├── email.php              # Email sending
├── upload.php             # File upload handling
├── voucher.php            # Voucher/discount logic
├── whatsapp.php           # WhatsApp integration
└── payment_gateway_dummy.php  # Payment gateway stub
```

### /database Folder - Database
```
database/
└── schema.sql             # Database schema & seed data
```

### /css & /js - Frontend Assets
```
css/
└── style.css              # Main stylesheet

js/
└── script.js              # Main JavaScript
```

### /uploads Folder - User Uploads
```
uploads/
└── games/                 # Game images
```

---

## 🛠️ Development Setup

### 1. Setup IDE/Editor

#### VS Code Setup
```json
// .vscode/settings.json
{
  "php.validate.executablePath": "C:\\xampp\\php\\php.exe",
  "php.validate.run": "onType",
  "[php]": {
    "editor.formatOnSave": true,
    "editor.defaultFormatter": "bmewburn.vscode-intelephense-client"
  }
}
```

#### Recommended Extensions
- **Intelephense** - PHP intellisense
- **PHP Debug** - XDebug debugger
- **Live Server** - Live preview
- **Thunder Client** - API testing
- **MySQL** - Database management

### 2. Install PHP Debugger (Xdebug)

**Windows:**
```bash
# Download php_xdebug DLL sesuai PHP version
# Copy ke: C:\xampp\php\ext\

# Edit C:\xampp\php\php.ini, tambah:
[xdebug]
zend_extension = php_xdebug.dll
xdebug.mode = debug
xdebug.start_with_request = yes
xdebug.client_port = 9003
```

**Linux/Mac:**
```bash
# Install via pecl
pecl install xdebug

# Edit php.ini
[xdebug]
zend_extension=xdebug.so
xdebug.mode=debug
xdebug.start_with_request=yes
```

### 3. Enable Error Reporting

Edit `config/database.php`:
```php
<?php
// Development mode
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// For production
// error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
// ini_set('display_errors', 0);
```

---

## 📋 Code Standards

### PHP Style Guide

#### Naming Conventions
```php
// Classes - PascalCase
class UserManager { }
class OrderProcessor { }

// Functions/Methods - camelCase
function validateEmail($email) { }
public function processPayment($amount) { }

// Constants - UPPER_SNAKE_CASE
const DATABASE_TIMEOUT = 30;
const MAX_UPLOAD_SIZE = 5242880;

// Variables - snake_case
$user_id = $_SESSION['user_id'];
$order_total = 0;
```

#### File Organization
```php
<?php
// 1. Declare namespace (if using)
namespace App\Controllers;

// 2. Include files
require_once '../config/database.php';
require_once '../includes/auth.php';

// 3. Session start
session_start();

// 4. Class definition
class UserController { }

// 5. Function calls
$user = new UserController();
```

#### Indentation & Formatting
```php
// Use 4 spaces per indentation level
if ($condition) {
    $result = true;
    foreach ($array as $item) {
        echo $item;
    }
}

// Space after control structures
if ($x > 5) { }
for ($i = 0; $i < 10; $i++) { }

// Space around operators
$total = $price + $tax;
$name = 'John ' . 'Doe';
```

#### Comments
```php
// Single line comments
// TODO: Implement validation

/* 
   Multi-line comments
   Use for complex logic
*/

/**
 * Function documentation
 * @param string $email User email
 * @param int $id User ID
 * @return bool Success status
 */
function sendEmail($email, $id) {
    // Implementation
}
```

### HTML/CSS Standards

#### HTML Structure
```html
<!-- Proper closing tags -->
<form method="POST">
    <input type="text" name="username">
    <button type="submit">Submit</button>
</form>

<!-- Semantic HTML -->
<header>
    <nav class="navbar">
        <!-- Navigation items -->
    </nav>
</header>

<main>
    <!-- Main content -->
</main>

<footer>
    <!-- Footer content -->
</footer>
```

#### CSS Naming
```css
/* BEM (Block Element Modifier) convention */
.card { }               /* Block */
.card__header { }       /* Element */
.card--featured { }     /* Modifier */

/* Utility classes */
.text-center { }
.margin-top-2 { }
.display-flex { }
```

### JavaScript Standards

```javascript
// Use const by default
const userName = 'John';

// Use let for variables that change
let counter = 0;

// Avoid var
var oldStyle = 'deprecated'; // ❌

// Function declaration
function calculateTotal(items) {
    return items.reduce((sum, item) => sum + item.price, 0);
}

// Arrow functions
const getUser = (id) => {
    return fetch(`/api/users/${id}`);
};
```

---

## ✨ Adding Features

### Add New Public Page

#### 1. Create Page File
```php
<?php
// Example: help.php
session_start();
require_once 'config/database.php';
require_once 'includes/db_utils.php';

// Get data if needed
$data = [];
try {
    // Query data
} catch (Exception $e) {
    // Handle error
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help - PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Include header component if available -->
    
    <main class="container">
        <!-- Page content -->
    </main>
    
    <!-- Include footer component if available -->
    
    <script src="js/script.js"></script>
</body>
</html>
```

#### 2. Add Navigation Link
Edit relevant page or navbar component:
```html
<li><a href="help.php">Help</a></li>
```

---

### Add Admin Feature

#### 1. Create Admin Page
```php
<?php
// admin/help-articles.php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check admin access
require_admin_login();

// Get all help articles
try {
    $stmt = $pdo->query("SELECT * FROM help_articles ORDER BY created_at DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Help Articles - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <h1>Help Articles</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($article['title']); ?></td>
                            <td><?php echo $article['created_at']; ?></td>
                            <td>
                                <a href="edit-article.php?id=<?php echo $article['id']; ?>">Edit</a>
                                <a href="#" onclick="deleteArticle(<?php echo $article['id']; ?>)">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
```

#### 2. Update Sidebar
Edit `admin/sidebar.php`:
```php
<li>
    <a href="help-articles.php">Help Articles</a>
</li>
```

---

### Add New API Endpoint

#### 1. Create API File
```php
<?php
// api/help.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Get help articles
try {
    $search = $_GET['search'] ?? '';
    
    if ($search) {
        $stmt = $pdo->prepare("
            SELECT id, title, content 
            FROM help_articles 
            WHERE title LIKE :search OR content LIKE :search
            LIMIT 10
        ");
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt = $pdo->query("
            SELECT id, title, content 
            FROM help_articles 
            LIMIT 10
        ");
    }
    
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'ok' => true,
        'data' => $articles
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'server error'
    ]);
}
?>
```

#### 2. Test API
```bash
curl "http://localhost/playshop/api/help.php?search=payment"
```

---

## 🗄️ Database Changes

### Add New Table

#### 1. Create Migration (schema update)
```sql
-- database/schema.sql - Add this:

CREATE TABLE IF NOT EXISTS help_articles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  content LONGTEXT NOT NULL,
  category VARCHAR(50) NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_active (is_active)
);
```

#### 2. Apply Migration
```bash
# Method 1: Command line
mysql -u root playshop_db < database/schema.sql

# Method 2: phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select database
# 3. Go to Import
# 4. Upload schema file
```

#### 3. Create DB Utilities (Optional)
```php
<?php
// includes/help_utils.php

function createHelpArticle($title, $content, $category) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO help_articles (title, content, category)
        VALUES (:title, :content, :category)
    ");
    return $stmt->execute([
        'title' => $title,
        'content' => $content,
        'category' => $category
    ]);
}

function getHelpArticle($id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM help_articles WHERE id = :id AND is_active = 1
    ");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
```

---

## 🐛 Debugging

### Using XDebug with VS Code

#### 1. Setup Debug Configuration
Create `.vscode/launch.json`:
```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for XDebug",
            "type": "php",
            "port": 9003,
            "pathMapping": {
                "/playshop": "${workspaceRoot}"
            }
        }
    ]
}
```

#### 2. Set Breakpoints
- Click line number in VS Code to set breakpoint
- Start debugger: F5
- Navigate to page in browser
- Debugger will pause at breakpoint

#### 3. Inspect Variables
- Hover over variables to see values
- Use Debug Console to execute code
- Use Watch to monitor specific variables

### Using Console Output

```php
<?php
// Log to file
error_log("User ID: " . $user_id);
error_log(print_r($array, true));

// Log to screen (development only)
echo '<pre>';
var_dump($variable);
echo '</pre>';

// Conditional logging
if (isset($_GET['debug'])) {
    echo 'Debug: ' . $value;
}
?>
```

### Check Error Logs

```bash
# View Apache error log
tail -f C:\xampp\apache\logs\error.log

# View PHP error log
tail -f C:\xampp\php\logs\php_error.log

# View application error log
tail -f error.log
```

---

## ✅ Best Practices

### Security

```php
<?php
// 1. Always validate input
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    throw new Exception('Invalid email');
}

// 2. Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);

// 3. Sanitize output
echo htmlspecialchars($user_input);

// 4. Use password hashing
$hash = password_hash($password, PASSWORD_BCRYPT);
$verified = password_verify($password, $hash);

// 5. Set secure headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
?>
```

### Performance

```php
<?php
// 1. Use database indexes
CREATE INDEX idx_email ON users(email);

// 2. Minimize database queries
// ❌ Bad: Query in loop
foreach ($users as $user) {
    $orders = getOrders($user['id']);
}

// ✅ Good: Batch query
$orders = getAllOrdersByUserIds($user_ids);

// 3. Cache frequently accessed data
$cache_key = 'games_list';
if (apcu_exists($cache_key)) {
    $games = apcu_fetch($cache_key);
} else {
    $games = getAllGames();
    apcu_store($cache_key, $games, 3600); // 1 hour
}
?>
```

### Code Organization

```php
<?php
// 1. Separate concerns
// config/ - configuration
// includes/ - business logic & utilities
// api/ - API endpoints
// admin/ - admin panel

// 2. Use helper functions
require_once 'includes/db_utils.php';
$user = getUserById($user_id);

// 3. Error handling
try {
    $pdo->beginTransaction();
    // Do something
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log($e->getMessage());
}
?>
```

---

## 🔧 Common Tasks

### Clear Cache
```php
<?php
// Clear APCu cache
apcu_clear_cache();

// Clear file cache
$cache_files = glob('cache/*');
foreach ($cache_files as $file) {
    unlink($file);
}
?>
```

### Send Email
```php
<?php
require_once 'includes/email.php';
send_email($to_email, $subject, $message);
?>
```

### Upload File
```php
<?php
require_once 'includes/upload.php';
$file = handle_image_upload($_FILES['image'], 'uploads/games/');
?>
```

### Send WhatsApp
```php
<?php
require_once 'includes/whatsapp.php';
send_whatsapp($phone, $message);
?>
```

### Create Backup
```bash
# Database backup
mysqldump -u root playshop_db > backup-$(date +%Y%m%d-%H%M%S).sql

# Full project backup
tar -czf playshop-backup-$(date +%Y%m%d).tar.gz playshop/
```

### Deploy to Server
```bash
# 1. Upload via FTP
# ftp user@server.com

# 2. Or use Git
git push production main

# 3. SSH and pull
ssh user@server.com
cd /var/www/playshop
git pull origin main
chmod -R 755 uploads/ storage/
```

---

## 📚 Additional Resources

- [PHP Manual](https://www.php.net/manual/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MDN Web Docs](https://developer.mozilla.org/)
- [GitHub - Playshop](https://github.com/yourrepo/playshop)

---

**Last Updated**: January 22, 2026

**Version**: 1.0.0

---
