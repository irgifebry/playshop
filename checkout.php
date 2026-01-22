<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

if(!isset($_GET['game_id'])) {
    header('Location: index.php');
    exit;
}

$game_id = $_GET['game_id'];
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ? AND is_active = 1");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$game) {
    header('Location: index.php');
    exit;
}

// Ambil produk untuk game ini
$stmt = $pdo->prepare("SELECT * FROM products WHERE game_id = ? AND is_active = 1 ORDER BY price");
$stmt->execute([$game_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo $game['name']; ?> | PLAYSHOP.ID</title>
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
            </div>
        </nav>
    </header>

    <section class="checkout-section">
        <div class="container">
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-label">Pilih Produk</div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-label">Pembayaran</div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">Selesai</div>
                </div>
            </div>

            <div class="checkout-container">
                <!-- Game Info -->
                <div class="checkout-header">
                    <div class="game-banner" style="background: linear-gradient(135deg, <?php echo $game['color_start']; ?>, <?php echo $game['color_end']; ?>);">
                        <?php if (!empty($game['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars(asset_url($game['image_path'])); ?>" alt="<?php echo htmlspecialchars($game['name']); ?>" style="width:64px;height:64px;border-radius:12px;object-fit:cover;background:rgba(255,255,255,0.2);" />
                        <?php else: ?>
                            <span class="banner-icon"><?php echo $game['icon']; ?></span>
                        <?php endif; ?>
                        <div>
                            <h2><?php echo $game['name']; ?></h2>
                            <p>Top Up Diamond/UC</p>
                        </div>
                    </div>
                </div>

                <form id="checkoutForm" method="POST" action="payment.php">
                    <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                    <input type="hidden" name="game_name" value="<?php echo $game['name']; ?>">
                    
                    <!-- Step 1: User ID -->
                    <div class="form-section">
                        <h3>1. Masukkan User ID</h3>
                        <div class="form-row">
                            <input type="text" name="user_id" id="user_id" placeholder="Masukkan User ID" required>
                            <input type="text" name="zone_id" id="zone_id" placeholder="Zone ID (opsional)">
                        </div>
                        <p class="form-hint">💡 User ID bisa ditemukan di profil game kamu</p>
                    </div>

                    <!-- Step 2: Pilih Produk -->
                    <div class="form-section">
                        <h3>2. Pilih Nominal</h3>
                        <div class="products-grid">
                            <?php foreach($products as $product): ?>
                            <label class="product-option">
                                <input type="radio" name="product_id" value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-name="<?php echo $product['name']; ?>" required>
                                <div class="product-card">
                                    <div class="product-name"><?php echo $product['name']; ?></div>
                                    <div class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 3: Pilih Pembayaran -->
                    <div class="form-section">
                        <h3>3. Pilih Metode Pembayaran</h3>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="DANA" required>
                                <div class="payment-card">
                                    <span class="payment-icon">💳</span>
                                    <span class="payment-name">DANA</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="OVO" required>
                                <div class="payment-card">
                                    <span class="payment-icon">💳</span>
                                    <span class="payment-name">OVO</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="GoPay" required>
                                <div class="payment-card">
                                    <span class="payment-icon">💳</span>
                                    <span class="payment-name">GoPay</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="Bank Transfer" required>
                                <div class="payment-card">
                                    <span class="payment-icon">🏦</span>
                                    <span class="payment-name">Bank Transfer</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Step 4: Voucher (Optional) -->
                    <div class="form-section">
                        <h3>4. Kode Promo (Opsional)</h3>
                        <div class="form-row">
                            <input type="text" name="voucher_code" id="voucher_code" placeholder="Contoh: PLAYSHOP20">
                            <input type="text" value="Cek promo di halaman Promo" disabled>
                        </div>
                        <p class="form-hint">💡 Kode promo akan dihitung saat masuk halaman pembayaran (simulasi)</p>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary">
                        <h3>Ringkasan Pesanan</h3>
                        <div class="summary-row">
                            <span>Produk</span>
                            <span id="summary-product">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Harga</span>
                            <span id="summary-price">Rp 0</span>
                        </div>
                        <div class="summary-row">
                            <span>Diskon</span>
                            <span id="summary-discount">Rp 0</span>
                        </div>
                        <div class="summary-row">
                            <span>Biaya Admin</span>
                            <span>Rp 1.000</span>
                        </div>
                        <hr>
                        <div class="summary-row total">
                            <span>Total Pembayaran</span>
                            <span id="summary-total">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-checkout">BAYAR SEKARANG</button>
                </form>
            </div>
        </div>
    </section>

    <script src="js/script.js"></script>
</body>
</html>