<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';
require_once __DIR__ . '/includes/voucher.php';
require_once __DIR__ . '/includes/email.php';
require_once __DIR__ . '/includes/whatsapp.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ensure user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$game_id = $_POST['game_id'];
$game_name = $_POST['game_name'];
$game_user_id = $_POST['user_id'];      // user id in game
$game_zone_id = $_POST['zone_id'] ?? ''; // zone id in game (optional)
$product_id = $_POST['product_id'];
$payment_method = $_POST['payment_method'];
$voucher_code = $_POST['voucher_code'] ?? '';

$account_user_id = $_SESSION['user_id'] ?? null;
$account_email = $_SESSION['user_email'] ?? null;

// Ambil info produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$admin_fee = 1000;
$subtotal = (int)$product['price'];

// Apply voucher (dummy engine)
$voucher = voucher_apply($pdo, $voucher_code, $subtotal);
$discount = (int)($voucher['discount'] ?? 0);
$total = max(0, $subtotal + $admin_fee - $discount);

// Simpan transaksi ke database
$order_id = 'TRX' . time() . rand(1000, 9999);

// Prefer new schema columns if available; fallback to legacy columns otherwise
if (db_has_column($pdo, 'transactions', 'game_user_id')) {
    $stmt = $pdo->prepare("
        INSERT INTO transactions
            (order_id, game_id, product_id, user_id, zone_id, account_user_id, account_email, game_user_id, game_zone_id, payment_method, subtotal, admin_fee, discount_amount, voucher_code, amount, status, created_at)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([
        $order_id,
        $game_id,
        $product_id,
        $game_user_id,
        $game_zone_id !== '' ? $game_zone_id : null,
        $account_user_id,
        $account_email,
        $game_user_id,
        $game_zone_id !== '' ? $game_zone_id : null,
        $payment_method,
        $subtotal,
        $admin_fee,
        $discount,
        strtoupper(trim($voucher_code)),
        $total
    ]);
} else {
    // Legacy: store game user id in user_id and zone in zone_id
    $stmt = $pdo->prepare("INSERT INTO transactions (order_id, game_id, product_id, user_id, zone_id, payment_method, amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$order_id, $game_id, $product_id, $game_user_id, $game_zone_id, $payment_method, $total]);
}

$_SESSION['order_id'] = $order_id;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>


    <section class="payment-section">
        <div class="container">
            <h1 class="page-title">🔐 Selesaikan Pembayaran Anda</h1>
            <p class="page-subtitle">Silakan selesaikan pembayaran sesuai metode yang Anda pilih</p>

            <div class="progress-steps">
                <div class="step completed">
                    <div class="step-number">✓</div>
                    <div class="step-label">Pilih Produk</div>
                </div>
                <div class="step-line"></div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-label">Pembayaran</div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">Selesai</div>
                </div>
            </div>

            <div class="payment-container">
                <div class="payment-info">
                    
                    <div class="payment-details">
                        <h3>Detail Pesanan</h3>
                        <div class="table-responsive">
                            <table class="detail-table">
                                <tr>
                                    <td>Order ID</td>
                                    <td><strong><?php echo $order_id; ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Game</td>
                                    <td><?php echo $game_name; ?></td>
                                </tr>
                                <tr>
                                    <td>Produk</td>
                                    <td><?php echo $product['name']; ?></td>
                                </tr>
                                <tr>
                                    <td>User ID</td>
                                    <td><?php echo $game_user_id; ?><?php echo $game_zone_id ? " ($game_zone_id)" : ''; ?></td>
                                </tr>
                                <tr>
                                    <td>Metode Pembayaran</td>
                                    <td><?php echo $payment_method; ?></td>
                                </tr>
                                <tr>
                                    <td>Subtotal</td>
                                    <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td>Diskon</td>
                                    <td>
                                        <?php if($discount > 0): ?>
                                            <strong>- Rp <?php echo number_format($discount, 0, ',', '.'); ?></strong>
                                            <?php if(!empty($voucher['code'])): ?>
                                                <div style="font-size: 0.9rem; color: #6b7280;">Kode: <?php echo htmlspecialchars($voucher['code']); ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Rp 0
                                            <?php if(!empty($voucher_code) && !($voucher['ok'] ?? false)): ?>
                                                <div style="font-size: 0.9rem; color: #991b1b;"><?php echo htmlspecialchars($voucher['message'] ?? 'Voucher tidak valid'); ?></div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Biaya Admin</td>
                                    <td>Rp <?php echo number_format($admin_fee, 0, ',', '.'); ?></td>
                                </tr>
                                <tr class="total-row">
                                    <td>Total Bayar</td>
                                    <td><strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="payment-simulation">
                        <h3>Simulasi Pembayaran <?php echo $payment_method; ?></h3>
                        <div class="qr-dummy">
                            <div class="qr-box">📱</div>
                            <p>Scan QR Code Dummy</p>
                        </div>
                        
                        <div class="timer">
                            <p>Waktu tersisa: <span id="countdown">15:00</span></p>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressBar"></div>
                            </div>
                        </div>

                        <form method="POST" action="success.php">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <button type="submit" class="btn-confirm">SUDAH BAYAR</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Countdown timer
        let timeLeft = 900; // 15 menit
        const countdown = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('countdown').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            const progress = (timeLeft / 900) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
            
            if(timeLeft <= 0) {
                clearInterval(countdown);
                alert('Waktu pembayaran habis!');
            }
        }, 1000);
    </script>
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

