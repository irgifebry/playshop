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
                    <li><a href="promo.php">Promo</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php">Kontak</a></li>
                    <li><a href="register.php" class="active">Daftar</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="checkout-section">
        <div class="container">
            <div class="checkout-container" style="max-width: 620px;">
                <div class="content-header" style="margin-bottom: 1rem;">
                    <h1 style="margin:0;">Daftar Akun</h1>
                    <p style="margin: 6px 0 0; color: #6b7280;">Buat akun untuk akses promo, riwayat, dan notifikasi dummy.</p>
                </div>

                <?php if($success): ?>
                    <div class="alert success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-section">
                        <h3>Data Akun</h3>
                        <div class="form-row">
                            <input type="text" name="name" placeholder="Nama lengkap" required>
                            <input type="text" name="phone" placeholder="No. WhatsApp (08xxxxxxxxxx)" required>
                        </div>
                        <div class="form-row" style="margin-top: 10px; grid-template-columns: 1fr;">
                            <input type="email" name="email" placeholder="email@example.com" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Password</h3>
                        <div class="form-row" style="grid-template-columns: 1fr;">
                            <input type="password" name="password" placeholder="Minimal 6 karakter" minlength="6" required>
                        </div>
                        <p class="form-hint">🔒 Password akan disimpan dalam bentuk hash (tidak plain text).</p>
                    </div>

                    <button type="submit" class="btn-checkout">DAFTAR SEKARANG</button>
                </form>

                <div style="margin-top: 1.25rem;">
                    <a href="login.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Sudah punya akun? Login</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 PLAYSHOP.ID - Transaksi Cepat & Aman</p>
        </div>
    </footer>
</body>
</html>
