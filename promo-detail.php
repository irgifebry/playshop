<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

$banner_id = (int)($_GET['banner_id'] ?? 0);

if ($banner_id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, title, description, image_path, link_url, is_active
        FROM banners
        WHERE id = ? AND is_active = 1
    ");
    $stmt->execute([$banner_id]);
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$banner) {
        header('Location: index.php');
        exit;
    }
} catch (Exception $e) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($banner['title']); ?> | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include "includes/header.php"; ?>

    <!-- Promo Detail Section -->
    <main class="promo-detail-section">
        <div class="container">
            <a href="index.php" class="btn-back-home">&larr; Kembali ke Beranda</a>
            
            <article class="promo-detail-card">
                <div class="promo-detail-banner">
                    <img src="<?php echo htmlspecialchars(asset_url($banner['image_path'])); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" />
                </div>
                
                <div class="promo-detail-body">
                    <h1 class="promo-detail-title"><?php echo htmlspecialchars($banner['title']); ?></h1>
                    
                    <?php if (!empty($banner['description'])): ?>
                        <div class="promo-detail-desc">
                            <?php echo nl2br(htmlspecialchars($banner['description'])); ?>
                        </div>
                    <?php else: ?>
                        <div class="promo-detail-desc text-muted italic">
                            Belum ada deskripsi detail untuk promo ini.
                        </div>
                    <?php endif; ?>

                    <div class="promo-detail-btn-group">
                        <a href="index.php#games" class="btn-primary" style="padding: 0.8rem 2rem; border-radius: 12px; text-decoration: none;">Top Up Sekarang</a>
                        <a href="promo.php" class="btn-secondary" style="padding: 0.8rem 2rem; border-radius: 12px; text-decoration: none;">Lihat Promo Lain</a>
                    </div>
                </div>
            </article>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>


