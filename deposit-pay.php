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

// Konfirmasi pembayaran dikirim oleh user
if (isset($_POST['confirm_payment'])) {
    if ($deposit['status'] === 'pending') {
        // Hanya tandai bahwa user sudah konfirmasi (opsional: tambah kolom payment_at atau biarkan status pending)
        // Untuk sekarang kita biarkan status 'pending' agar Admin tahu harus diproses.
        // Kita gunakan session atau redirect flag untuk memberikan feedback.
        header("Location: deposit-pay.php?id=$deposit_id&submitted=1");
        exit;
    }
}

$is_submitted = isset($_GET['submitted']);
$is_success = ($deposit['status'] === 'success');
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
                    <div style="text-align: center; padding: 3rem 2rem;">
                        <div style="font-size: 4.5rem; margin-bottom: 1.5rem; filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.2));">✅</div>
                        <h2 style="color: var(--primary); font-size: 1.8rem; margin-bottom: 0.5rem;">Deposit Berhasil!</h2>
                        <p style="margin-bottom: 2rem; color: #6b7280; font-size: 1.1rem;">Saldo sebesar <strong>Rp <?php echo number_format($deposit['amount'], 0, ',', '.'); ?></strong> telah ditambahkan ke akun Anda.</p>
                        <a href="profile.php" class="btn-primary" style="padding: 1rem 2.5rem; text-decoration: none; border-radius: 12px; font-weight: 700;">Lihat Saldo Saya</a>
                    </div>
                <?php elseif($is_submitted || $deposit['status'] === 'pending' && isset($_GET['id'])): ?>
                    <!-- Intruction Card -->
                    <div class="payment-card-instruction" style="background: white; border-radius: 20px; padding: 2rem; border: 1px solid #e5e7eb; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 2rem;">

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 1.25rem;">
                            <span style="color: #6b7280; font-weight: 500;">Metode Pembayaran</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <?php if($deposit['method_image']): ?>
                                    <img src="<?php echo asset_url($deposit['method_image']); ?>" style="height: 20px;">
                                <?php endif; ?>
                                <strong style="font-size: 1.05rem;"><?php echo htmlspecialchars($deposit['method_name']); ?></strong>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <span style="color: #6b7280; font-weight: 500;">Jumlah Transfer</span>
                            <div style="text-align: right;">
                                <strong style="color: var(--primary); font-size: 1.5rem; display: block;">Rp <?php echo number_format($deposit['amount'], 0, ',', '.'); ?></strong>
                                <small style="color: #9ca3af; font-size: 0.75rem;">Harap transfer hingga digit terakhir</small>
                            </div>
                        </div>

                        <div style="background: #fdf2f2; border-radius: 14px; padding: 1.25rem; border: 1px dashed #f87171; text-align: center;">
                            <?php if($deposit['method_code'] === 'QRIS'): ?>
                                <p style="font-size: 0.9rem; color: #b91c1c; font-weight: 600; margin-bottom: 1rem;">Scan QRIS di bawah ini:</p>
                                <img src="uploads/qris_dummy.png" alt="QRIS DUMMY" style="max-width: 250px; width: 100%; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: 0 auto; display: block;">
                                <p style="font-size: 0.75rem; color: #6b7280; margin-top: 1rem;">A/N PLAYSHOP.ID</p>
                            <?php else: ?>
                                <p style="font-size: 0.85rem; color: #b91c1c; font-weight: 600; margin-bottom: 0.5rem;">Nomor Rekening / Tujuan:</p>
                                <div style="font-weight: 800; font-size: 1.3rem; color: #1f2937; margin-bottom: 0.25rem;">
                                    <?php if($deposit['method_type'] === 'Bank Transfer'): ?>
                                        123-456-7890
                                    <?php else: ?>
                                        0812-3456-7890
                                    <?php endif; ?>
                                </div>
                                <p style="font-size: 0.9rem; color: #4b5563;">A/N PLAYSHOP.ID</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($is_submitted): ?>
                        <div style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 16px; padding: 1.5rem; text-align: center; border-left: 5px solid #0284c7;">
                            <h3 style="color: #0369a1; margin-bottom: 0.5rem; font-size: 1.15rem;">⏳ Sedang Menunggu Verifikasi</h3>
                            <p style="color: #075985; font-size: 0.9rem; line-height: 1.5;">
                                Pembayaran Anda telah kami terima sistem. Admin kami akan melakukan verifikasi manual dalam waktu <strong>1-10 menit</strong>. Saldo akan bertambah otomatis setelah disetujui.
                            </p>
                            <div style="margin-top: 1.25rem;">
                                <a href="profile.php" class="btn-primary" style="display: inline-block; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 10px; font-size: 0.9rem;">Kembali ke Profil</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert info" style="margin-bottom: 1.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 12px; padding: 1rem;">
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="font-size: 1.25rem;">💡</span>
                                <p style="font-size: 0.85rem; line-height: 1.4;">Setelah mentransfer, silakan klik tombol konfirmasi di bawah. Saldo tidak akan langsung bertambah sebelum Admin menyetujui bukti transfer Anda.</p>
                            </div>
                        </div>

                        <div style="padding: 1.5rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 20px; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.05);">
                            <h4 style="margin-bottom: 0.75rem; color: #92400e;">Konfirmasi Pembayaran</h4>
                            <p style="font-size: 0.85rem; color: #b45309; margin-bottom: 1.25rem;">Apakah Anda sudah melakukan transfer sesuai nominal di atas?</p>
                            <form method="POST">
                                <button type="submit" name="confirm_payment" class="btn-primary" style="width: 100%; padding: 1rem; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer;">SAYA SUDAH BAYAR</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="profile.php" style="color: #9ca3af; text-decoration: none; font-size: 0.9rem; transition: color 0.2s;" onmouseover="this.style.color='#6b7280'" onmouseout="this.style.color='#9ca3af'">Batalkan Deposit</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

