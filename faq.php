<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Pertanyaan Umum | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>
    


    <section class="legal-header">
        <div class="container">
            <span class="legal-badge">Pusat Bantuan</span>
            <h1>Pertanyaan Umum (FAQ)</h1>
            <p>Temukan jawaban cepat untuk pertanyaan yang paling sering diajukan.</p>
        </div>
    </section>

    <div class="container">
        <div class="legal-layout">
            <aside class="legal-sidebar">
                <nav class="legal-nav">
                    <a href="privacy.php" class="legal-nav-item">Kebijakan Privasi</a>
                    <a href="partnership.php" class="legal-nav-item">Partnership</a>
                    <a href="about.php" class="legal-nav-item">Tentang Kami</a>
                    <a href="career.php" class="legal-nav-item">Karier</a>
                    <a href="blog.php" class="legal-nav-item">Blog</a>
                    <a href="contact.php" class="legal-nav-item">Kontak</a>
                    <a href="faq.php" class="legal-nav-item active">FAQ</a>
                    <a href="testimonials.php" class="legal-nav-item">Testimoni</a>
                </nav>
            </aside>

            <main class="legal-content">
                <div class="legal-block">
                    <h2>📱 Tentang Layanan</h2>
                    <div class="faq-container">
                        <div class="faq-item active">
                            <div class="faq-question">
                                <h3>Apa itu PLAYSHOP.ID?</h3>

                            </div>
                            <div class="faq-answer">
                                <p>PLAYSHOP.ID adalah platform top up game online yang menyediakan layanan pembelian diamond, UC, dan item game lainnya dengan proses cepat dan aman.</p>
                            </div>
                        </div>
                        <div class="faq-item active">
                            <div class="faq-question">
                                <h3>Berapa lama proses top up?</h3>

                            </div>
                            <div class="faq-answer">
                                <p>Rata-rata di bawah 1 menit setelah pembayaran berhasil dikonfirmasi oleh sistem kami secara otomatis.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="legal-block">
                    <h2>💳 Pembayaran</h2>
                    <div class="faq-container">
                        <div class="faq-item active">
                            <div class="faq-question">
                                <h3>Metode pembayaran apa saja yang tersedia?</h3>

                            </div>
                            <div class="faq-answer">
                                <p>Kami mendukung E-Wallet (DANA, OVO, GoPay), Bank Transfer (BCA, Mandiri, BRI), QRIS, dan retail (Indomaret/Alfamart).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="legal-block">
                    <h2>🎮 Akun & Transaksi</h2>
                    <div class="faq-container">
                        <div class="faq-item active">
                            <div class="faq-question">
                                <h3>Bagaimana jika salah memasukkan User ID?</h3>

                            </div>
                            <div class="faq-answer">
                                <p>Seluruh transaksi bersifat final. Mohon teliti User ID Anda sebelum membayar karena kami tidak bisa melakukan pembatalan untuk data yang salah.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-cta" style="margin-top: 3rem; background: #f9fafb; padding: 2rem; border-radius: 16px; text-align: center;">
                    <h3>Masih punya pertanyaan lain?</h3>
                    <p style="margin-bottom: 1.5rem; color: var(--text-light);">Tim Customer Service kami siap melayani Anda.</p>
                    <a href="contact.php" class="btn-primary" style="display:inline-block; padding: 0.8rem 2rem; border-radius: 8px;">Hubungi CS Kami</a>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script>
        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                // Close all
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Open clicked
                if(!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>

