<?php
session_start();
require_once 'config/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_POST['order_id'];

// Update status transaksi
$stmt = $pdo->prepare("UPDATE transactions SET status = 'success', updated_at = NOW() WHERE order_id = ?");
$stmt->execute([$order_id]);

// Ambil detail transaksi
$stmt = $pdo->prepare("SELECT t.*, g.name as game_name, p.name as product_name 
                       FROM transactions t 
                       JOIN games g ON t.game_id = g.id 
                       JOIN products p ON t.product_id = p.id 
                       WHERE t.order_id = ?");
$stmt->execute([$order_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <span class="logo-icon">🎮</span>
                    <span class="logo-text">PLAYSHOP<span class="highlight">.ID</span></span>
                </div>
            </div>
        </nav>
    </header>

    <section class="success-section">
        <div class="container">
            <div class="progress-steps">
                <div class="step completed">
                    <div class="step-number">✓</div>
                    <div class="step-label">Pilih Produk</div>
                </div>
                <div class="step-line"></div>
                <div class="step completed">
                    <div class="step-number">✓</div>
                    <div class="step-label">Pembayaran</div>
                </div>
                <div class="step-line"></div>
                <div class="step active">
                    <div class="step-number">✓</div>
                    <div class="step-label">Selesai</div>
                </div>
            </div>

            <div class="success-container">
                <div class="success-icon">✅</div>
                <h1 class="success-title">Pembayaran Berhasil!</h1>
                <p class="success-subtitle">Top up kamu sedang diproses</p>

                <div class="success-details">
                    <h3>Detail Transaksi</h3>
                    <table class="detail-table">
                        <tr>
                            <td>Order ID</td>
                            <td><strong><?php echo $transaction['order_id']; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Game</td>
                            <td><?php echo $transaction['game_name']; ?></td>
                        </tr>
                        <tr>
                            <td>Produk</td>
                            <td><?php echo $transaction['product_name']; ?></td>
                        </tr>
                        <tr>
                            <td>User ID</td>
                            <td><?php echo $transaction['user_id']; ?></td>
                        </tr>
                        <tr>
                            <td>Total Bayar</td>
                            <td><strong>Rp <?php echo number_format($transaction['amount'], 0, ',', '.'); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><span class="status-badge success">Berhasil</span></td>
                        </tr>
                    </table>
                </div>

                <div class="success-info">
                    <p>⚡ Diamond/UC akan masuk ke akun kamu dalam <strong>1-5 menit</strong></p>
                    <p>📧 Bukti transaksi telah dikirim ke email kamu</p>
                </div>

                <div class="success-actions">
                    <a href="index.php" class="btn-primary">Kembali ke Beranda</a>
                    <button onclick="window.print()" class="btn-secondary">Cetak Bukti</button>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Auto confetti effect
        setTimeout(() => {
            for(let i = 0; i < 50; i++) {
                createConfetti();
            }
        }, 500);

        function createConfetti() {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.animationDelay = Math.random() * 3 + 's';
            confetti.style.backgroundColor = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'][Math.floor(Math.random() * 4)];
            document.body.appendChild(confetti);
            setTimeout(() => confetti.remove(), 3000);
        }
    </script>
</body>
</html>

<!-- ============ FILE 6: admin/login.php ============ -->
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