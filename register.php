<?php
session_start();
require_once 'config/database.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $passwordRaw = (string)($_POST['password'] ?? '');

    if (strlen($passwordRaw) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if($stmt->rowCount() > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");
            if($stmt->execute([$name, $email, $phone, $password])) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Gagal registrasi. Coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include "includes/header.php"; ?>

    <main class="auth-page">
        <div class="login-container" style="max-width: 420px;">
            <div class="login-box">
                <div class="login-header">
                    <a href="index.php" style="text-decoration: none; color: inherit;">
                        <h2 style="font-size: 1.5rem;">Daftar Akun</h2>
                        <p style="font-size: 0.85rem;">Bergabung dengan <strong>PLAYSHOP.ID</strong></p>
                    </a>
                </div>

                <?php if($success): ?>
                    <div class="alert success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 1.2rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="budi@email.com" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>WhatsApp</label>
                            <input type="text" name="phone" placeholder="0812..." required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" minlength="6" required>
                        <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 5px;">🔒 Aman & Terenkripsi</p>
                    </div>

                    <button type="submit" class="btn-login" style="margin-top: 10px;">DAFTAR SEKARANG</button>
                </form>

                <div class="login-footer">
                    <p style="margin-bottom: 8px;">Sudah punya akun? <a href="login.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Login Disini</a></p>
                    <a href="index.php" style="color: #9ca3af; text-decoration: none; font-size: 0.9rem;">← Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>

    <style>
        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</body>
</html>


