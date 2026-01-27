// Main Script for PLAYSHOP.ID

// Global Helper Functions
function selectGame(gameId, gameName) {
    window.location.href = `checkout.php?game_id=${gameId}`;
}

document.addEventListener('DOMContentLoaded', function() {
    // ========== CHECKOUT SUMMARY LOGIC ==========
    const productRadios = document.querySelectorAll('input[name="product_id"]');
    const voucherInput = document.getElementById('voucher_code');
    
    function updateSummary() {
        const checked = document.querySelector('input[name="product_id"]:checked');
        if (!checked) return;

        const price = parseInt(checked.dataset.price);
        const name = checked.dataset.name;
        const adminFee = 1000;

        // Dummy preview discount: show 0 (real discount calculated on server in payment.php)
        const discount = 0;
        const total = price + adminFee - discount;
        
        // Safe update - check if elements exist first
        const elProduct = document.getElementById('summary-product');
        const elPrice = document.getElementById('summary-price');
        const elDiscount = document.getElementById('summary-discount');
        const elTotal = document.getElementById('summary-total');

        if(elProduct) elProduct.textContent = name;
        if(elPrice) elPrice.textContent = 'Rp ' + price.toLocaleString('id-ID');
        if(elDiscount) elDiscount.textContent = 'Rp ' + discount.toLocaleString('id-ID');
        if(elTotal) elTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    productRadios.forEach(radio => radio.addEventListener('change', updateSummary));
    if (voucherInput) voucherInput.addEventListener('input', updateSummary);


    // ========== NAVBAR TOGGLE FUNCTIONALITY ==========
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navbar = document.querySelector('.navbar');
    const backToTop = document.getElementById('backToTop');
    
    if(navToggle && navMenu) {
        navToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent immediate closing
            const isActive = navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
            
            // If opening, scroll to top for better visibility and lock body
            if(isActive) {
                // Optional: window.scrollTo({ top: 0, behavior: 'smooth' });
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = navMenu.contains(event.target) || navToggle.contains(event.target);
            if (!isClickInside && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Auto Close Mobile Menu on Link Click
        const navLinks = document.querySelectorAll('.nav-menu a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if(navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    navToggle.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    }

    // ========== SCROLL EFFECTS ==========
    window.addEventListener('scroll', function() {
        // Header Scroll Class
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        // Back to Top Visibility
        if (backToTop) {
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        }
    });

    // Smooth Back to Top Action
    if(backToTop) {
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ========== SMOOTH SCROLL ANCHOR ==========
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if(targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if(target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});