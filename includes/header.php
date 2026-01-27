<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$legal_pages = ['privacy.php', 'about.php', 'contact.php', 'faq.php', 'career.php', 'partnership.php', 'blog.php', 'testimonials.php'];
$is_legal_page = in_array($current_page, $legal_pages);
$navbar_class = "navbar no-transition";
if ($is_legal_page) {
    $navbar_class .= " scrolled";
}
?>
<header>
    <nav class="<?php echo $navbar_class; ?>" <?php echo $is_legal_page ? 'data-is-legal="true"' : ''; ?>>
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <span class="logo-icon">🎮</span>
                    <span class="logo-text">PLAYSHOP<span class="highlight">.ID</span></span>
                </a>
            </div>
            
            <button class="nav-toggle" id="navToggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>

            <ul class="nav-menu" id="navMenu">
                <div class="nav-indicator" id="navIndicator"></div>
                <li><a href="index.php" id="homeLink" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="index.php#games" id="gamesLink">Games</a></li>
                <li><a href="promo.php" class="<?php echo $current_page === 'promo.php' ? 'active' : ''; ?>">Promo</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="check-order.php" class="<?php echo $current_page === 'check-order.php' ? 'active' : ''; ?>">Cek Order</a></li>
                <?php endif; ?>
                <li><a href="privacy.php" class="<?php echo str_contains($current_page, 'privacy') || str_contains($current_page, 'about') || str_contains($current_page, 'contact') || str_contains($current_page, 'faq') || str_contains($current_page, 'career') || str_contains($current_page, 'partnership') || str_contains($current_page, 'blog') || str_contains($current_page, 'testimonials') ? 'active' : ''; ?>">Lainnya</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php" class="<?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">Profil</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="<?php echo $current_page === 'login.php' ? 'active' : ''; ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
<div id="pageWrapper">
