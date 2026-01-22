<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

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
            <div class="carousel-container">
                <div class="banner-track" id="bannerTrack">
                    <?php foreach($banners as $index => $b): ?>
                        <a class="banner-item <?php echo $index === 0 ? 'active' : ''; ?>" href="promo-detail.php?banner_id=<?php echo (int)$b['id']; ?>" style="text-decoration:none;">
                            <img src="<?php echo htmlspecialchars(asset_url($b['image_path'])); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>" />

                            <div class="banner-caption">
                                <span class="banner-title"><?php echo htmlspecialchars($b['title']); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if (count($banners) > 1): ?>
                <!-- Navigation buttons -->
                <button class="carousel-btn carousel-btn-prev" id="prevBtn" aria-label="Previous banner">❮</button>
                <button class="carousel-btn carousel-btn-next" id="nextBtn" aria-label="Next banner">❯</button>

                <!-- Indicators -->
                <div class="carousel-indicators" id="indicators">
                    <?php for($i = 0; $i < count($banners); $i++): ?>
                        <button class="indicator <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>" aria-label="Go to slide <?php echo $i + 1; ?>"></button>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
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
            
            <!-- Searchbar -->
            <div class="search-container">
                <input type="text" id="searchInput" class="search-input" placeholder="Cari game...">
                <span class="search-icon">🔍</span>
            </div>

            <!-- Category Filter -->
            <div class="category-filter">
                <button class="category-btn active" onclick="filterByCategory('all', this)">Semua</button>
                <button class="category-btn" onclick="filterByCategory('RPG', this)">RPG</button>
                <button class="category-btn" onclick="filterByCategory('MOBA', this)">MOBA</button>
                <button class="category-btn" onclick="filterByCategory('PC', this)">PC</button>
                <button class="category-btn" onclick="filterByCategory('Action', this)">Action</button>
                <button class="category-btn" onclick="filterByCategory('Sports', this)">Sports</button>
                <button class="category-btn" onclick="filterByCategory('Strategy', this)">Strategy</button>
            </div>
            
            <div class="games-grid" id="gamesGrid">
                <?php foreach($games as $game): ?>
                <div class="game-card" data-game-id="<?php echo $game['id']; ?>" data-category="<?php echo htmlspecialchars($game['category'] ?? 'Other'); ?>" data-name="<?php echo htmlspecialchars(strtolower($game['name'])); ?>" onclick="selectGame(<?php echo $game['id']; ?>, '<?php echo $game['name']; ?>')">
                    <div class="game-image" style="background: linear-gradient(135deg, <?php echo $game['color_start']; ?>, <?php echo $game['color_end']; ?>);">
                        <?php if (!empty($game['image_path'])): ?>
                            <img class="game-thumb" src="<?php echo htmlspecialchars(asset_url($game['image_path'])); ?>" alt="<?php echo htmlspecialchars($game['name']); ?>" />
                        <?php else: ?>
                            <span class="game-icon"><?php echo $game['icon']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="game-info">
                        <h3 class="game-name"><?php echo $game['name']; ?></h3>
                        <p class="game-category"><?php echo htmlspecialchars($game['category'] ?? 'Other'); ?></p>
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
    <script>
        // Carousel functionality
        let currentSlide = 0;
        let slideInterval;
        const bannerCount = <?php echo count($banners); ?>;

        function initCarousel() {
            if (bannerCount <= 1) return;

            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const indicators = document.querySelectorAll('.indicator');

            if (prevBtn) prevBtn.addEventListener('click', prevSlide);
            if (nextBtn) nextBtn.addEventListener('click', nextSlide);

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => goToSlide(index));
            });

            startAutoPlay();
        }

        function updateCarousel() {
            const bannerTrack = document.getElementById('bannerTrack');
            const indicators = document.querySelectorAll('.indicator');
            const bannerItems = document.querySelectorAll('.banner-item');

            if (!bannerTrack) return;

            // Update transform
            bannerTrack.style.transform = `translateX(-${currentSlide * 100}%)`;

            // Update active classes
            bannerItems.forEach((item, index) => {
                item.classList.toggle('active', index === currentSlide);
            });

            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentSlide);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % bannerCount;
            updateCarousel();
            resetAutoPlay();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + bannerCount) % bannerCount;
            updateCarousel();
            resetAutoPlay();
        }

        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            updateCarousel();
            resetAutoPlay();
        }

        function startAutoPlay() {
            slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }

        function resetAutoPlay() {
            clearInterval(slideInterval);
            startAutoPlay();
        }

        // Initialize carousel when DOM is loaded
        document.addEventListener('DOMContentLoaded', initCarousel);

        // Game filtering functionality
        let currentCategory = 'all';

        function filterByCategory(category, btn) {
            currentCategory = category;

            // Update button styles
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            filterGames();
        }

        function filterGames() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const gameCards = document.querySelectorAll('.game-card');
            let visibleCount = 0;

            gameCards.forEach(card => {
                const gameName = card.dataset.name;
                const gameCategory = card.dataset.category;

                const matchesSearch = gameName.includes(searchTerm);
                const matchesCategory = currentCategory === 'all' || gameCategory === currentCategory;

                if (matchesSearch && matchesCategory) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show "no results" message if needed
            let noResultsMsg = document.getElementById('noResultsMsg');
            if (visibleCount === 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMsg';
                    noResultsMsg.style.cssText = 'text-align: center; padding: 40px 20px; color: #6b7280; font-size: 1.1rem;';
                    noResultsMsg.textContent = 'Game tidak ditemukan';
                    document.getElementById('gamesGrid').parentNode.appendChild(noResultsMsg);
                }
                noResultsMsg.style.display = 'block';
            } else if (noResultsMsg) {
                noResultsMsg.style.display = 'none';
            }
        }

        // Setup search input listener
        document.getElementById('searchInput').addEventListener('input', filterGames);
    </script>
</body>
</html>