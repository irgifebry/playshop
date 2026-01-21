// Select game and redirect to checkout
function selectGame(gameId, gameName) {
    window.location.href = `checkout.php?game_id=${gameId}`;
}

// Update order summary on checkout page
document.addEventListener('DOMContentLoaded', function() {
    const productRadios = document.querySelectorAll('input[name="product_id"]');
    
    productRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const price = parseInt(this.dataset.price);
            const name = this.dataset.name;
            const adminFee = 1000;
            const total = price + adminFee;
            
            document.getElementById('summary-product').textContent = name;
            document.getElementById('summary-price').textContent = 'Rp ' + price.toLocaleString('id-ID');
            document.getElementById('summary-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
        });
    });
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