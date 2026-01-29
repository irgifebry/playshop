<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_POST['order_id'];

// TIDAK auto-update ke success, biarkan tetap pending
// Status akan diupdate oleh admin via admin/transaction-detail.php

// Ambil detail transaksi
$stmt = $pdo->prepare("SELECT t.*, g.name as game_name, p.name as product_name 
                       FROM transactions t 
                       JOIN games g ON t.game_id = g.id 
                       JOIN products p ON t.product_id = p.id 
                       WHERE t.order_id = ?");
$stmt->execute([$order_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    header('Location: index.php');
    exit;
}

// Log pembayaran user (bukan konfirmasi sukses)
try {
    $logMessage = sprintf(
        "[PEMBAYARAN DIKIRIM] Order ID: %s | Game: %s | Produk: %s | Total: Rp %s | User: %s | Menunggu konfirmasi admin",
        $order_id,
        $transaction['game_name'],
        $transaction['product_name'],
        number_format((int)$transaction['amount'], 0, ',', '.'),
        $transaction['game_user_id'] ?? 'Guest'
    );
    $stmt = $pdo->prepare("INSERT INTO notifications_log (message, created_at) VALUES (?, NOW())");
    $stmt->execute([$logMessage]);
} catch (Exception $e) {
    // ignore log failures silently
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Konfirmasi | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .waiting-icon {
            font-size: 5rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .status-pending {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-weight: 700;
            display: inline-block;
        }
    </style>
</head>
<body>
    <?php include "includes/header.php"; ?>

    <section class="success-section">
        <div class="container">
            <h1 class="page-title">⏳ Menunggu Konfirmasi</h1>
            <p class="page-subtitle">Pembayaran Anda sedang diverifikasi oleh admin</p>

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
                    <div class="step-number">⏳</div>
                    <div class="step-label">Verifikasi</div>
                </div>
            </div>

            <div class="success-container">
                <div class="waiting-icon">⏳</div>
                <h1 class="success-title">Pembayaran Diterima!</h1>
                <p class="success-subtitle">Pesanan Anda sedang menunggu konfirmasi dari admin</p>

                <div class="success-details">
                    <h3>Detail Transaksi</h3>
                    <table class="detail-table">
                        <tr>
                            <td>Order ID</td>
                            <td><strong><?php echo htmlspecialchars($transaction['order_id']); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Game</td>
                            <td><?php echo htmlspecialchars($transaction['game_name']); ?></td>
                        </tr>
                        <tr>
                            <td>Produk</td>
                            <td><?php echo htmlspecialchars($transaction['product_name']); ?></td>
                        </tr>
                        <tr>
                            <td>User ID</td>
                            <td><?php echo htmlspecialchars($transaction['game_user_id']); ?><?php echo $transaction['game_zone_id'] ? " (" . htmlspecialchars($transaction['game_zone_id']) . ")" : ''; ?></td>
                        </tr>
                        <tr>
                            <td>Total Bayar</td>
                            <td><strong>Rp <?php echo number_format($transaction['amount'], 0, ',', '.'); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><span class="status-pending">Menunggu Konfirmasi</span></td>
                        </tr>
                    </table>
                </div>

                <div class="success-info" style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 16px; padding: 1.5rem; margin-top: 1.5rem;">
                    <p style="margin: 0; color: #92400e;">⏰ <strong>Estimasi waktu verifikasi:</strong> 1-15 menit</p>
                    <p style="margin: 0.5rem 0 0; color: #92400e; font-size: 0.9rem;">Anda akan menerima notifikasi setelah admin mengkonfirmasi pembayaran.</p>
                </div>

                <div class="success-info" style="margin-top: 1rem;">
                    <p>📋 Simpan <strong>Order ID</strong> ini untuk mengecek status pesanan Anda</p>
                    <p>💬 Hubungi CS jika pesanan tidak diproses dalam 30 menit</p>
                </div>

                <div class="success-actions">
                    <a href="check-order.php" class="btn-primary">Cek Status Pesanan</a>
                    <a href="index.php" class="btn-secondary">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
