<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="legal-header">
        <div class="container">
            <span class="legal-badge">Profil Perusahaan</span>
            <h1>Tentang PLAYSHOP.ID</h1>
            <p>Mengenal lebih dekat platform top up game #1 pilihan gamas Indonesia.</p>
        </div>
    </section>

    <div class="container">
        <div class="legal-layout">
            <aside class="legal-sidebar">
                <nav class="legal-nav">
                    <a href="privacy.php" class="legal-nav-item">Kebijakan Privasi</a>
                    <a href="partnership.php" class="legal-nav-item">Partnership</a>
                    <a href="about.php" class="legal-nav-item active">Tentang Kami</a>
                    <a href="career.php" class="legal-nav-item">Karier</a>
                    <a href="blog.php" class="legal-nav-item">Blog</a>
                    <a href="contact.php" class="legal-nav-item">Kontak</a>
                    <a href="faq.php" class="legal-nav-item">FAQ</a>
                    <a href="testimonials.php" class="legal-nav-item">Testimoni</a>
                </nav>
            </aside>

            <main class="legal-content">
                <div class="legal-block">
                    <h2>Cerita Kami</h2>
                    <p>PLAYSHOP.ID didirikan pada tahun 2024 dengan visi untuk memberikan layanan top up game yang cepat, aman, dan terpercaya kepada para gamers di Indonesia. Kami memahami bahwa waktu adalah hal yang berharga bagi para gamers, oleh karena itu kami berkomitmen untuk memberikan layanan top up yang super cepat dengan proses otomatis dalam hitungan detik.</p>
                    <p>Dengan dukungan teknologi terkini dan tim yang berpengalaman, kami terus berinovasi untuk memberikan pengalaman terbaik bagi pelanggan kami. Hingga saat ini, kami telah melayani lebih dari 100.000 transaksi sukses dan terus bertumbuh setiap harinya.</p>
                </div>

                <div class="legal-block">
                    <h2>Nilai-Nilai Kami</h2>
                    <div class="values-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                        <div class="value-card" style="padding: 1.5rem; background: #f9fafb; border-radius: 12px; text-align: center;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚡</div>
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Kecepatan</h3>
                            <p style="font-size: 0.9rem; color: var(--text-light);">Proses transaksi super cepat, rata-rata di bawah 1 menit.</p>
                        </div>
                        <div class="value-card" style="padding: 1.5rem; background: #f9fafb; border-radius: 12px; text-align: center;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔒</div>
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Keamanan</h3>
                            <p style="font-size: 0.9rem; color: var(--text-light);">Sistem keamanan berlapis untuk melindungi data Anda.</p>
                        </div>
                        <div class="value-card" style="padding: 1.5rem; background: #f9fafb; border-radius: 12px; text-align: center;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💯</div>
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Terpercaya</h3>
                            <p style="font-size: 0.9rem; color: var(--text-light);">Berlisensi resmi dan terpercaya oleh ribuan pelanggan.</p>
                        </div>
                    </div>
                </div>

                <div class="legal-block">
                    <h2>Tim Kami</h2>
                    <p>PLAYSHOP.ID dikelola oleh PT. Mitra Karya Informatika dengan tim profesional yang berpengalaman di bidang teknologi informasi dan gaming.</p>
                    <div class="grid-2-col" style="margin-top: 1rem;">
                        <div style="padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 12px; text-align: center;">
                            <div style="width: 60px; height: 60px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-weight: 700;">MN</div>
                            <h3 style="font-size: 1rem;">M. Najwan Busaman</h3>
                            <p style="font-size: 0.85rem; color: var(--text-light);">Backend Developer</p>
                        </div>
                        <div style="padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 12px; text-align: center;">
                            <div style="width: 60px; height: 60px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-weight: 700;">IF</div>
                            <h3 style="font-size: 1rem;">Irgi Febryansyah</h3>
                            <p style="font-size: 0.85rem; color: var(--text-light);">UI/UX Designer</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
