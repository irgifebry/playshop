<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

if(!isset($_GET['slug'])) {
    header('Location: blog.php');
    exit;
}

$slug = $_GET['slug'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ?");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$post) {
    header('Location: blog.php');
    exit;
}

// Fetch other posts for sidebar
$other_posts = $pdo->prepare("SELECT * FROM posts WHERE id != ? ORDER BY created_at DESC LIMIT 5");
$other_posts->execute([$post['id']]);
$others = $other_posts->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .post-content img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin: 1.5rem 0;
        }
        .post-content h3 {
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .post-content p {
            line-height: 1.8;
            margin-bottom: 1.25rem;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <?php include "includes/header.php"; ?>

    <section class="legal-header">
        <div class="container">
            <span class="legal-badge"><a href="blog.php" style="color: inherit; text-decoration: none;">← Kembali ke Blog</a></span>
            <h1 style="font-size: 2.2rem !important;"><?php echo htmlspecialchars($post['title']); ?></h1>
            <p>Terbit pada <?php echo date('d M Y', strtotime($post['created_at'])); ?></p>
        </div>
    </section>

    <div class="container">
        <div class="legal-layout">
            <aside class="legal-sidebar">
                <div class="legal-nav">
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; padding: 0 0.5rem;">Artikel Lainnya</h3>
                    <div style="display: grid; gap: 1rem;">
                        <?php foreach($others as $oth): ?>
                            <a href="post-detail.php?slug=<?php echo $oth['slug']; ?>" class="legal-nav-item" style="font-size: 0.9rem; padding: 0.6rem 0.8rem;">
                                <?php echo htmlspecialchars($oth['title']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="background: var(--primary); padding: 1.5rem; border-radius: 20px; color: white; margin-top: 2rem; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);">
                    <h4 style="margin-bottom: 0.5rem;">Butuh Diamond?</h4>
                    <p style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 1rem;">Top up otomatis 24 jam</p>
                    <a href="index.php" class="btn-primary" style="background: white; color: var(--primary); width: 100%; text-align: center; border-radius: 10px; font-weight: 700; display: block; text-decoration: none; padding: 0.6rem;">Beli Sekarang</a>
                </div>
            </aside>

            <main class="legal-content">
                <?php if($post['image_path']): ?>
                    <img src="<?php echo asset_url($post['image_path']); ?>" alt="Banner" style="width: 100%; border-radius: 24px; margin-bottom: 2rem;">
                <?php endif; ?>

                <div class="post-content">
                    <?php echo $post['content']; ?>
                </div>

                <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; gap: 10px;">
                        <span style="background: #f3f4f6; padding: 0.5rem 1rem; border-radius: 30px; font-size: 0.85rem; font-weight: 600;">#Gaming</span>
                        <span style="background: #f3f4f6; padding: 0.5rem 1rem; border-radius: 30px; font-size: 0.85rem; font-weight: 600;">#TopUp</span>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

