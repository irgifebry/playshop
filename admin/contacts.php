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
        if ($action === 'close_contact') {
            $id = (int)($_POST['contact_id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE contacts SET status = 'closed' WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Pesan telah ditandai sebagai selesai (closed).';
        } elseif ($action === 'delete_contact') {
            $id = (int)($_POST['contact_id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Pesan telah dihapus.';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>📥 Pesan Masuk (Contacts)</h1>
                <p>Kelola pesan dari formulir Hubungi Kami</p>
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
                            <th>Pengirim</th>
                            <th>Subjek & Pesan</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($contacts)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 2rem;">Belum ada pesan masuk.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach($contacts as $c): ?>
                        <tr>
                            <td><?php echo (int)$c['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($c['name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($c['email']); ?></small>
                            </td>
                            <td style="max-width: 400px;">
                                <div style="font-weight: 700; margin-bottom: 4px;"><?php echo htmlspecialchars($c['subject']); ?></div>
                                <div style="font-size: 0.9rem; color: #4b5563; line-height: 1.4;">
                                    <?php echo nl2br(htmlspecialchars($c['message'])); ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo ($c['status'] === 'new') ? 'pending' : 'success'; ?>">
                                    <?php echo ucfirst($c['status']); ?>
                                </span>
                            </td>
                            <td><small><?php echo date('d/m/Y H:i', strtotime($c['created_at'])); ?></small></td>
                            <td style="white-space: nowrap;">
                                <?php if ($c['status'] === 'new'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="close_contact">
                                    <input type="hidden" name="contact_id" value="<?php echo (int)$c['id']; ?>">
                                    <button type="submit" class="btn-primary" style="padding: 6px 10px; font-size: 0.8rem;">Tandai Selesai</button>
                                </form>
                                <?php endif; ?>
                                
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus pesan ini?')">
                                    <input type="hidden" name="action" value="delete_contact">
                                    <input type="hidden" name="contact_id" value="<?php echo (int)$c['id']; ?>">
                                    <button type="submit" class="btn-secondary" style="padding: 6px 10px; font-size: 0.8rem; border-color: #ef4444; color: #ef4444;">Hapus</button>
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
