<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';
require_once __DIR__ . '/includes/email.php';
require_once __DIR__ . '/includes/whatsapp.php';

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

// Decrement stock if products.stock is used (NULL = unlimited)
try {
    $stmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = ? AND stock IS NOT NULL AND stock > 0");
    $stmt->execute([(int)$transaction['product_id']]);
} catch (Exception $e) {
    // ignore stock update failures (optional feature)
}

// Voucher usage counter (only if voucher_code exists)
try {
    $voucherCode = strtoupper(trim((string)($transaction['voucher_code'] ?? '')));
    $discountAmount = (int)($transaction['discount_amount'] ?? 0);
    if ($voucherCode !== '' && $discountAmount > 0) {
        $stmt = $pdo->prepare("UPDATE vouchers SET used_count = used_count + 1 WHERE code = ?");
        $stmt->execute([$voucherCode]);
    }
} catch (Exception $e) {
    // ignore voucher counter failures
}

// Dummy notifications (only if user is logged in)
$toEmail = $_SESSION['user_email'] ?? null;
if ($toEmail) {
    email_send_dummy(
        $toEmail,
        "PLAYSHOP.ID - Pembayaran Berhasil ({$order_id})",
        "Pesanan kamu berhasil. Order ID: {$order_id}\nGame: {$transaction['game_name']}\nProduk: {$transaction['product_name']}\nTotal: Rp " . number_format((int)$transaction['amount'], 0, ',', '.'),
        ['order_id' => $order_id, 'type' => 'payment_success']
    );
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>
    


    <section class="success-section">
        <div class="container">
            <h1 class="page-title">✅ Transaksi Berhasil</h1>
            <p class="page-subtitle">Terima kasih telah melakukan top up di PLAYSHOP.ID</p>

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

    <?php include __DIR__ . '/includes/footer.php'; ?>
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

