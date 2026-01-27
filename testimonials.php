<?php 
session_start(); 
require_once 'config/database.php';

// Fetch real reviews from DB
$reviews = [];
try {
    $stmt = $pdo->query("SELECT * FROM testimonials WHERE is_shown = 1 ORDER BY created_at DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $reviews = []; }
$featured_testimonials = array_slice($reviews, 0, 3);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni Pelanggan | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>

    <section class="legal-header">
        <div class="container">
            <span class="legal-badge">Bukti Kepuasan</span>
            <h1>Apa Kata Mereka?</h1>
            <p>Ribuan transaksi sukses setiap harinya adalah bukti kepercayaan pelanggan kepada PLAYSHOP.ID.</p>
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
                    <a href="faq.php" class="legal-nav-item">FAQ</a>
                    <a href="testimonials.php" class="legal-nav-item active">Testimoni</a>
                </nav>
            </aside>

            <main class="legal-content">
                <div class="legal-block">
                    <h2>Testimoni Pilihan</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                        <?php foreach($featured_testimonials as $ft): ?>
                        <div style="background: #f9fafb; padding: 1.5rem; border-radius: 16px; border: 1px solid #e5e7eb; position: relative;">
                            <div style="color: #f59e0b; margin-bottom: 0.75rem;">
                                <?php for($i=0; $i<$ft['rating']; $i++): ?>⭐<?php endfor; ?>
                            </div>
                            <p style="font-style: italic; color: var(--text); margin-bottom: 1.5rem; font-size: 0.95rem;">"<?php echo htmlspecialchars($ft['comment']); ?>"</p>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">
                                    <?php echo strtoupper(substr($ft['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h4 style="font-size: 0.9rem; margin: 0;"><?php echo htmlspecialchars($ft['name']); ?></h4>
                                    <small style="color: var(--text-light);">Customer Terverifikasi</small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="legal-block">
                    <h2>Semua Review</h2>
                    <?php if(count($reviews) > 0): ?>
                        <div style="display: grid; gap: 1rem; margin-top: 1rem;">
                            <?php foreach($reviews as $r): ?>
                                <div class="flex-responsive" style="padding: 1.25rem; border-bottom: 1px solid #f3f4f6;">
                                    <div style="font-weight: 800; color: var(--primary); font-size: 1.2rem; min-width: 50px;"><?php echo $r['rating']; ?>/5</div>
                                    <div style="flex: 1;">
                                        <p style="margin: 0 0 0.5rem; color: var(--text);">"<?php echo htmlspecialchars($r['comment']); ?>"</p>
                                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                            <span style="font-weight: 600; font-size: 0.85rem;"><?php echo htmlspecialchars($r['name'] ?? 'Guest'); ?></span>
                                            <small style="color: var(--text-light);"><?php echo date('d M Y', strtotime($r['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 16px; border: 2px dashed #e5e7eb; margin-top: 1rem;">
                            <p style="color: var(--text-light); margin: 0;">Belum ada review terbaru dari database. Jadilah yang pertama memberikan feedback!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); padding: 2.5rem; border-radius: 20px; color: white; text-align: center; margin-top: 2rem;">
                    <h3 style="margin-bottom: 0.5rem; font-size: 1.5rem;">Puas dengan Layanan Kami?</h3>
                    <p style="opacity: 0.9; margin-bottom: 1.5rem;">Ayo top up sekarang dan berikan ulasan terbaikmu!</p>
                    <a href="index.php" class="btn-primary" style="background: white; color: var(--primary); font-weight: 800; padding: 0.8rem 2.5rem; border-radius: 12px; text-decoration: none; display: inline-block;">Top Up Sekarang</a>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>


