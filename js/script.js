// Select game and redirect to checkout
function selectGame(gameId, gameName) {
    window.location.href = `checkout.php?game_id=${gameId}`;
}

// Update order summary on checkout page
document.addEventListener('DOMContentLoaded', function() {
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
        
        document.getElementById('summary-product').textContent = name;
        document.getElementById('summary-price').textContent = 'Rp ' + price.toLocaleString('id-ID');

        const discountEl = document.getElementById('summary-discount');
        if (discountEl) discountEl.textContent = 'Rp ' + discount.toLocaleString('id-ID');

        document.getElementById('summary-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    productRadios.forEach(radio => radio.addEventListener('change', updateSummary));
    if (voucherInput) voucherInput.addEventListener('input', updateSummary);
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});