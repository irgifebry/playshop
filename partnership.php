<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partnership | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include "includes/header.php"; ?>
    


    <section class="legal-header">
        <div class="container">
            <span class="legal-badge">Kolaborasi</span>
            <h1>Partnership</h1>
            <p>Jadilah mitra strategis PLAYSHOP.ID dan tumbuh bersama kami.</p>
        </div>
    </section>

    <div class="container">
        <div class="legal-layout">
            <aside class="legal-sidebar">
                <nav class="legal-nav">
                    <a href="privacy.php" class="legal-nav-item">Kebijakan Privasi</a>
                    <a href="partnership.php" class="legal-nav-item active">Partnership</a>
                    <a href="about.php" class="legal-nav-item">Tentang Kami</a>
                    <a href="career.php" class="legal-nav-item">Karier</a>
                    <a href="blog.php" class="legal-nav-item">Blog</a>
                    <a href="contact.php" class="legal-nav-item">Kontak</a>
                    <a href="faq.php" class="legal-nav-item">FAQ</a>
                    <a href="testimonials.php" class="legal-nav-item">Testimoni</a>
                </nav>
            </aside>

            <main class="legal-content">
                <div class="legal-block">
                    <h2>Ajukan Kerja Sama</h2>
                    <p>Kami membuka peluang kerja sama yang luas bagi pemilik komunitas gaming, influencer, reseller, maupun korporasi yang ingin mengintegrasikan layanan top up ke dalam ekosistem mereka.</p>
                    
                    <h3>Keuntungan Menjadi Mitra:</h3>
                    <ul>
                        <li>Harga khusus reseller dengan margin yang kompetitif.</li>
                        <li>Dukungan API integrasi yang cepat dan stabil.</li>
                        <li>Akses ke promosi eksklusif untuk komunitas Anda.</li>
                        <li>Layanan prioritas dari tim dukungan pelanggan kami.</li>
                    </ul>

                    <p>Saat ini kami sedang menyiapkan portal khusus mitra. Sementara itu, silakan gunakan halaman kontak untuk mengirim proposal kerja sama atau pertanyaan awal Anda.</p>
                    
                    <div style="margin-top: 2rem;">
                        <a href="contact.php" class="btn-primary" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">Hubungi Tim Kemitraan</a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>


