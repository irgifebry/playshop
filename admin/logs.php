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
    try {
        if ($action === 'clear_logs') {
            $stmt = $pdo->query("DELETE FROM notifications_log");
            $success = 'Semua log telah dihapus.';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM notifications_log ORDER BY created_at DESC LIMIT 500");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Sistem | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>📜 Log Sistem (Notifications)</h1>
                <div style="display:flex; gap: 10px;">
                    <form method="POST" onsubmit="return confirm('Hapus semua log?')">
                        <input type="hidden" name="action" value="clear_logs">
                        <button type="submit" class="btn-secondary" style="border-color: #ef4444; color: #ef4444;">Hapus Semua Log</button>
                    </form>
                </div>
            </div>

            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Aktivitas / Pesan</th>
                            <th style="width: 200px;">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding: 2rem;">Belum ada log aktivitas.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach($logs as $l): ?>
                        <tr>
                            <td>#<?php echo (int)$l['id']; ?></td>
                            <td>
                                <div style="font-family: 'Courier New', Courier, monospace; font-size: 0.9rem; border-left: 3px solid #10b981; padding-left: 10px;">
                                    <?php echo htmlspecialchars($l['message']); ?>
                                </div>
                            </td>
                            <td><small><?php echo date('d/m/Y H:i:s', strtotime($l['created_at'])); ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
