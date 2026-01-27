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
        if (($user['status'] ?? 'active') !== 'active') {
            $error = 'Akun Anda sedang dinonaktifkan.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: index.php');
            exit;
        }
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include "includes/header.php"; ?>

    <main class="auth-page">
        <div class="login-container">
            <div class="login-box">
                <div class="login-header">
                    <a href="index.php" style="text-decoration: none; color: inherit;">
                        <h2 style="font-size: 1.5rem;">Selamat Datang</h2>
                        <p style="font-size: 0.85rem;">Masuk ke akun <strong>PLAYSHOP.ID</strong> Anda</p>
                    </a>
                </div>

                <?php if($error): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="nama@email.com" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn-login" style="margin-top: 10px;">MASUK</button>
                </form>

                <div class="login-footer">
                    <p style="margin-bottom: 8px;">Belum punya akun? <a href="register.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Daftar Sekarang</a></p>
                    <a href="index.php" style="color: #9ca3af; text-decoration: none; font-size: 0.9rem;">Lanjut ke Beranda →</a>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>


