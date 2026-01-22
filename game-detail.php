<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

if (!isset($_GET['game_id'])) {
    header('Location: index.php');
    exit;
}

$game_id = (int)$_GET['game_id'];

$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ? LIMIT 1");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE game_id = ? AND is_active = 1 ORDER BY price");
$stmt->execute([$game_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function nl2p(string $text): string {
    $lines = preg_split('/\r\n|\r|\n/', trim($text));
    $out = '';
    foreach ($lines as $line) {
        if (trim($line) === '') continue;
        $out .= '<p>' . htmlspecialchars($line) . '</p>';
    }
    return $out;
}

$bannerStyle = "background: linear-gradient(135deg, {$game['color_start']}, {$game['color_end']});";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game['name']); ?> | Detail Game - PLAYSHOP.ID</title>
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
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="checkout-section">
        <div class="container">
            <div class="checkout-container">
                <div class="checkout-header">
                    <div class="game-banner" style="<?php echo $bannerStyle; ?>">
                        <?php if (!empty($game['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars(asset_url($game['image_path'])); ?>" alt="<?php echo htmlspecialchars($game['name']); ?>" style="width:64px;height:64px;border-radius:12px;object-fit:cover;background:rgba(255,255,255,0.2);" />
                        <?php else: ?>
                            <span class="banner-icon"><?php echo $game['icon']; ?></span>
                        <?php endif; ?>
                        <div>
                            <h2><?php echo htmlspecialchars($game['name']); ?></h2>
                            <p>Info lengkap, cara top up, dan FAQ</p>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Deskripsi</h3>
                    <?php if (!empty($game['description'])): ?>
                        <?php echo nl2p($game['description']); ?>
                    <?php else: ?>
                        <p>Belum ada deskripsi untuk game ini.</p>
                    <?php endif; ?>
                </div>

                <div class="form-section">
                    <h3>Nominal Populer</h3>
                    <?php if (count($products) > 0): ?>
                        <div class="products-grid">
                            <?php foreach($products as $p): ?>
                                <div class="product-card" style="cursor: default;">
                                    <div class="product-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                    <div class="product-price">Rp <?php echo number_format((int)$p['price'], 0, ',', '.'); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Produk belum tersedia untuk game ini.</p>
                    <?php endif; ?>

                    <div style="margin-top: 16px;">
                        <a class="btn-primary" href="checkout.php?game_id=<?php echo (int)$game['id']; ?>">Top Up Sekarang</a>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Cara Top Up</h3>
                    <?php if (!empty($game['how_to_topup'])): ?>
                        <?php echo nl2p($game['how_to_topup']); ?>
                    <?php else: ?>
                        <ol>
                            <li>Pilih produk</li>
                            <li>Masukkan User ID/Zone ID</li>
                            <li>Pilih pembayaran</li>
                            <li>Selesaikan pembayaran</li>
                        </ol>
                    <?php endif; ?>
                </div>

                <div class="form-section">
                    <h3>FAQ Game</h3>
                    <?php if (!empty($game['faq'])): ?>
                        <?php echo nl2p($game['faq']); ?>
                    <?php else: ?>
                        <p>FAQ khusus game ini belum tersedia.</p>
                    <?php endif; ?>
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
