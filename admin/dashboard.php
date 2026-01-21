<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Statistik
$stmt = $pdo->query("SELECT COUNT(*) as total FROM transactions WHERE DATE(created_at) = CURDATE()");
$today_transactions = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(amount) as total FROM transactions WHERE DATE(created_at) = CURDATE() AND status = 'success'");
$today_revenue = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM transactions WHERE status = 'pending'");
$pending_transactions = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM games");
$total_games = $stmt->fetch()['total'];

// Transaksi terbaru
$stmt = $pdo->query("SELECT t.*, g.name as game_name, p.name as product_name 
                     FROM transactions t 
                     JOIN games g ON t.game_id = g.id 
                     JOIN products p ON t.product_id = p.id 
                     ORDER BY t.created_at DESC LIMIT 10");
$recent_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1>Dashboard Overview</h1>
                <p>Selamat datang, <?php echo $_SESSION['admin_username']; ?>!</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3><?php echo $today_transactions; ?></h3>
                        <p>Transaksi Hari Ini</p>
                    </div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3>Rp <?php echo number_format($today_revenue, 0, ',', '.'); ?></h3>
                        <p>Pendapatan Hari Ini</p>
                    </div>
                </div>
                <div class="stat-card yellow">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo $pending_transactions; ?></h3>
                        <p>Transaksi Pending</p>
                    </div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon">🎮</div>
                    <div class="stat-info">
                        <h3><?php echo $total_games; ?></h3>
                        <p>Total Game</p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="table-container">
                <h2>Transaksi Terbaru</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Game</th>
                            <th>Produk</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_transactions as $trx): ?>
                        <tr>
                            <td>
                                <a href="transaction-detail.php?order_id=<?php echo urlencode($trx['order_id']); ?>" style="color: inherit; text-decoration: underline;">
                                    <?php echo $trx['order_id']; ?>
                                </a>
                            </td>
                            <td><?php echo $trx['game_name']; ?></td>
                            <td><?php echo $trx['product_name']; ?></td>
                            <td><?php echo $trx['user_id']; ?></td>
                            <td>Rp <?php echo number_format($trx['amount'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="status-badge <?php echo $trx['status']; ?>">
                                    <?php echo ucfirst($trx['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($trx['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>