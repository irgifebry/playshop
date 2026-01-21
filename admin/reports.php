<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get date filter
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Export CSV (Excel-compatible)
if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="playshop_report_' . $start_date . '_' . $end_date . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['order_id','game','product','user_id','payment_method','amount','status','created_at']);

    $stmt = $pdo->prepare("SELECT t.order_id, g.name as game_name, p.name as product_name, t.user_id, t.payment_method, t.amount, t.status, t.created_at
                           FROM transactions t
                           JOIN games g ON t.game_id = g.id
                           JOIN products p ON t.product_id = p.id
                           WHERE DATE(t.created_at) BETWEEN ? AND ?
                           ORDER BY t.created_at DESC");
    $stmt->execute([$start_date, $end_date]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, [
            $row['order_id'],
            $row['game_name'],
            $row['product_name'],
            $row['user_id'],
            $row['payment_method'],
            $row['amount'],
            $row['status'],
            $row['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

// Get transaction summary
$stmt = $pdo->prepare("SELECT 
                       COUNT(*) as total_transactions,
                       SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count,
                       SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                       SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                       SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as total_revenue
                       FROM transactions 
                       WHERE DATE(created_at) BETWEEN ? AND ?");
$stmt->execute([$start_date, $end_date]);
$summary = $stmt->fetch(PDO::FETCH_ASSOC);

// Get by game
$stmt = $pdo->prepare("SELECT g.name, COUNT(*) as count, SUM(t.amount) as revenue 
                       FROM transactions t 
                       JOIN games g ON t.game_id = g.id 
                       WHERE t.status = 'success' AND DATE(t.created_at) BETWEEN ? AND ?
                       GROUP BY g.name 
                       ORDER BY revenue DESC");
$stmt->execute([$start_date, $end_date]);
$by_game = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Laporan Transaksi</h1>
                <div style="display:flex; gap: 10px;">
                    <a class="btn-primary" href="?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>&export=csv" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">⬇️ Export CSV</a>
                    <button onclick="window.print()" class="btn-primary">🖨️ Print Laporan</button>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="filter-box">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <button type="submit" class="btn-filter">Filter</button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3><?php echo $summary['total_transactions']; ?></h3>
                        <p>Total Transaksi</p>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <h3><?php echo $summary['success_count']; ?></h3>
                        <p>Berhasil</p>
                    </div>
                </div>

                <div class="stat-card yellow">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo $summary['pending_count']; ?></h3>
                        <p>Pending</p>
                    </div>
                </div>

                <div class="stat-card purple">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3>Rp <?php echo number_format($summary['total_revenue'], 0, ',', '.'); ?></h3>
                        <p>Total Pendapatan</p>
                    </div>
                </div>
            </div>

            <!-- Revenue by Game -->
            <div class="table-container">
                <h2>Pendapatan Per Game</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($by_game as $game): ?>
                        <tr>
                            <td><strong><?php echo $game['name']; ?></strong></td>
                            <td><?php echo $game['count']; ?>x</td>
                            <td>Rp <?php echo number_format($game['revenue'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>