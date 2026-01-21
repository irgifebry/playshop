<?php
// admin/sidebar.php
// Reusable sidebar for admin pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional guard: pages already guard, but this prevents accidental include without auth
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$current = basename($_SERVER['PHP_SELF'] ?? '');
function nav_active(string $current, string $file): string {
    return $current === $file ? ' active' : '';
}
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <span class="logo-icon">🎮</span>
        <h3>Admin Panel</h3>
        <p style="margin-top: 6px; font-size: 0.85rem; color: #9ca3af;">
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?>
        </p>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item<?php echo nav_active($current, 'dashboard.php'); ?>">📊 Dashboard</a>
        <a href="games.php" class="nav-item<?php echo nav_active($current, 'games.php'); ?>">🎮 Kelola Game</a>
        <a href="products.php" class="nav-item<?php echo nav_active($current, 'products.php'); ?>">🧾 Kelola Produk</a>
        <a href="discounts.php" class="nav-item<?php echo nav_active($current, 'discounts.php'); ?>">🎫 Voucher/Promo</a>
        <a href="banners.php" class="nav-item<?php echo nav_active($current, 'banners.php'); ?>">🖼️ Banner/Slider</a>
        <a href="reports.php" class="nav-item<?php echo nav_active($current, 'reports.php'); ?>">📈 Laporan</a>
        <a href="users.php" class="nav-item<?php echo nav_active($current, 'users.php'); ?>">👥 Users</a>
        <a href="settings.php" class="nav-item<?php echo nav_active($current, 'settings.php'); ?>">⚙️ Pengaturan</a>
        <a href="logout.php" class="nav-item">🚪 Logout</a>
    </nav>
</aside>