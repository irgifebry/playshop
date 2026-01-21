<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Pertanyaan Umum | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <a href="index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                        <span class="logo-icon">🎮</span>
                        <span class="logo-text">PLAYSHOP<span class="highlight">.ID</span></span>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="faq.php" class="active">FAQ</a></li>
                    <li><a href="contact.php">Kontak</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="faq-section">
        <div class="container">
            <h1 class="page-title">Pertanyaan yang Sering Diajukan (FAQ)</h1>
            <p class="page-subtitle">Temukan jawaban untuk pertanyaan Anda</p>

            <div class="faq-container">
                <div class="faq-category">
                    <h2>📱 Tentang Layanan</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Apa itu PLAYSHOP.ID?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>PLAYSHOP.ID adalah platform top up game online yang menyediakan layanan pembelian diamond, UC, dan item game lainnya dengan proses cepat dan aman. Kami melayani berbagai game populer seperti Mobile Legends, Free Fire, PUBG Mobile, dan lainnya.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Apakah top up di PLAYSHOP.ID aman?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Ya, sangat aman! Kami menggunakan sistem enkripsi untuk melindungi data Anda. Semua transaksi dilakukan melalui payment gateway resmi dan terpercaya. Kami tidak menyimpan data kartu kredit atau informasi pembayaran sensitif Anda.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Berapa lama proses top up?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Proses top up sangat cepat, rata-rata di bawah 1 menit setelah pembayaran berhasil. Dalam kondisi normal, diamond/UC akan langsung masuk ke akun game Anda secara otomatis.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-category">
                    <h2>💳 Pembayaran</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Metode pembayaran apa saja yang tersedia?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Kami menyediakan berbagai metode pembayaran:</p>
                            <ul>
                                <li>E-Wallet: DANA, OVO, GoPay, LinkAja</li>
                                <li>Bank Transfer: BCA, Mandiri, BNI, BRI</li>
                                <li>Retail: Indomaret, Alfamart</li>
                                <li>QRIS</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Apakah ada biaya admin?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Ya, ada biaya admin sebesar Rp 1.000 untuk setiap transaksi. Biaya ini sudah termasuk dalam total yang harus dibayar.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-category">
                    <h2>🎮 Cara Order</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Bagaimana cara melakukan top up?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Sangat mudah! Ikuti langkah berikut:</p>
                            <ol>
                                <li>Pilih game yang ingin di-top up</li>
                                <li>Masukkan User ID dan Zone ID (jika ada)</li>
                                <li>Pilih nominal diamond/UC yang diinginkan</li>
                                <li>Pilih metode pembayaran</li>
                                <li>Lakukan pembayaran</li>
                                <li>Diamond/UC otomatis masuk ke akun Anda</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Bagaimana cara menemukan User ID saya?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p><strong>Mobile Legends:</strong> Buka game → Profil → User ID ada di bawah nickname</p>
                            <p><strong>Free Fire:</strong> Buka game → Profil → User ID ada di pojok kiri atas</p>
                            <p><strong>PUBG Mobile:</strong> Buka game → Profil → ID ada di bawah karakter</p>
                            <p><strong>Genshin Impact:</strong> Buka game → Paimon Menu → Settings → Account → UID</p>
                        </div>
                    </div>
                </div>

                <div class="faq-category">
                    <h2>❓ Troubleshooting</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Diamond belum masuk setelah pembayaran?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Jika diamond belum masuk setelah 5 menit:</p>
                            <ol>
                                <li>Cek apakah User ID yang dimasukkan benar</li>
                                <li>Restart game Anda</li>
                                <li>Cek status pesanan di halaman "Cek Order"</li>
                                <li>Jika masih belum masuk, hubungi customer service kami</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Salah memasukkan User ID, bagaimana?</h3>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Mohon periksa kembali User ID sebelum melakukan pembayaran. Jika sudah terlanjur salah, diamond akan masuk ke User ID yang Anda input. Kami tidak bisa membatalkan atau memindahkan diamond yang sudah terkirim.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-cta">
                <h3>Masih ada pertanyaan?</h3>
                <p>Hubungi customer service kami</p>
                <a href="contact.php" class="btn-primary">Hubungi Kami</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 PLAYSHOP.ID - Transaksi Cepat & Aman</p>
        </div>
    </footer>

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