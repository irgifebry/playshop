<?php
// includes/footer.php
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand Column -->
            <div class="footer-brand">
                <div class="logo">
                    <span class="logo-icon">🎮</span>
                    <span class="logo-text">PLAYSHOP<span class="highlight">.ID</span></span>
                </div>
                <p>Platform Top Up Game terpercaya di Indonesia. Proses otomatis, harga kompetitif, dan layanan pelanggan 24/7 untuk pengalaman gaming terbaik Anda.</p>
            </div>

            <!-- Company Column -->
            <div class="footer-col">
                <h4 class="footer-heading">Perusahaan</h4>
                <ul class="footer-links">
                    <li><a href="about.php">Tentang Kami</a></li>
                    <li><a href="career.php">Karier</a></li>
                    <li><a href="partnership.php">Partnership</a></li>
                    <li><a href="blog.php">Blog & Berita</a></li>
                </ul>
            </div>

            <!-- Support Column -->
            <div class="footer-col">
                <h4 class="footer-heading">Dukungan</h4>
                <ul class="footer-links">
                    <li><a href="faq.php">Pusat Bantuan / FAQ</a></li>
                    <li><a href="contact.php">Hubungi Kami</a></li>
                    <li><a href="check-order.php">Cek Status Pesanan</a></li>
                    <li><a href="promo.php">Promo & Voucher</a></li>
                    <li><a href="testimonials.php">Testimoni</a></li>
                </ul>
            </div>

            <!-- Account Column -->
            <div class="footer-col">
                <h4 class="footer-heading">Akun Anda</h4>
                <ul class="footer-links">
                    <li><a href="profile.php">Profil Saya</a></li>
                    <li><a href="history.php">Riwayat Transaksi</a></li>
                    <li><a href="login.php">Masuk / Daftar</a></li>
                </ul>
            </div>

            <!-- Legal Column -->
            <div class="footer-col">
                <h4 class="footer-heading">Legal</h4>
                <ul class="footer-links">
                    <li><a href="privacy.php">Kebijakan Privasi</a></li>
                    <li><a href="privacy.php#terms">Syarat & Ketentuan</a></li>
                    <li><a href="privacy.php#refund">Kebijakan Refund</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> PLAYSHOP<a href="admin/login.php" style="text-decoration: none; color: inherit; cursor: default;">.</a>ID - Hak Cipta Dilindungi. Dibuat untuk gamers Indonesia.</p>
        </div>
    </div>
</footer>
</div> <!-- End pageWrapper -->


<!-- Back to Top Button -->
<a href="#" class="back-to-top" id="backToTop">
    <span>↑</span>
</a>

