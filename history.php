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
    <link rel="stylesheet" href="css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>

    <section class="history-section">
        <div class="container">
            <div id="history-header-anim" style="opacity: 0;">
                <h1 class="page-title">📜 Riwayat Transaksi</h1>
                <p class="page-subtitle">Pantau semua status pesanan dan riwayat top up Anda</p>

                <div class="filter-tabs">
                    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">Semua</a>
                    <a href="?filter=success" class="filter-tab <?php echo $filter === 'success' ? 'active' : ''; ?>">Berhasil</a>
                    <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="?filter=failed" class="filter-tab <?php echo $filter === 'failed' ? 'active' : ''; ?>">Gagal</a>
                </div>

                <div class="history-filter-card">
                    <form method="GET" class="history-filter-form">
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                        <div class="form-group">
                            <label>Dari Tanggal</label>
                            <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="form-group">
                            <label>Sampai Tanggal</label>
                            <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        <div class="form-group">
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
                        <button type="submit" class="btn-primary history-apply-btn">Terapkan</button>
                    </form>
                </div>
            </div>

            <!-- Transactions List -->
            <div id="transaction-anim-container" class="transactions-list">

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

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <style>
    .transactions-list {
        opacity: 0; /* Hidden by default to prevent glitch */
        will-change: transform, opacity;
    }
    .transactions-list.ready {
        opacity: 1;
    }
    .slide-left {
        animation: slideInLeft 0.5s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
    }
    .slide-right {
        animation: slideInRight 0.5s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
    }
    .fade-in-standard {
        animation: simpleFadeIn 0.5s ease-out forwards;
    }
    .fade-in-up-entry {
        animation: pageFadeIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }
    @keyframes slideInLeft {
        from { transform: translateX(20px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideInRight {
        from { transform: translateX(-20px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes simpleFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in-up {
        animation: pageFadeIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }
    @keyframes pageFadeIn {
        0% { opacity: 0; transform: translateY(8px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    
    .filter-tab {
        transition: all 0.3s ease;
    }
    </style>

    <script>
    (function() {
        // Run as fast as possible
        const order = ['all', 'success', 'pending', 'failed'];
        const currentFilter = '<?php echo $filter; ?>';
        const currentIndex = order.indexOf(currentFilter);
        const prevFilter = sessionStorage.getItem('prev_history_filter');
        const prevIndex = order.indexOf(prevFilter);
        
        window.addEventListener('DOMContentLoaded', () => {
            const header = document.getElementById('history-header-anim');
            const list = document.getElementById('transaction-anim-container');
            
            // 1. Instant Scroll Restoration
            const savedScroll = sessionStorage.getItem('history_scroll_pos');
            if (savedScroll !== null) {
                window.scrollTo({ top: parseInt(savedScroll), behavior: 'instant' });
                sessionStorage.removeItem('history_scroll_pos');
            }

            // 2. Determine Animation Logic
            const isFromProfile = document.referrer.includes('profile.php');
            
            if (isFromProfile && (!prevFilter || prevFilter === currentFilter)) {
                // FRESH ENTRY FROM PROFILE
                // Animate BOTH header and list with Fade Up
                header.classList.add('fade-in-up-entry');
                list.classList.add('fade-in-up-entry');
                header.style.opacity = '1';
            } else if (prevFilter && prevFilter !== currentFilter) {
                // CHANGING FILTERS WITHIN PAGE
                // Header stays static, only List slides
                header.style.opacity = '1'; 
                if (currentIndex > prevIndex) {
                    list.classList.add('slide-left');
                } else {
                    list.classList.add('slide-right');
                }
            } else {
                // REFRESH OR DIRECT ENTRY
                header.style.opacity = '1';
                list.classList.add('fade-in-standard');
            }
            
            // Reveal Content
            list.classList.add('ready');
            
            // Update filter memory
            sessionStorage.setItem('prev_history_filter', currentFilter);
        });

        // Save scroll position
        window.addEventListener('beforeunload', () => {
            sessionStorage.setItem('history_scroll_pos', window.scrollY);
        });
    })();
    </script>
</body>
</html>

