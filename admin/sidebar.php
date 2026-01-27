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

<aside class="sidebar" id="adminSidebar">
    <!-- Mobile Toggle Button -->
    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
        ☰ Menu Admin
    </button>
    
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
        <a href="payment-methods.php" class="nav-item<?php echo nav_active($current, 'payment-methods.php'); ?>">💳 Metode Pembayaran</a>
        <a href="providers.php" class="nav-item<?php echo nav_active($current, 'providers.php'); ?>">🔌 API Provider</a>
        <a href="posts.php" class="nav-item<?php echo nav_active($current, 'posts.php'); ?>">📝 Post/Blog</a>
        <a href="testimonials.php" class="nav-item<?php echo nav_active($current, 'testimonials.php'); ?>">💬 Testimoni</a>
        <a href="reports.php" class="nav-item<?php echo nav_active($current, 'reports.php'); ?>">📈 Laporan</a>
        <a href="users.php" class="nav-item<?php echo nav_active($current, 'users.php'); ?>">👥 Users</a>
        <a href="settings.php" class="nav-item<?php echo nav_active($current, 'settings.php'); ?>">⚙️ Pengaturan</a>
        <a href="logout.php" class="nav-item">🚪 Logout</a>
    </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== SIDEBAR SCROLL PERSISTENCE ==========
    const sidebar = document.getElementById('adminSidebar');
    if (sidebar) {
        // 1. Restore scroll position
        const savedPos = sessionStorage.getItem('adminSidebarScroll');
        if (savedPos) {
            sidebar.scrollTop = parseInt(savedPos);
        }

        // 2. Save scroll position when clicking menu items
        const navItems = sidebar.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // If clicking an already active menu item, scroll ONLY the main content to top
                if (this.classList.contains('active')) {
                    const mainContent = document.querySelector('.main-content');
                    if (mainContent) {
                        e.preventDefault();
                        mainContent.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                    return;
                }
                sessionStorage.setItem('adminSidebarScroll', sidebar.scrollTop);
            });
        });
    }

    // ========== BOUNCING SCROLL EFFECT (Strictly Isolated & Dynamic) ==========
    function initBouncyScroll(el) {
        if (!el || el.dataset.bounceInit === "true") return;
        
        const style = window.getComputedStyle(el);
        const isScrollable = (style.overflowY === 'auto' || style.overflowY === 'scroll') && (el.scrollHeight > el.clientHeight);
        
        if (!isScrollable) return;
        el.dataset.bounceInit = "true";
        
        let delta = 0;
        let bounceTimer = null;
        el.style.willChange = 'transform';

        el.addEventListener('wheel', (e) => {
            const atTop = el.scrollTop <= 10;
            const atBottom = el.scrollHeight - el.scrollTop <= el.clientHeight + 15;

            if ((atTop && e.deltaY < 0) || (atBottom && e.deltaY > 0)) {
                // Hanya cegah default jika elemen ini memang bisa membal
                e.preventDefault();
                e.stopPropagation();
                
                delta -= e.deltaY * 0.4;
                delta = Math.max(-120, Math.min(120, delta));

                el.style.transition = 'none';
                el.style.transform = `translate3d(0, ${delta}px, 0)`;

                clearTimeout(bounceTimer);
                bounceTimer = setTimeout(() => {
                    el.style.transition = 'transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    el.style.transform = '';
                    delta = 0;
                }, 40);
            }
        }, { passive: false });

        // Touch Support
        let startY = 0;
        el.addEventListener('touchstart', (e) => {
            if (e.touches.length > 0) startY = e.touches[0].pageY;
        }, { passive: true });

        el.addEventListener('touchmove', (e) => {
            if (e.touches.length === 0) return;
            const currentY = e.touches[0].pageY;
            const diff = currentY - startY;
            const atTop = el.scrollTop <= 10;
            const atBottom = el.scrollHeight - el.scrollTop <= el.clientHeight + 15;

            if ((atTop && diff > 0) || (atBottom && diff < 0)) {
                delta = Math.sign(diff) * Math.pow(Math.abs(diff), 0.7) * 2;
                el.style.transition = 'none';
                el.style.transform = `translate3d(0, ${delta}px, 0)`;
            }
        }, { passive: true });

        el.addEventListener('touchend', () => {
            el.style.transition = 'transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            el.style.transform = '';
            delta = 0;
        });
    }

    function applyAdminBounces() {
        // Fokuskan hanya pada kontainer utama yang memang seharusnya menampung scroll
        const contentSelectors = ['.main-content', 'main'];
        contentSelectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(el => initBouncyScroll(el));
        });
    }

    applyAdminBounces();
    [200, 600, 1200, 2000].forEach(delay => setTimeout(applyAdminBounces, delay));

    // Expose function globally so onclick attribute works
    window.toggleSidebar = function() {
        if (sidebar) {
            const isOpen = sidebar.classList.toggle('open');
            
            // Interaction: scroll to top on open and toggle lock
            if (isOpen) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        } else {
            console.error('Sidebar element not found');
        }
    };
});
</script>