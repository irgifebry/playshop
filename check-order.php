<?php
require_once 'config/database.php';

$transaction = null;
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    
    $stmt = $pdo->prepare("SELECT t.*, g.name as game_name, p.name as product_name 
                           FROM transactions t 
                           JOIN games g ON t.game_id = g.id 
                           JOIN products p ON t.product_id = p.id 
                           WHERE t.order_id = ?");
    $stmt->execute([$order_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$transaction) {
        $error = 'Order ID tidak ditemukan!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pesanan | PLAYSHOP.ID</title>
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
                    <li><a href="check-order.php" class="active">Cek Order</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="check-order-section">
        <div class="container">
            <div class="check-order-box">
                <h1>Cek Status Pesanan</h1>
                <p>Masukkan Order ID untuk melihat status transaksi Anda</p>

                <form method="POST" class="check-form">
                    <div class="form-group">
                        <input type="text" name="order_id" placeholder="Contoh: TRX17123456789" required>
                    </div>
                    <button type="submit" class="btn-submit">CEK STATUS</button>
                </form>

                <?php if($error): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if($transaction): ?>
                    <div class="order-result">
                        <div class="result-header">
                            <span class="status-badge <?php echo $transaction['status']; ?> large">
                                <?php 
                                $status_text = [
                                    'success' => '✅ Berhasil',
                                    'pending' => '⏳ Pending',
                                    'failed' => '❌ Gagal'
                                ];
                                echo $status_text[$transaction['status']];
                                ?>
                            </span>
                        </div>

                        <div class="result-details">
                            <h3>Detail Pesanan</h3>
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
                                    <td>Pembayaran</td>
                                    <td><?php echo $transaction['payment_method']; ?></td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td><strong>Rp <?php echo number_format($transaction['amount'], 0, ',', '.'); ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td><?php echo date('d M Y, H:i', strtotime($transaction['created_at'])); ?></td>
                                </tr>
                            </table>

                            <?php if($transaction['status'] === 'success'): ?>
                                <div class="success-info">
                                    <p>✅ Diamond/UC sudah masuk ke akun game Anda</p>
                                </div>
                            <?php elseif($transaction['status'] === 'pending'): ?>
                                <div class="pending-info">
                                    <p>⏳ Transaksi sedang diproses. Mohon tunggu 1-5 menit.</p>
                                </div>
                            <?php else: ?>
                                <div class="failed-info">
                                    <p>❌ Transaksi gagal. Silakan hubungi customer service.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
