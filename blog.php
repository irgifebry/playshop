<?php 
session_start(); 
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog & News | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include "includes/header.php"; ?>



    <section class="legal-header">
        <div class="container">
            <span class="legal-badge">Informasi & Update</span>
            <h1>Blog & Berita</h1>
            <p>Update promo, event, dan tips top up game terbaru untuk Anda.</p>
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
                    <a href="blog.php" class="legal-nav-item active">Blog</a>
                    <a href="contact.php" class="legal-nav-item">Kontak</a>
                    <a href="faq.php" class="legal-nav-item">FAQ</a>
                    <a href="testimonials.php" class="legal-nav-item">Testimoni</a>
                </nav>
            </aside>

            <main class="legal-content">
                <div class="legal-block">
                    <h2>Artikel Terbaru</h2>
                    
                    <?php if(count($posts) > 0): ?>
                        <div style="display: grid; gap: 2rem; margin-top: 2rem;">
                            <?php foreach($posts as $p): ?>
                                <article style="display: flex; gap: 1.5rem; flex-wrap: wrap; background: white; border-radius: 16px; border: 1px solid #f3f4f6; overflow: hidden; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div style="flex: 0 0 200px; height: 140px; background: #eee;">
                                        <?php if($p['image_path']): ?>
                                            <img src="<?php echo asset_url($p['image_path']); ?>" alt="thumb" style="width:100%; height:100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="width:100%; height:100%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">📰</div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex: 1; padding: 1.5rem 1.5rem 1.5rem 0; min-width: 250px;">
                                        <small style="color: var(--primary); font-weight: 700;"><?php echo date('d M Y', strtotime($p['created_at'])); ?></small>
                                        <h3 style="margin: 0.5rem 0;"><?php echo htmlspecialchars($p['title']); ?></h3>
                                        <div style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 1rem;">
                                            <?php echo substr(strip_tags($p['content']), 0, 120); ?>...
                                        </div>
                                        <a href="post-detail.php?slug=<?php echo $p['slug']; ?>" style="color: var(--primary); text-decoration: none; font-weight: 700; font-size: 0.9rem;">Baca Selengkapnya →</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="background: #f9fafb; padding: 3rem; border-radius: 20px; text-align: center; border: 2px dashed #e5e7eb; margin-top: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">✍️</div>
                            <h3 style="margin-bottom: 0.5rem;">Sedang Dalam Penulisan</h3>
                            <p style="color: var(--text-light);">Nantikan update perdana kami di minggu depan!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>


