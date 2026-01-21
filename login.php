<?php
session_start();
require_once 'config/database.php';

if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        header('Location: index.php');
        exit;
    } else {
        $error = 'Email atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <a href="index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                        <span class="logo-icon">🎮</span>
                        <span class="logo-text">PLAYSHOP<span class="highlight">.ID</span></span>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="register.php">Daftar</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="auth-section">
        <div class="container">
            <div class="auth-container">
                <div class="auth-box">
                    <div class="auth-header">
                        <h2>Login ke Akun Anda</h2>
                        <p>Selamat datang kembali!</p>
                    </div>

                    <?php if($error): ?>
                        <div class="alert error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Masukkan email" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Masukkan password" required>
                        </div>
                        
                        <button type="submit" class="btn-submit">LOGIN</button>
                    </form>

                    <div class="auth-footer">
                        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                        <p><a href="index.php">Lanjut tanpa login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>