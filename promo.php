<?php
session_start();
require_once 'config/database.php';

// Get active promos from database
$stmt = $pdo->query("SELECT * FROM vouchers WHERE status = 'active' AND expired_date >= CURDATE() ORDER BY created_at DESC");
$promos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo & Diskon | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>
    


    <section class="promo-section">
        <div class="container">
            <h1 class="page-title">🎉 Promo & Diskon Spesial</h1>
            <p class="page-subtitle">Dapatkan harga terbaik untuk top up game favoritmu!</p>

            <!-- Featured Promo Banner -->
            <div class="featured-promo">
                <div class="promo-banner">
                    <div class="banner-content">
                        <span class="promo-badge">HOT DEAL</span>
                        <h2>MEGA SALE 50% OFF!</h2>
                        <p>Top up Mobile Legends & Free Fire</p>
                        <p class="promo-period">Periode: 1 Januari 2025 - 31 Desember 2026</p>
                        <a href="index.php" class="btn-primary">Top Up Sekarang</a>
                    </div>
                    <div class="banner-graphic">🎮💎</div>
                </div>
            </div>

            <!-- Voucher/Promo Codes -->
            <div class="vouchers-section">
                <h2>Kode Promo Aktif</h2>
                <p>Salin kode dan gunakan saat checkout</p>

                <div class="vouchers-grid">
                    <?php if(count($promos) > 0): ?>
                        <?php foreach($promos as $promo): ?>
                        <div class="voucher-card">
                            <div class="voucher-header">
                                <span class="voucher-icon">🎫</span>
                                <span class="voucher-type"><?php echo $promo['type'] === 'percentage' ? 'DISKON' : 'POTONGAN'; ?></span>
                            </div>
                            <div class="voucher-body">
                                <h3 class="voucher-value">
                                    <?php 
                                    if($promo['type'] === 'percentage') {
                                        echo $promo['amount'] . '%';
                                    } else {
                                        echo 'Rp ' . number_format($promo['amount'], 0, ',', '.');
                                    }
                                    ?>
                                </h3>
                                <p class="voucher-desc"><?php echo $promo['description']; ?></p>
                                <div class="voucher-code-box">
                                    <code class="voucher-code"><?php echo $promo['code']; ?></code>
                                    <button onclick="copyCode('<?php echo $promo['code']; ?>')" class="btn-copy">Salin</button>
                                </div>
                                <p class="voucher-expiry">Berlaku sampai: <?php echo date('d M Y', strtotime($promo['expired_date'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Default Dummy Promos -->
                        <div class="voucher-card">
                            <div class="voucher-header">
                                <span class="voucher-icon">🎫</span>
                                <span class="voucher-type">DISKON</span>
                            </div>
                            <div class="voucher-body">
                                <h3 class="voucher-value">20%</h3>
                                <p class="voucher-desc">Diskon untuk semua game</p>
                                <div class="voucher-code-box">
                                    <code class="voucher-code">PLAYSHOP20</code>
                                    <button onclick="copyCode('PLAYSHOP20')" class="btn-copy">Salin</button>
                                </div>
                                <p class="voucher-expiry">Berlaku sampai: 31 Jan 2025</p>
                            </div>
                        </div>

                        <div class="voucher-card">
                            <div class="voucher-header">
                                <span class="voucher-icon">🎫</span>
                                <span class="voucher-type">POTONGAN</span>
                            </div>
                            <div class="voucher-body">
                                <h3 class="voucher-value">Rp 10.000</h3>
                                <p class="voucher-desc">Untuk transaksi minimal Rp 50.000</p>
                                <div class="voucher-code-box">
                                    <code class="voucher-code">NEWUSER10K</code>
                                    <button onclick="copyCode('NEWUSER10K')" class="btn-copy">Salin</button>
                                </div>
                                <p class="voucher-expiry">Berlaku sampai: 31 Jan 2025</p>
                            </div>
                        </div>

                        <div class="voucher-card">
                            <div class="voucher-header">
                                <span class="voucher-icon">🎫</span>
                                <span class="voucher-type">DISKON</span>
                            </div>
                            <div class="voucher-body">
                                <h3 class="voucher-value">15%</h3>
                                <p class="voucher-desc">Khusus Mobile Legends</p>
                                <div class="voucher-code-box">
                                    <code class="voucher-code">ML15OFF</code>
                                    <button onclick="copyCode('ML15OFF')" class="btn-copy">Salin</button>
                                </div>
                                <p class="voucher-expiry">Berlaku sampai: 28 Jan 2025</p>
                            </div>
                        </div>

                        <div class="voucher-card">
                            <div class="voucher-header">
                                <span class="voucher-icon">🎫</span>
                                <span class="voucher-type">POTONGAN</span>
                            </div>
                            <div class="voucher-body">
                                <h3 class="voucher-value">Rp 5.000</h3>
                                <p class="voucher-desc">Gratis ongkir untuk semua transaksi</p>
                                <div class="voucher-code-box">
                                    <code class="voucher-code">GRATIS5K</code>
                                    <button onclick="copyCode('GRATIS5K')" class="btn-copy">Salin</button>
                                </div>
                                <p class="voucher-expiry">Berlaku sampai: 31 Jan 2025</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Special Offers -->
            <div class="special-offers">
                <h2>Penawaran Spesial Lainnya</h2>
                
                <div class="offers-grid">
                    <div class="offer-card">
                        <div class="offer-icon">🎁</div>
                        <h3>Cashback 10%</h3>
                        <p>Dapatkan cashback hingga Rp 50.000 untuk transaksi pertama!</p>
                    </div>

                    <div class="offer-card">
                        <div class="offer-icon">💳</div>
                        <h3>Promo E-Wallet</h3>
                        <p>Diskon 15% untuk pembayaran via DANA, OVO, atau GoPay</p>
                    </div>

                    <div class="offer-card">
                        <div class="offer-icon">🏆</div>
                        <h3>Loyalty Rewards</h3>
                        <p>Kumpulkan poin setiap transaksi dan tukar dengan diskon!</p>
                    </div>

                    <div class="offer-card">
                        <div class="offer-icon">👥</div>
                        <h3>Refer a Friend</h3>
                        <p>Ajak teman dan dapatkan bonus Rp 20.000 untuk kalian berdua!</p>
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="promo-terms">
                <h3>Syarat & Ketentuan</h3>
                <ul>
                    <li>Promo berlaku sesuai periode yang ditentukan</li>
                    <li>Satu kode promo hanya dapat digunakan satu kali per user</li>
                    <li>Kode promo tidak dapat digabungkan dengan promo lain</li>
                    <li>Kami berhak mengubah atau membatalkan promo tanpa pemberitahuan</li>
                    <li>Promo hanya berlaku untuk transaksi yang berhasil</li>
                </ul>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Kode promo "' + code + '" berhasil disalin!');
            });
        }
    </script>
</body>
</html>

