<?php
session_start();
require_once '../config/database.php';

if(isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple auth (username: admin, password: admin123)
    if($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <span class="logo-icon">🎮</span>
                <h2>Admin Panel</h2>
                <p>PLAYSHOP.ID</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            
            <div class="login-footer">
                <p>Default: admin / admin123</p>
                <a href="../index.php">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>