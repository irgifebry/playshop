<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

$banner_id = (int)($_GET['banner_id'] ?? 0);

if ($banner_id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, title, description, image_path, link_url, is_active
        FROM banners
        WHERE id = ? AND is_active = 1
    ");
    $stmt->execute([$banner_id]);
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$banner) {
        header('Location: index.php');
        exit;
    }
} catch (Exception $e) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($banner['title']); ?> | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <span class="logo-icon">🎮</span>
                    <span class="logo-text">PLAYSHOP<span class="highlight">.ID</span></span>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#games">Games</a></li>
                    <li><a href="promo.php" class="active">Promo</a></li>
                    <li><a href="check-order.php">Cek Order</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php">Kontak</a></li>
                    <li><a href="about.php">Tentang</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="profile.php">Profil</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                    <li><a href="admin/login.php">Admin</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Promo Detail Section -->
    <section class="promo-detail-section">
        <div class="container">
            <a href="index.php" class="back-link">&larr; Kembali ke Beranda</a>

            <div class="promo-detail-content">
                <div class="promo-detail-image">
                    <img src="<?php echo htmlspecialchars(asset_url($banner['image_path'])); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" />
                </div>

                <div class="promo-detail-info">
                    <h1 class="promo-title"><?php echo htmlspecialchars($banner['title']); ?></h1>
                    
                    <?php if (!empty($banner['description'])): ?>
                        <div class="promo-description">
                            <?php echo nl2br(htmlspecialchars($banner['description'])); ?>
                        </div>
                    <?php endif; ?>

                    <div class="promo-actions">
                        <a href="index.php#games" class="btn-primary">Top Up Sekarang</a>
                        <a href="index.php" class="btn-secondary">Lihat Game Lain</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 PLAYSHOP.ID - Transaksi Cepat & Aman</p>
            <p>Platform Top Up Game Terpercaya di Indonesia</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
