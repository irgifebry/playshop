<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$deposit_id = (int)$_GET['id'];

// Fetch deposit details
$stmt = $pdo->prepare("SELECT d.*, pm.name as method_name, pm.code as method_code, pm.image_path as method_image, pm.type as method_type 
                       FROM deposits d 
                       JOIN payment_methods pm ON d.payment_method_id = pm.id 
                       WHERE d.id = ? AND d.user_id = ?");
$stmt->execute([$deposit_id, $user_id]);
$deposit = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$deposit) {
    die("Data deposit tidak ditemukan.");
}

// Simulasi konfirmasi pembayaran
if (isset($_POST['confirm_payment'])) {
    if ($deposit['status'] === 'pending') {
        $pdo->prepare("UPDATE deposits SET status = 'success' WHERE id = ?")->execute([$deposit_id]);
        $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")->execute([$deposit['amount'], $user_id]);
        
        // Refresh data
        header("Location: deposit-pay.php?id=$deposit_id&success=1");
        exit;
    }
}

$is_success = ($deposit['status'] === 'success') || isset($_GET['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruksi Pembayaran Deposit | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>

    <section class="checkout-section">
        <div class="container">
            <h1 class="page-title">💸 Selesaikan Pembayaran</h1>
            <p class="page-subtitle">Silakan lakukan transfer sesuai rincian di bawah ini.</p>

            <div class="checkout-container" style="max-width: 650px;">
                
                <?php if($is_success): ?>
                    <div style="text-align: center; padding: 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                        <h2 style="color: var(--primary);">Deposit Berhasil!</h2>
                        <p style="margin-bottom: 1.5rem; color: #6b7280;">Saldo sebesar <strong>Rp <?php echo number_format($deposit['amount'], 0, ',', '.'); ?></strong> telah ditambahkan ke akun Anda.</p>
                        <a href="profile.php" class="btn-primary">Lihat Saldo Saya</a>
                    </div>
                <?php else: ?>
                    <div class="payment-card-instruction" style="background: #f9fafb; border-radius: 16px; padding: 1.5rem; border: 1px solid #e5e7eb; margin-bottom: 2rem;">

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
                            <span>Metode Pembayaran</span>
                            <strong><?php echo htmlspecialchars($deposit['method_name']); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span>Jumlah Transfer</span>
                            <strong style="color: var(--primary); font-size: 1.25rem;">Rp <?php echo number_format($deposit['amount'], 0, ',', '.'); ?></strong>
                        </div>

                        <div style="background: white; border-radius: 12px; padding: 1rem; border: 1px solid #eee;">
                            <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem;">Nomor Tujuan / Scan QR:</p>
                            <div style="font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                                <?php if($deposit['method_type'] === 'Bank Transfer'): ?>
                                    🏦 123-456-7890 (A/N PLAYSHOP.ID)
                                <?php else: ?>
                                    📱 0812-3456-7890 (A/N Irgi)
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="alert info" style="margin-bottom: 2rem;">
                        💡 <strong>Info:</strong> Setelah melakukan transfer, saldo akan otomatis masuk ke akun Anda dalam waktu 1-5 menit.
                    </div>

                    <div style="padding: 1.5rem; background: #fffcf0; border: 1px solid #fde68a; border-radius: 16px;">
                        <h4 style="margin-bottom: 0.5rem; color: #92400e;">Lanjutkan Pembayaran</h4>
                        <p style="font-size: 0.85rem; color: #92400e; margin-bottom: 1rem;">Klik tombol di bawah ini untuk mensimulasikan bahwa Anda telah melakukan pembayaran.</p>
                        <form method="POST">
                            <button type="submit" name="confirm_payment" class="btn-primary" style="width: 100%;">SIMULASI BAYAR SEKARANG</button>
                        </form>
                    </div>

                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="profile.php" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">Batalkan Deposit</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

