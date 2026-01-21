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

function safe_int($v, $default = 0): int {
    $n = filter_var($v, FILTER_VALIDATE_INT);
    return $n === false ? (int)$default : (int)$n;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'toggle_status') {
            $id = safe_int($_POST['user_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('user_id invalid');
            $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active','banned','active') WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Status user berhasil diubah.';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all users (support both new and legacy transaction schemas)
if (db_has_column($pdo, 'transactions', 'account_user_id')) {
    $stmt = $pdo->query("SELECT u.*,
                         (SELECT COUNT(*) FROM transactions WHERE account_user_id = u.id) as total_transactions,
                         (SELECT SUM(amount) FROM transactions WHERE account_user_id = u.id AND status = 'success') as total_spent
                         FROM users u ORDER BY created_at DESC");
} else {
    $stmt = $pdo->query("SELECT u.*, 
                         (SELECT COUNT(*) FROM transactions WHERE user_id = u.email) as total_transactions,
                         (SELECT SUM(amount) FROM transactions WHERE user_id = u.email AND status = 'success') as total_spent
                         FROM users u ORDER BY created_at DESC");
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Kelola User</h1>
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
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Total Transaksi</th>
                            <th>Total Belanja</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?php echo (int)$u['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo htmlspecialchars($u['phone']); ?></td>
                            <td>
                                <span class="status-badge <?php echo ($u['status'] === 'active') ? 'success' : 'failed'; ?>">
                                    <?php echo ucfirst($u['status']); ?>
                                </span>
                            </td>
                            <td><?php echo (int)($u['total_transactions'] ?? 0); ?>x</td>
                            <td>Rp <?php echo number_format((int)($u['total_spent'] ?? 0), 0, ',', '.'); ?></td>
                            <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Ubah status user ini?')">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                    <button type="submit" class="btn-secondary" style="padding: 8px 12px;">
                                        <?php echo ($u['status'] === 'active') ? 'Ban' : 'Unban'; ?>
                                    </button>
                                </form>
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
