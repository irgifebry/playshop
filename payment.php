<?php
session_start();
require_once 'config/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$game_id = $_POST['game_id'];
$game_name = $_POST['game_name'];
$user_id = $_POST['user_id'];
$zone_id = $_POST['zone_id'] ?? '';
$product_id = $_POST['product_id'];
$payment_method = $_POST['payment_method'];

// Ambil info produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$admin_fee = 1000;
$total = $product['price'] + $admin_fee;

// Simpan transaksi ke database
$order_id = 'TRX' . time() . rand(1000, 9999);
$stmt = $pdo->prepare("INSERT INTO transactions (order_id, game_id, product_id, user_id, zone_id, payment_method, amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
$stmt->execute([$order_id, $game_id, $product_id, $user_id, $zone_id, $payment_method, $total]);

$_SESSION['order_id'] = $order_id;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran | PLAYSHOP.ID</title>
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

    <section class="payment-section">
        <div class="container">
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
                    <h2>🔐 Halaman Pembayaran Dummy</h2>
                    <p class="payment-note">Ini adalah simulasi pembayaran. Tidak ada transaksi nyata yang dilakukan.</p>
                    
                    <div class="payment-details">
                        <h3>Detail Pesanan</h3>
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
                                <td><?php echo $user_id; ?><?php echo $zone_id ? " ($zone_id)" : ''; ?></td>
                            </tr>
                            <tr>
                                <td>Metode Pembayaran</td>
                                <td><?php echo $payment_method; ?></td>
                            </tr>
                            <tr class="total-row">
                                <td>Total Bayar</td>
                                <td><strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></td>
                            </tr>
                        </table>
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
                            <button type="submit" class="btn-confirm">SIMULASI PEMBAYARAN BERHASIL</button>
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
</body>
</html>