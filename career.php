<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include "includes/header.php"; ?>



    <section class="legal-header">
        <div class="container">
            <span class="legal-badge">Peluang Kerja</span>
            <h1>Karier di PLAYSHOP.ID</h1>
            <p>Ayo berkontribusi dalam memajukan ekosistem gaming di Indonesia.</p>
        </div>
    </section>

    <div class="container">
        <div class="legal-layout">
            <aside class="legal-sidebar">
                <nav class="legal-nav">
                    <a href="privacy.php" class="legal-nav-item">Kebijakan Privasi</a>
                    <a href="partnership.php" class="legal-nav-item">Partnership</a>
                    <a href="about.php" class="legal-nav-item">Tentang Kami</a>
                    <a href="career.php" class="legal-nav-item active">Karier</a>
                    <a href="blog.php" class="legal-nav-item">Blog</a>
                    <a href="contact.php" class="legal-nav-item">Kontak</a>
                    <a href="faq.php" class="legal-nav-item">FAQ</a>
                    <a href="testimonials.php" class="legal-nav-item">Testimoni</a>
                </nav>
            </aside>

            <main class="legal-content">
                <div class="legal-block">
                    <h2>Bergabung Dengan Tim Kami</h2>
                    <p>Kami mencari individu yang bersemangat, inovatif, dan mencintai dunia game untuk membantu kami memberikan layanan top up terbaik bagi jutaan gamers di seluruh tanah air.</p>
                    
                    <h3>Mengapa Bergabung dengan Kami?</h3>
                    <ul>
                        <li>Budaya kerja yang dinamis, fleksibel, dan berorientasi pada hasil.</li>
                        <li>Kesempatan belajar langsung dari para profesional di industri e-commerce gaming.</li>
                        <li>Fasilitas kantor yang modern dan berlokasi strategis.</li>
                        <li>Bonus performa dan tunjangan kesehatan yang kompetitif.</li>
                    </ul>

                    <div style="background: #f9fafb; padding: 2rem; border-radius: 12px; margin-top: 2rem; border-left: 4px solid var(--primary);">
                        <h3>Posisi Tersedia</h3>
                        <p>Saat ini belum ada lowongan terbuka. Namun, kami selalu terbuka untuk talenta hebat! Kirimkan CV dan Portofolio Anda ke email <strong>hrd@playshop.id</strong> untuk kami simpan dalam database kami.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>