<!-- Global Scripts -->
<script>
(function() {
    // ========== NAVBAR TOGGLE ==========
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navbar = document.querySelector('.navbar');
    const backToTop = document.getElementById('backToTop');
    const navLinks = document.querySelectorAll('.nav-menu a');
    const isHomePage = window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/') || window.location.pathname === '';
    let isManualScrolling = false;
    let scrollTimeout = null;
    
    if(navToggle && navMenu) {
        console.log('Navbar elements found!'); // DEBUG
        
        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Toggle clicked!'); // DEBUG
            
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
            
            console.log('Menu classes:', navMenu.className); // DEBUG
            
            // FORCE STYLE via JS to bypass CSS issues
            if(navMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
                navMenu.style.maxHeight = '500px';
                navMenu.style.opacity = '1';
                navMenu.style.visibility = 'visible';
                navMenu.style.display = 'flex';
                navMenu.style.flexDirection = 'column';
                navMenu.style.background = 'rgba(17, 24, 39, 0.95)';
                navMenu.style.backdropFilter = 'blur(10px)';
                navMenu.style.position = 'absolute';
                navMenu.style.top = 'calc(100% + 15px)';
                navMenu.style.left = '5%';
                navMenu.style.width = '90%';
                navMenu.style.borderRadius = '20px';
                navMenu.style.zIndex = '2147483647';
            } else {
                document.body.style.overflow = '';
                navMenu.style.maxHeight = '0';
                navMenu.style.opacity = '0';
                navMenu.style.visibility = 'hidden';
                // Delay clearing display to allow animation
                setTimeout(() => {
                    if(!navMenu.classList.contains('active')) navMenu.style.display = 'none';
                }, 300);
            }
        });

        // Close menu when clicking a link
        navMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                // SAVE POSITION for seamless transition
                if (typeof navIndicator !== 'undefined' && navIndicator) {
                    sessionStorage.setItem('navIndicatorLeft', navIndicator.style.left);
                    sessionStorage.setItem('navIndicatorWidth', navIndicator.style.width);
                }

                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
                document.body.style.overflow = '';
                // Clear inline styles
                navMenu.style.maxHeight = '';
                navMenu.style.opacity = '';
                navMenu.style.visibility = '';
            });
        });

        // Add a global listener for any link that might point to a page in the nav
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && !link.href.includes('#')) {
                if (typeof navIndicator !== 'undefined' && navIndicator) {
                    sessionStorage.setItem('navIndicatorLeft', navIndicator.style.left);
                    sessionStorage.setItem('navIndicatorWidth', navIndicator.style.width);
                }
            }
        }, { passive: true });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if(!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
                document.body.style.overflow = '';
                // Clear inline styles
                navMenu.style.maxHeight = '';
                navMenu.style.opacity = '';
                navMenu.style.visibility = '';
            }
        });
    } else {
        console.error('Navbar elements NOT found!'); // DEBUG
    }

    // ========== NAVBAR INDICATOR SLIDE ==========
    const navIndicator = document.getElementById('navIndicator');
    // navMenu is already defined above
    
    // 1. Refined Initial State Logic: Support cross-page sliding for all links including Games
    function initNavbarIndicator() {
        if (!navIndicator || !navMenu) return;

        let activeLink = navMenu.querySelector('a.active');
        const prevLeft = sessionStorage.getItem('navIndicatorLeft');
        const prevWidth = sessionStorage.getItem('navIndicatorWidth');
        const isGamesAnchor = window.location.hash === '#games';
        
        // Reset state: Always start hidden and stationary
        navIndicator.classList.remove('animated');
        navIndicator.style.opacity = '0';

        // Override active status for Games hash navigation
        if (isGamesAnchor && isHomePage) {
            const gamesLink = document.getElementById('gamesLink');
            if (gamesLink) {
                // Ensure no scroll fight during initial anchor jump
                isManualScrolling = true;
                if (scrollTimeout) clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => { isManualScrolling = false; }, 1500);

                navLinks.forEach(l => l.classList.remove('active'));
                gamesLink.classList.add('active');
                activeLink = gamesLink; // Set as the target for the slide
            }
        }

        if (activeLink) {
            if (prevLeft && prevWidth) {
                // PREMIUM CROSS-PAGE SLIDE: Start from the previous page's position
                navIndicator.style.left = prevLeft;
                navIndicator.style.width = prevWidth;
                navIndicator.style.opacity = '1';
                
                // Trigger the slide to the current link after a tiny delay
                // setTimeout(..., 10) is often more reliable than requestAnimationFrame for triggering a computed transition
                setTimeout(() => {
                    if (navIndicator) {
                        navIndicator.classList.add('animated');
                        updateIndicator(true);
                    }
                }, 30);
            } else {
                // CLEAN SNAP: No history (e.g., fresh load or reload)
                const linkRect = activeLink.getBoundingClientRect();
                const menuRect = navMenu.getBoundingClientRect();
                navIndicator.style.width = `${linkRect.width}px`;
                navIndicator.style.left = `${linkRect.left - menuRect.left}px`;
                navIndicator.style.opacity = '1';
                
                // Re-enable animations for future interactions
                setTimeout(() => {
                    if (navIndicator) navIndicator.classList.add('animated');
                }, 100);
            }
        }

        // Clean up storage ONLY after a short delay to ensure it was used
        setTimeout(() => {
            sessionStorage.removeItem('navIndicatorLeft');
            sessionStorage.removeItem('navIndicatorWidth');
        }, 500);
    }

    function updateIndicator(useTransition = true) {
        if (!navIndicator || !navMenu) return;
        const activeLink = navMenu.querySelector('a.active');
        
        if (activeLink) {
            const linkRect = activeLink.getBoundingClientRect();
            const menuRect = navMenu.getBoundingClientRect();
            
            if (useTransition) navIndicator.classList.add('animated');
            else navIndicator.classList.remove('animated');
            
            navIndicator.style.width = `${linkRect.width}px`;
            navIndicator.style.left = `${linkRect.left - menuRect.left}px`;
            navIndicator.style.opacity = '1';
        } else {
            navIndicator.style.opacity = '0';
        }
    }

    // Helper to keep indicator synced during layout changes (like navbar expansion)
    let syncActive = false;
    let expansionPending = false;

    function syncWhileExpanding() {
        if (!syncActive) return;
        
        // Use true but manage the animated class at a higher level
        updateIndicator(false); 
        
        requestAnimationFrame(syncWhileExpanding);
    }

    // Run when DOM is ready to avoid layout shifts
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initNavbarIndicator();
            checkScroll(); // Re-check once DOM structure is solid
        });
    } else {
        initNavbarIndicator();
        checkScroll();
    }

    window.addEventListener('load', () => {
        checkScroll();
        updateIndicator(true);
    });
    window.addEventListener('resize', () => updateIndicator(false));
    // ========== NAVBAR SCROLL EFFECT & ACTIVE STATE ==========
    // All variables already defined globally above

    if (navLinks) {
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // SAVE POSITION for seamless transition
                if (typeof navIndicator !== 'undefined' && navIndicator) {
                    const style = window.getComputedStyle(navIndicator);
                    sessionStorage.setItem('navIndicatorLeft', style.left);
                    sessionStorage.setItem('navIndicatorWidth', style.width);
                }

                // SAVE SCROLL STATE to prevent jump on next page load
                if (navbar && navbar.classList.contains('scrolled')) {
                    sessionStorage.setItem('navWasScrolled', 'true');
                } else {
                    sessionStorage.removeItem('navWasScrolled');
                }

                // Smooth Scroll logic for Active Links or Anchor links
                const href = this.getAttribute('href');
                
                if (href.includes('#')) {
                    const targetId = href.split('#')[1];
                    const targetEl = document.getElementById(targetId);
                    
                    if (targetEl && isHomePage) {
                        e.preventDefault();
                        isManualScrolling = true;
                        if (scrollTimeout) clearTimeout(scrollTimeout);

                        const offset = 120; // Account for floating navbar
                        const targetPos = targetEl.getBoundingClientRect().top + window.scrollY - offset;
                        
                        window.scrollTo({ top: targetPos, behavior: 'smooth' });
                        
                        // Switch active class manually for anchor
                        navLinks.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                        updateIndicator(true);

                        // Reset flag after scroll finishes
                        scrollTimeout = setTimeout(() => { isManualScrolling = false; }, 1000);

                        // Close mobile menu
                        if (typeof navMenu !== 'undefined' && navMenu.classList.contains('active')) {
                            navMenu.classList.remove('active');
                            navToggle.classList.remove('active');
                        }
                        return;
                    }
                }

                // DOUBLE-CLICK REFRESH: If user clicks the currently active menu, scroll to top as a "refresh" action
                if (this.classList.contains('active')) {
                    e.preventDefault();
                    
                    // Only scroll if not already at top
                    if (window.scrollY > 10) {
                        isManualScrolling = true;
                        if (scrollTimeout) clearTimeout(scrollTimeout);

                        // RESET SCROLL STATE
                        sessionStorage.removeItem('navWasScrolled');
                        if (navbar) navbar.classList.remove('scrolled');

                        window.scrollTo({ top: 0, behavior: 'smooth' });

                        scrollTimeout = setTimeout(() => { isManualScrolling = false; }, 1000);
                    }

                    // Close mobile menu if open
                    if (typeof navMenu !== 'undefined' && navMenu.classList.contains('active')) {
                        navMenu.classList.remove('active');
                        navToggle.classList.remove('active');
                    }
                    return; // Stop further processing
                }

                // HOME PAGE SPECIAL: Clicking Home link while on home page
                if (isHomePage && (href === 'index.php' || href === 'index.php#')) {
                    e.preventDefault();
                    isManualScrolling = true;
                    if (scrollTimeout) clearTimeout(scrollTimeout);

                    // RESET SCROLL STATE to prevent "stuck in scrolled" on return
                    sessionStorage.removeItem('navWasScrolled');
                    if (navbar) navbar.classList.remove('scrolled');

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    
                    // Switch active class for home
                    navLinks.forEach(l => l.classList.remove('active'));
                    const homeLink = document.getElementById('homeLink');
                    if (homeLink) homeLink.classList.add('active');
                    updateIndicator(true);

                    scrollTimeout = setTimeout(() => { isManualScrolling = false; }, 1000);

                    // Close mobile menu
                    if (typeof navMenu !== 'undefined' && navMenu.classList.contains('active')) {
                        navMenu.classList.remove('active');
                        navToggle.classList.remove('active');
                    }
                }
            });
        });
    }

    // Auto-switch Active State on Scroll (Home vs Games)
    if (isHomePage) {
        window.addEventListener('scroll', function() {
            if (isManualScrolling) return; // Prevent "pull back" during programmatic scroll

            const gamesSection = document.getElementById('games');
            const homeLink = document.getElementById('homeLink');
            const gamesLink = document.getElementById('gamesLink');
            
            if (gamesSection && homeLink && gamesLink) {
                const scrollPos = window.scrollY;
                const gamesPos = gamesSection.offsetTop - 150;

                if (scrollPos >= gamesPos) {
                    if (!gamesLink.classList.contains('active')) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        gamesLink.classList.add('active');
                        updateIndicator(true);
                    }
                } else {
                    if (!homeLink.classList.contains('active')) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        homeLink.classList.add('active');
                        updateIndicator(true);
                    }
                }
            }
        });
    }
    
    function checkScroll() {
        if(navbar) {
            const wasScrolled = sessionStorage.getItem('navWasScrolled') === 'true';
            
            // Expansion pending check prevents scroll listener from "fighting" the animation
            if(window.scrollY > 50 || (wasScrolled && !expansionPending && window.scrollY < 50)) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
        
        if(backToTop) {
            if(window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        }
    }

    window.addEventListener('scroll', checkScroll);
    
    // Initial check: applies class immediately without animation
    // 1. Initial State Sync
    checkScroll();
    
    // 2. IMPORTANT: Run indicator init AFTER checkScroll so it measures the correct navbar layout (pill vs full)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavbarIndicator);
    } else {
        initNavbarIndicator();
    }
    
    // 3. Reveal Navbar Immediate
    if(navbar) {
        void navbar.offsetHeight; 
        navbar.style.opacity = '1';
    }

    // 4. Trigger Morphing Expansion Animation
    setTimeout(() => {
        if(navbar) {
            navbar.classList.remove('no-transition');
            void navbar.offsetHeight; 

            // If we land at top but were scrolled on the previous page -> Trigger smooth expand
            if (sessionStorage.getItem('navWasScrolled') === 'true' && window.scrollY < 50) {
                expansionPending = true; 
                syncActive = true;
                syncWhileExpanding();

                // Start expansion
                navbar.classList.remove('scrolled');
                sessionStorage.removeItem('navWasScrolled');

                // Cleanup when animation ends
                const endHandler = function(e) {
                    // Check specifically for width or max-width concluding
                    if (e.propertyName === 'width' || e.propertyName === 'max-width' || e.propertyName === 'top') {
                        syncActive = false;
                        expansionPending = false;
                        
                        // RE-ENABLE ANIMATION: Crucial for the slide to work again
                        setTimeout(() => {
                            updateIndicator(true);
                        }, 50);
                        
                        navbar.removeEventListener('transitionend', endHandler);
                    }
                };
                navbar.addEventListener('transitionend', endHandler);

                // Safety fallback
                setTimeout(() => { 
                    syncActive = false; 
                    expansionPending = false; 
                    updateIndicator(true);
                }, 700);
            }

            const logoIcon = navbar.querySelector('.logo-icon');
            if(logoIcon) logoIcon.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        }
    }, 100);

    // ========== BACK TO TOP ==========
    if(backToTop) {
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            isManualScrolling = true;
            if (scrollTimeout) clearTimeout(scrollTimeout);

            // RESET SCROLL STATE
            sessionStorage.removeItem('navWasScrolled');
            if (navbar) navbar.classList.remove('scrolled');

            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // ONLY sync indicator to Home if we ARE on the home page
            if (isHomePage) {
                navLinks.forEach(l => l.classList.remove('active'));
                const homeLink = document.getElementById('homeLink');
                if (homeLink) homeLink.classList.add('active');
                updateIndicator(true);
            }
            // On non-home pages, keep the current active indicator as-is

            scrollTimeout = setTimeout(() => { isManualScrolling = false; }, 1000);
        });
    }

    // ========== CHECKOUT SUMMARY ==========
    const productRadios = document.querySelectorAll('input[name="product_id"]');
    
    function updateSummary() {
        const checked = document.querySelector('input[name="product_id"]:checked');
        if(!checked) return;
        
        const price = parseInt(checked.dataset.price) || 0;
        const name = checked.dataset.name || '-';
        const adminFee = 1000;
        const total = price + adminFee;
        
        const elProduct = document.getElementById('summary-product');
        const elPrice = document.getElementById('summary-price');
        const elTotal = document.getElementById('summary-total');
        
        if(elProduct) elProduct.textContent = name;
        if(elPrice) elPrice.textContent = 'Rp ' + price.toLocaleString('id-ID');
        if(elTotal) elTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    
    productRadios.forEach(function(radio) {
        radio.addEventListener('change', updateSummary);
    });

    // ========== LEGAL PAGES SMOOTH SCROLL ANIMATION ==========
    const legalPages = ['privacy.php', 'partnership.php', 'about.php', 'career.php', 'blog.php', 'contact.php', 'faq.php', 'testimonials.php'];
    
    function isLegalPage(url) {
        return legalPages.some(page => url.includes(page));
    }

    // 1. Detect clicks on links to legal pages
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href) {
            const url = link.href;
            // Check if it's a legal page AND not a hash link (e.g. #terms)
            if (isLegalPage(url) && !url.includes('#')) {
                
                // FUNCTION: Prepare state
                const saveNavState = () => {
                    if (typeof navIndicator !== 'undefined' && navIndicator) {
                        const style = window.getComputedStyle(navIndicator);
                        sessionStorage.setItem('navIndicatorLeft', style.left);
                        sessionStorage.setItem('navIndicatorWidth', style.width);
                    }
                    const navbar = document.querySelector('.navbar');
                    if (navbar && navbar.classList.contains('scrolled')) {
                        sessionStorage.setItem('navWasScrolled', 'true');
                    } else {
                        sessionStorage.removeItem('navWasScrolled');
                    }
                    sessionStorage.setItem('triggerLegalScroll', 'true');
                };

                // NEW: If navigating from a legal page to another legal page AND we are scrolled down
                if (isLegalPage(window.location.href) && window.scrollY > 50) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    saveNavState();
                    
                    // Redirect after scroll animation
                    setTimeout(() => {
                        window.location.href = url;
                    }, 600); // 600ms matches smooth scroll feel
                    return;
                }

                // Default: Just save state and proceed
                saveNavState();
            }
        }
    });

    // 2. Execute Scroll on Load
    if (sessionStorage.getItem('triggerLegalScroll') === 'true') {
        // Only run if we are actually on a legal page and no hash exists
        if (isLegalPage(window.location.pathname) && !window.location.hash) {
            const legalContent = document.querySelector('.legal-content');
            
            if (legalContent) {
                // Disable native scroll restoration to allow header animation
                if ('scrollRestoration' in history) {
                    history.scrollRestoration = 'manual';
                }
                
                // Start at top to show header animation
                window.scrollTo(0, 0);

                // Wait for header animation (approx 0.8s) then scroll
                setTimeout(() => {
                    const offset = 100; // Navbar + padding
                    const targetPos = legalContent.getBoundingClientRect().top + window.scrollY - offset;

                    window.scrollTo({
                        top: targetPos,
                        behavior: 'smooth'
                    });
                    
                    // Cleanup flag
                    sessionStorage.removeItem('triggerLegalScroll');
                }, 2000); // 2000ms delay to let user read the header badge and title before scrolling
            }
        } else {
            // Cleanup if redirects happened or we're not on target
            sessionStorage.removeItem('triggerLegalScroll');
        }
    }

    // ========== BOUNCING SCROLL EFFECT ==========
    function initBouncyScroll(el, useWindow = false) {
        if (!el && !useWindow) return;
        let delta = 0;
        let bounceTimer = null;
        const target = useWindow ? window : el;
        const scrollEl = useWindow ? (document.scrollingElement || document.documentElement) : el;
        const moveEl = useWindow ? (document.getElementById('pageWrapper') || document.body) : el;

        target.addEventListener('wheel', (e) => {
            const atTop = scrollEl.scrollTop <= 0;
            const atBottom = scrollEl.scrollTop + scrollEl.clientHeight >= scrollEl.scrollHeight - 1;

            if ((atTop && e.deltaY < 0) || (atBottom && e.deltaY > 0)) {
                // If it's the main window, we allow a bit more move but avoid breaking fixed headers
                e.preventDefault();
                delta -= e.deltaY * 0.2;
                delta = Math.max(-60, Math.min(60, delta));

                moveEl.style.transition = 'none';
                moveEl.style.transform = `translateY(${delta}px)`;

                clearTimeout(bounceTimer);
                bounceTimer = setTimeout(() => {
                    moveEl.style.transition = 'transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    moveEl.style.transform = '';
                    delta = 0;
                }, 40);
            }
        }, { passive: false });

        // Touch Support
        let startY = 0;
        target.addEventListener('touchstart', (e) => {
            startY = e.touches[0].pageY;
        }, { passive: true });

        target.addEventListener('touchmove', (e) => {
            const currentY = e.touches[0].pageY;
            const diff = currentY - startY;
            const atTop = scrollEl.scrollTop <= 0;
            const atBottom = scrollEl.scrollTop + scrollEl.clientHeight >= scrollEl.scrollHeight - 1;

            if ((atTop && diff > 0) || (atBottom && diff < 0)) {
                // e.preventDefault() is often restricted in touchmove, so we just apply transform
                delta = Math.sign(diff) * Math.pow(Math.abs(diff), 0.6); // log scale for resistance
                moveEl.style.transition = 'none';
                moveEl.style.transform = `translateY(${delta}px)`;
            }
        }, { passive: true });

        target.addEventListener('touchend', () => {
            moveEl.style.transition = 'transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            moveEl.style.transform = '';
            delta = 0;
        });
    }

    // Initialize for User Pages
    // Only if not in an iframe or similar
    if (window.self === window.top) {
        initBouncyScroll(null, true);
    }
})();
</script>
