<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? '';
if ($order_id === '') {
    header('Location: dashboard.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = $_POST['status'] ?? '';
    if (in_array($newStatus, ['pending','success','failed'], true)) {
        // Get current status to check if it's changing to success
        $stmt = $pdo->prepare("SELECT status, product_id, voucher_code, discount_amount FROM transactions WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $currentTrx = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Update status
        $stmt = $pdo->prepare("UPDATE transactions SET status = ?, updated_at = NOW() WHERE order_id = ?");
        $stmt->execute([$newStatus, $order_id]);
        
        // If status is being changed TO success (from non-success), process stock and voucher
        if ($newStatus === 'success' && $currentTrx && $currentTrx['status'] !== 'success') {
            // Decrement stock
            try {
                $stmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = ? AND stock IS NOT NULL AND stock > 0");
                $stmt->execute([(int)$currentTrx['product_id']]);
            } catch (Exception $e) {}
            
            // Update voucher usage counter
            try {
                $voucherCode = strtoupper(trim((string)($currentTrx['voucher_code'] ?? '')));
                $discountAmount = (int)($currentTrx['discount_amount'] ?? 0);
                if ($voucherCode !== '' && $discountAmount > 0) {
                    $stmt = $pdo->prepare("UPDATE vouchers SET used_count = used_count + 1 WHERE code = ?");
                    $stmt->execute([$voucherCode]);
                }
            } catch (Exception $e) {}
            
            // Log to notifications
            try {
                $stmt = $pdo->prepare("SELECT g.name as game_name, p.name as product_name, t.amount, t.game_user_id 
                                       FROM transactions t 
                                       JOIN games g ON t.game_id = g.id 
                                       JOIN products p ON t.product_id = p.id 
                                       WHERE t.order_id = ?");
                $stmt->execute([$order_id]);
                $trxInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $logMessage = sprintf(
                    "[ADMIN KONFIRMASI SUKSES] Order ID: %s | Game: %s | Produk: %s | Total: Rp %s | User ID: %s",
                    $order_id,
                    $trxInfo['game_name'],
                    $trxInfo['product_name'],
                    number_format((int)$trxInfo['amount'], 0, ',', '.'),
                    $trxInfo['game_user_id']
                );
                $stmt = $pdo->prepare("INSERT INTO notifications_log (message, created_at) VALUES (?, NOW())");
                $stmt->execute([$logMessage]);
            } catch (Exception $e) {}
        }
        
        $success = 'Status transaksi berhasil diupdate menjadi ' . ucfirst($newStatus) . '.';
    } else {
        $error = 'Status tidak valid.';
    }
}

$stmt = $pdo->prepare("SELECT t.*, g.name as game_name, p.name as product_name
                       FROM transactions t
                       JOIN games g ON t.game_id = g.id
                       JOIN products p ON t.product_id = p.id
                       WHERE t.order_id = ? LIMIT 1");
$stmt->execute([$order_id]);
$trx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trx) {
    header('Location: dashboard.php');
    exit;
}

$gameUserId = $trx['game_user_id'];
$gameZoneId = $trx['game_zone_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Detail Transaksi</h1>
                <div style="display:flex; gap: 10px;">
                    <a class="btn-secondary" href="dashboard.php" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">← Kembali</a>
                    <button onclick="window.print()" class="btn-primary">🖨️ Print</button>
                </div>
            </div>

            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <h2>Ringkasan</h2>
                <table class="detail-table">
                    <tr>
                        <td>Order ID</td>
                        <td><strong><?php echo htmlspecialchars($trx['order_id']); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Game</td>
                        <td><?php echo htmlspecialchars($trx['game_name']); ?></td>
                    </tr>
                    <tr>
                        <td>Produk</td>
                        <td><?php echo htmlspecialchars($trx['product_name']); ?></td>
                    </tr>
                    <tr>
                        <td>User ID (Game)</td>
                        <td><?php echo htmlspecialchars((string)$gameUserId); ?><?php echo $gameZoneId ? ' (' . htmlspecialchars((string)$gameZoneId) . ')' : ''; ?></td>
                    </tr>
                    <tr>
                        <td>Account Email</td>
                        <td><?php echo htmlspecialchars((string)($trx['account_email'] ?? '')); ?></td>
                    </tr>
                    <tr>
                        <td>Metode Pembayaran</td>
                        <td><?php echo htmlspecialchars($trx['payment_method']); ?></td>
                    </tr>
                    <tr>
                        <td>Subtotal</td>
                        <td>Rp <?php echo number_format((int)($trx['subtotal'] ?? 0), 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Diskon</td>
                        <td>Rp <?php echo number_format((int)($trx['discount_amount'] ?? 0), 0, ',', '.'); ?> (<?php echo htmlspecialchars((string)($trx['voucher_code'] ?? '')); ?>)</td>
                    </tr>
                    <tr>
                        <td>Biaya Admin</td>
                        <td>Rp <?php echo number_format((int)($trx['admin_fee'] ?? 0), 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td><strong>Rp <?php echo number_format((int)$trx['amount'], 0, ',', '.'); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>
                            <span class="status-badge <?php echo htmlspecialchars($trx['status']); ?>">
                                <?php echo ucfirst($trx['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Created At</td>
                        <td><?php echo date('d/m/Y H:i', strtotime($trx['created_at'])); ?></td>
                    </tr>
                </table>

                <h2 style="margin-top: 24px;">Update Status</h2>
                <form method="POST" class="trx-status-form">
                    <select name="status" required class="trx-status-select">
                        <option value="pending" <?php echo $trx['status']==='pending'?'selected':''; ?>>Pending</option>
                        <option value="success" <?php echo $trx['status']==='success'?'selected':''; ?>>Success</option>
                        <option value="failed" <?php echo $trx['status']==='failed'?'selected':''; ?>>Failed</option>
                    </select>
                    <button type="submit" class="btn-submit trx-status-submit" style="margin:0;">Simpan</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
