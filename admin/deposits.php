<?php
session_start();
require_once '../config/database.php';
require_once __DIR__ . '/../includes/db_utils.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['deposit_id'] ?? 0);

    try {
        if ($action === 'approve_deposit') {
            $pdo->beginTransaction();
            
            // Check status first to prevent double approval
            $st = $pdo->prepare("SELECT status, user_id, amount FROM deposits WHERE id = ? FOR UPDATE");
            $st->execute([$id]);
            $dep = $st->fetch(PDO::FETCH_ASSOC);
            
            if (!$dep) throw new RuntimeException('Deposit tidak ditemukan.');
            if ($dep['status'] !== 'pending') throw new RuntimeException('Deposit sudah diproses sebelumnya.');

            // 1. Update status
            $stmt = $pdo->prepare("UPDATE deposits SET status = 'success', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$id]);

            // 2. Update user balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([(int)$dep['amount'], (int)$dep['user_id']]);

            $pdo->commit();
            $success = 'Deposit berhasil disetujui. Saldo user telah ditambahkan.';
        } elseif ($action === 'reject_deposit') {
            $stmt = $pdo->prepare("UPDATE deposits SET status = 'failed', updated_at = NOW() WHERE id = ? AND status = 'pending'");
            $stmt->execute([$id]);
            $success = 'Deposit telah ditolak/dibatalkan.';
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Fetch deposits with user and payment method info
$stmt = $pdo->query("
    SELECT d.*, u.name as user_name, u.email as user_email, pm.name as payment_name 
    FROM deposits d
    JOIN users u ON d.user_id = u.id
    LEFT JOIN payment_methods pm ON d.payment_method_id = pm.id
    ORDER BY d.created_at DESC
");
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Deposit | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>💰 Kelola Deposit</h1>
                <p>Verifikasi dan setujui permintaan tambah saldo (deposit) dari pengguna</p>
            </div>

            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deposits)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 2rem;">Belum ada riwayat deposit.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach($deposits as $d): ?>
                        <tr>
                            <td>#DEP-<?php echo (int)$d['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($d['user_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($d['user_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($d['payment_name'] ?? '-'); ?></td>
                            <td><strong>Rp <?php echo number_format($d['amount'], 0, ',', '.'); ?></strong></td>
                            <td>
                                <span class="status-badge <?php 
                                    echo ($d['status'] === 'pending') ? 'pending' : (($d['status'] === 'success') ? 'success' : 'failed'); 
                                ?>">
                                    <?php echo ucfirst($d['status']); ?>
                                </span>
                            </td>
                            <td><small><?php echo date('d/m/Y H:i', strtotime($d['created_at'])); ?></small></td>
                            <td>
                                <?php if ($d['status'] === 'pending'): ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Setujui deposit ini dan tambahkan saldo ke user?')">
                                    <input type="hidden" name="action" value="approve_deposit">
                                    <input type="hidden" name="deposit_id" value="<?php echo (int)$d['id']; ?>">
                                    <button type="submit" class="btn-primary" style="padding: 8px 12px; font-size: 0.85rem;">Setujui</button>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Tolak deposit ini?')">
                                    <input type="hidden" name="action" value="reject_deposit">
                                    <input type="hidden" name="deposit_id" value="<?php echo (int)$d['id']; ?>">
                                    <button type="submit" class="btn-secondary" style="padding: 8px 12px; font-size: 0.85rem; border-color: #ef4444; color: #ef4444;">Tolak</button>
                                </form>
                                <?php else: ?>
                                <span style="font-size: 0.8rem; color: #9ca3af;">Tidak ada aksi</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
