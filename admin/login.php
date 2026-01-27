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
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="auth-logo-container" style="margin-top: -1rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center;">
                    <img src="../assets/logo.png" alt="Logo" class="auth-logo" style="height: 100px; width: auto; max-width: 100%; object-fit: contain;">
                </div>
                <h2>Admin Login</h2>
                <p>Silakan masuk ke panel kontrol</p>
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
                <button type="submit" class="btn-login" style="margin-top: 10px;">Masuk Sekarang</button>
            </form>
            
            <div class="login-footer">
                <p style="margin-bottom: 10px; color: #9ca3af; font-size: 0.8rem;">Default: admin / admin123</p>
                <a href="../index.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>