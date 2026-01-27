<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

// Ensure user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'] ?? '';

$transaction = null;
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    
    // Build query with security check: order must belong to this user
    $query = "SELECT t.*, g.name as game_name, p.name as product_name 
              FROM transactions t 
              JOIN games g ON t.game_id = g.id 
              JOIN products p ON t.product_id = p.id 
              WHERE t.order_id = ? AND (";
    
    $params = [$order_id];
    
    if (db_has_column($pdo, 'transactions', 'account_user_id')) {
        $query .= " t.account_user_id = ? ";
        $params[] = (int)$user_id;
    } else {
        $query .= " t.user_id = ? ";
        $params[] = $user_email;
    }
    
    $query .= ")";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$transaction) {
        $error = 'Order ID tidak ditemukan atau bukan milik Anda!';
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>
    


    <?php 
    // Only animate on initial page load (GET), not after form submission (POST)
    $animate_class = ($_SERVER['REQUEST_METHOD'] === 'GET') ? 'animate-page-entrance' : ''; 
    ?>
    <section class="check-order-section <?php echo $animate_class; ?>">
        <div class="container">
            <h1 class="page-title">🔍 Cek Status Pesanan</h1>
            <p class="page-subtitle">Masukkan Order ID untuk melihat rincian dan status transaksi Anda secara real-time</p>
            
            <div class="check-order-box">
                <!-- ... existing content ... -->
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

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

