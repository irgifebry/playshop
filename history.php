<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Use account_user_id if schema supports it, fallback to legacy (email stored in transactions.user_id)
$account_user_id = (int)$user_id;
$account_email = $_SESSION['user_email'] ?? null;
if (!$account_email) {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    $account_email = $u['email'] ?? null;
}

// Get all transactions
$filter = $_GET['filter'] ?? 'all';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$game_filter = $_GET['game_id'] ?? '';

$query = "SELECT t.*, g.name as game_name, p.name as product_name 
          FROM transactions t 
          JOIN games g ON t.game_id = g.id 
          JOIN products p ON t.product_id = p.id 
          WHERE ";

$params = [];
if (db_has_column($pdo, 'transactions', 'account_user_id')) {
    $query .= " t.account_user_id = ? ";
    $params[] = $account_user_id;
} else {
    $query .= " t.user_id = ? ";
    $params[] = $account_email;
}

if($filter !== 'all') {
    $query .= " AND t.status = ?";
    $params[] = $filter;
}

$dateClause = '';
if ($start_date !== '' && $end_date !== '') {
    $dateClause = " AND DATE(t.created_at) BETWEEN ? AND ? ";
    $params[] = $start_date;
    $params[] = $end_date;
} elseif ($start_date !== '') {
    $dateClause = " AND DATE(t.created_at) >= ? ";
    $params[] = $start_date;
} elseif ($end_date !== '') {
    $dateClause = " AND DATE(t.created_at) <= ? ";
    $params[] = $end_date;
}
$query .= $dateClause;

if ($game_filter !== '') {
    $query .= " AND t.game_id = ? ";
    $params[] = (int)$game_filter;
}

$query .= " ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dropdown games
$games = $pdo->query("SELECT id, name FROM games ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <a href="index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                        <span class="logo-icon">🎮</span>
                        <span class="logo-text">PLAYSHOP<span class="highlight">.ID</span></span>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="history.php" class="active">Riwayat</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="history-section">
        <div class="container">
            <h1 class="page-title">Riwayat Transaksi</h1>

            <!-- Filter -->
            <div class="filter-tabs">
                <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">Semua</a>
                <a href="?filter=success" class="filter-tab <?php echo $filter === 'success' ? 'active' : ''; ?>">Berhasil</a>
                <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                <a href="?filter=failed" class="filter-tab <?php echo $filter === 'failed' ? 'active' : ''; ?>">Gagal</a>
            </div>

            <div style="background: #ffffff; padding: 1rem; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.06); margin-bottom: 1.5rem;">
                <form method="GET" class="filter-form" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 12px; align-items: end;">
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <div class="form-group" style="margin: 0;">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>Game</label>
                        <select name="game_id">
                            <option value="">Semua Game</option>
                            <?php foreach($games as $g): ?>
                                <option value="<?php echo $g['id']; ?>" <?php echo ((string)$game_filter === (string)$g['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($g['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-submit" style="margin: 0;">Terapkan</button>
                </form>
            </div>

            <!-- Transactions List -->
            <div class="transactions-list">
                <?php if(count($transactions) > 0): ?>
                    <?php foreach($transactions as $trx): ?>
                    <div class="transaction-card">
                        <div class="trx-header">
                            <div>
                                <h3><?php echo $trx['game_name']; ?></h3>
                                <p class="trx-date"><?php echo date('d M Y, H:i', strtotime($trx['created_at'])); ?></p>
                            </div>
                            <span class="status-badge <?php echo $trx['status']; ?>">
                                <?php 
                                $status_text = [
                                    'success' => 'Berhasil',
                                    'pending' => 'Pending',
                                    'failed' => 'Gagal'
                                ];
                                echo $status_text[$trx['status']];
                                ?>
                            </span>
                        </div>
                        
                        <div class="trx-body">
                            <div class="trx-row">
                                <span>Order ID</span>
                                <strong><?php echo $trx['order_id']; ?></strong>
                            </div>
                            <div class="trx-row">
                                <span>Produk</span>
                                <strong><?php echo $trx['product_name']; ?></strong>
                            </div>
                            <div class="trx-row">
                                <span>User ID</span>
                                <strong>
                                    <?php
                                    // Prefer new column if exists, otherwise legacy user_id is already game user id
                                    echo htmlspecialchars($trx['game_user_id'] ?? $trx['user_id']);
                                    ?>
                                </strong>
                            </div>
                            <div class="trx-row">
                                <span>Pembayaran</span>
                                <strong><?php echo $trx['payment_method']; ?></strong>
                            </div>
                            <div class="trx-row total">
                                <span>Total</span>
                                <strong>Rp <?php echo number_format($trx['amount'], 0, ',', '.'); ?></strong>
                            </div>
                        </div>
                        
                        <?php if($trx['status'] === 'success'): ?>
                        <div class="trx-footer">
                            <p class="success-msg">✅ Diamond/UC sudah masuk ke akun game Anda</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📦</div>
                        <h3>Belum Ada Transaksi</h3>
                        <p>Anda belum melakukan transaksi apapun</p>
                        <a href="index.php" class="btn-primary">Mulai Top Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 PLAYSHOP.ID - Transaksi Cepat & Aman</p>
        </div>
    </footer>
</body>
</html>