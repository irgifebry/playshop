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
                    <li><a href="check-order.php">Cek Order</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php">Kontak</a></li>
                    <li><a href="about.php">Tentang</a></li>
                    <li><a href="login.php" class="active">Login</a></li>
                    <li><a href="admin/login.php">Admin</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="checkout-section">
        <div class="container">
            <div class="checkout-container" style="max-width: 520px;">
                <div class="content-header" style="margin-bottom: 1rem;">
                    <h1 style="margin:0;">Login</h1>
                    <p style="margin: 6px 0 0; color: #6b7280;">Masuk untuk melihat profil & riwayat transaksi.</p>
                </div>

                <?php if($error): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-section">
                        <h3>Email</h3>
                        <div class="form-row" style="grid-template-columns: 1fr;">
                            <input type="email" name="email" placeholder="email@example.com" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Password</h3>
                        <div class="form-row" style="grid-template-columns: 1fr;">
                            <input type="password" name="password" placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-checkout">LOGIN</button>
                </form>

                <div style="margin-top: 1.25rem; display:flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                    <a href="register.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Belum punya akun? Daftar</a>
                    <a href="index.php" style="color: #6b7280; text-decoration: none;">Lanjut tanpa login</a>
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
