<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'toggle') {
        $pdo->prepare("UPDATE testimonials SET is_shown = CASE WHEN is_shown = 1 THEN 0 ELSE 1 END WHERE id = ?")->execute([$id]);
        $success = 'Status testimoni diubah.';
    }
    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM testimonials WHERE id = ?")->execute([$id]);
        $success = 'Testimoni dihapus.';
    }
}

$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Testimoni | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="content-header">
                <h1>Testimoni Pelanggan</h1>
                <p>Kelola testimoni yang tampil di halaman depan</p>
            </div>

            <?php if($success) echo "<div class='alert success'>$success</div>"; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Tampil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($testimonials as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['name']); ?></td>
                            <td>⭐ <?php echo $t['rating']; ?></td>
                            <td><?php echo htmlspecialchars($t['comment']); ?></td>
                            <td><?php echo $t['is_shown'] ? '✅ Ya' : '❌ Tidak'; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                    <button type="submit" class="btn-secondary"><?php echo $t['is_shown'] ? 'Sembunyikan' : 'Tampilkan'; ?></button>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                    <button type="submit" class="btn-danger-small">Hapus</button>
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
