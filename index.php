<?php
session_start();
require_once 'config/database.php';

// Ambil banner aktif (opsional)
$banners = [];
try {
    $stmt = $pdo->query("
        SELECT *
        FROM banners
        WHERE is_active = 1
          AND (start_date IS NULL OR start_date <= CURDATE())
          AND (end_date IS NULL OR end_date >= CURDATE())
        ORDER BY sort_order ASC, created_at DESC
    ");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $banners = [];
}

// Ambil data game dari database
$stmt = $pdo->query("SELECT * FROM games WHERE is_active = 1 ORDER BY name");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLAYSHOP.ID - Top Up Game Cepat & Murah</title>
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="#games">Games</a></li>
                    <li><a href="promo.php">Promo</a></li>
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

    <?php if (count($banners) > 0): ?>
    <section class="banner-slider">
        <div class="container">
            <div class="banner-track">
                <?php foreach($banners as $b): ?>
                    <a class="banner-item" href="<?php echo htmlspecialchars($b['link_url'] ?: '#'); ?>" style="text-decoration:none;" <?php echo ($b['link_url'] ?? '') ? '' : 'onclick="return false;"'; ?>>
                        <img src="<?php echo htmlspecialchars($b['image_path']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>" />
                        <div class="banner-caption">
                            <span class="banner-title"><?php echo htmlspecialchars($b['title']); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Top Up Game <span class="highlight">Super Cepat!</span></h1>
                <p class="hero-subtitle">Proses otomatis dalam hitungan detik. Aman, Cepat, Terpercaya!</p>
                <div class="hero-features">
                    <div class="feature-item">
                        <span class="feature-icon">⚡</span>
                        <span>Proses Instan</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🔒</span>
                        <span>100% Aman</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">💰</span>
                        <span>Harga Murah</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Games Section -->
    <section id="games" class="games-section">
        <div class="container">
            <h2 class="section-title">Pilih Game Favoritmu</h2>
            <p class="section-subtitle">Top up mudah untuk game-game populer</p>
            
            <div class="games-grid">
                <?php foreach($games as $game): ?>
                <div class="game-card" onclick="selectGame(<?php echo $game['id']; ?>, '<?php echo $game['name']; ?>')">
                    <div class="game-image" style="background: linear-gradient(135deg, <?php echo $game['color_start']; ?>, <?php echo $game['color_end']; ?>);">
                        <span class="game-icon"><?php echo $game['icon']; ?></span>
                    </div>
                    <div class="game-info">
                        <h3 class="game-name"><?php echo $game['name']; ?></h3>
                        <p class="game-price">Mulai dari Rp <?php echo number_format($game['min_price'], 0, ',', '.'); ?></p>
                        <div class="game-actions">
                            <button class="btn-topup">Top Up Sekarang</button>
                            <a class="btn-secondary btn-small" href="game-detail.php?game_id=<?php echo (int)$game['id']; ?>" onclick="event.stopPropagation();">Detail</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
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