<?php
session_start();
require_once '../config/database.php';
require_once __DIR__ . '/../includes/upload.php';

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

    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $link_url = trim($_POST['link_url'] ?? '');
        $sort_order = safe_int($_POST['sort_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;

        if ($title === '') {
            $error = 'Judul banner wajib diisi.';
        } else {
            $upload = upload_image_dummy($_FILES['image'] ?? [], __DIR__ . '/../uploads/banners');
            if (!($upload['ok'] ?? false)) {
                $error = 'Upload gambar gagal: ' . ($upload['message'] ?? 'unknown');
            } else {
                $absPath = (string)$upload['path'];
                $relPath = str_replace(realpath(__DIR__ . '/..') ?: '', '', realpath($absPath) ?: $absPath);
                $relPath = str_replace('\\', '/', $relPath);
                if (substr($relPath, 0, 1) !== '/') $relPath = '/' . ltrim($relPath, '/');

                $stmt = $pdo->prepare("INSERT INTO banners (title, image_path, link_url, sort_order, is_active, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $relPath, $link_url ?: null, $sort_order, $is_active, $start_date ?: null, $end_date ?: null]);
                $success = 'Banner berhasil ditambahkan.';
            }
        }
    }

    if ($action === 'delete') {
        $id = safe_int($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Banner berhasil dihapus.';
        }
    }

    if ($action === 'toggle') {
        $id = safe_int($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE banners SET is_active = IF(is_active=1,0,1) WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Status banner diupdate.';
    }
}

$banners = $pdo->query("SELECT * FROM banners ORDER BY sort_order ASC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Banner | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Kelola Banner/Slider</h1>
                <button onclick="showAddModal()" class="btn-primary">+ Tambah Banner</button>
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
                            <th>Preview</th>
                            <th>Judul</th>
                            <th>Link</th>
                            <th>Urutan</th>
                            <th>Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($banners as $b): ?>
                            <tr>
                                <td><?php echo (int)$b['id']; ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($b['image_path']); ?>" alt="banner" style="width:120px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" />
                                </td>
                                <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars((string)($b['link_url'] ?? '')); ?></td>
                                <td><?php echo (int)$b['sort_order']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo ((int)$b['is_active'] === 1) ? 'success' : 'failed'; ?>">
                                        <?php echo ((int)$b['is_active'] === 1) ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td style="display:flex; gap:8px;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle" />
                                        <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>" />
                                        <button type="submit" class="btn-secondary" style="padding: 8px 12px;">Toggle</button>
                                    </form>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus banner ini?')">
                                        <input type="hidden" name="action" value="delete" />
                                        <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>" />
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

    <div id="addModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2>Tambah Banner</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add" />
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" required />
                </div>
                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="image" accept="image/*" required />
                </div>
                <div class="form-group">
                    <label>Link (opsional)</label>
                    <input type="text" name="link_url" placeholder="https://... atau /promo.php" />
                </div>
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="sort_order" value="0" />
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_active" checked /> Aktif</label>
                </div>
                <div class="form-group">
                    <label>Start Date (opsional)</label>
                    <input type="date" name="start_date" />
                </div>
                <div class="form-group">
                    <label>End Date (opsional)</label>
                    <input type="date" name="end_date" />
                </div>
                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() { document.getElementById('addModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('addModal').style.display = 'none'; }
    </script>
</body>
</html>
