<?php
session_start();
require_once '../config/database.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../includes/db_utils.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $type = $_POST['type'] ?? 'E-Wallet';
        $fee_flat = (int)($_POST['fee_flat'] ?? 0);
        $fee_percent = (float)($_POST['fee_percent'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($name === '' || $code === '') {
            $error = 'Nama dan Kode wajib diisi.';
        } else {
            $image_path = $_POST['existing_image'] ?? null;
            if (!empty($_FILES['image']['tmp_name'])) {
                $upload = upload_image_dummy($_FILES['image'], __DIR__ . '/../uploads/payments');
                if ($upload['ok']) {
                    if ($image_path) delete_uploaded_file($image_path);
                    $image_path = 'uploads/payments/' . basename($upload['path']);
                }
            }

            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO payment_methods (name, code, type, fee_flat, fee_percent, image_path, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $code, $type, $fee_flat, $fee_percent, $image_path, $is_active]);
                $success = 'Metode pembayaran berhasil ditambahkan.';
            } else {
                $stmt = $pdo->prepare("UPDATE payment_methods SET name = ?, code = ?, type = ?, fee_flat = ?, fee_percent = ?, image_path = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $code, $type, $fee_flat, $fee_percent, $image_path, $is_active, $id]);
                $success = 'Metode pembayaran berhasil diupdate.';
            }
        }
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("SELECT image_path FROM payment_methods WHERE id = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetchColumn();
        if ($img) delete_uploaded_file($img);
        
        $stmt = $pdo->prepare("DELETE FROM payment_methods WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Metode pembayaran dihapus.';
    }
}

$methods = $pdo->query("SELECT * FROM payment_methods ORDER BY type, name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metode Pembayaran | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="content-header">
                <h1>Metode Pembayaran</h1>
                <button onclick="showModal()" class="btn-primary">+ Tambah Metode</button>
            </div>

            <?php if($success) echo "<div class='alert success'>$success</div>"; ?>
            <?php if($error) echo "<div class='alert error'>$error</div>"; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Tipe</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($methods as $m): ?>
                        <tr>
                            <td>
                                <?php if(!empty($m['image_path'])): ?>
                                    <img src="<?php echo asset_url($m['image_path']); ?>" alt="logo" style="height:32px; object-fit:contain;">
                                <?php else: ?>
                                    <span style="font-size: 1.2rem;">💳</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($m['name']); ?></td>
                            <td><?php echo htmlspecialchars($m['code']); ?></td>
                            <td><?php echo $m['type']; ?></td>
                            <td>Rp <?php echo number_format($m['fee_flat'],0); ?> + <?php echo $m['fee_percent']; ?>%</td>
                            <td><span class="status-badge <?php echo $m['is_active'] ? 'success' : 'failed'; ?>"><?php echo $m['is_active'] ? 'Aktif' : 'Non-aktif'; ?></span></td>
                            <td>
                                <button class="btn-secondary" onclick='showModal(<?php echo json_encode($m); ?>)'>Edit</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
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

    <div id="methodModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Tambah Metode</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="modalAction" value="add">
                <input type="hidden" name="id" id="methodId">
                <input type="hidden" name="existing_image" id="existingImage">
                <div class="form-group"><label>Nama</label><input type="text" name="name" id="methodName" required></div>
                <div class="form-group"><label>Kode (Internal)</label><input type="text" name="code" id="methodCode" required></div>
                <div class="form-group">
                    <label>Tipe</label>
                    <select name="type" id="methodType">
                        <option>E-Wallet</option><option>Bank Transfer</option><option>Virtual Account</option><option>Store</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Logo</label>
                    <input type="file" name="image" id="methodImage" accept="image/*">
                    <img id="methodImagePreview" src="" alt="preview" style="display:none; max-width: 100px; margin-top: 10px; border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Fee Tetap (Rp)</label><input type="number" name="fee_flat" id="methodFeeFlat" value="0"></div>
                    <div class="form-group"><label>Fee (%)</label><input type="number" step="0.01" name="fee_percent" id="methodFeePercent" value="0"></div>
                </div>
                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="is_active" id="methodActive" checked>
                        <label for="methodActive">Aktif</label>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <script>
    const methodImagePreview = document.getElementById('methodImagePreview');
    const methodImageInput = document.getElementById('methodImage');

    function showModal(data = null) {
        document.getElementById('methodModal').style.display = 'flex';
        if (data) {
            document.getElementById('modalTitle').innerText = 'Edit Metode';
            document.getElementById('modalAction').value = 'update';
            document.getElementById('methodId').value = data.id;
            document.getElementById('methodName').value = data.name;
            document.getElementById('methodCode').value = data.code;
            document.getElementById('methodType').value = data.type;
            document.getElementById('methodFeeFlat').value = data.fee_flat;
            document.getElementById('methodFeePercent').value = data.fee_percent;
            document.getElementById('methodActive').checked = data.is_active == 1;
            document.getElementById('existingImage').value = data.image_path;

            if (data.image_path) {
                let src = data.image_path;
                if (!src.startsWith('http')) {
                    src = '../' + src.replace(/^\//, '');
                }
                methodImagePreview.src = src;
                methodImagePreview.style.display = 'block';
            } else {
                methodImagePreview.src = '';
                methodImagePreview.style.display = 'none';
            }
        } else {
            document.getElementById('modalTitle').innerText = 'Tambah Metode';
            document.getElementById('modalAction').value = 'add';
            document.getElementById('methodId').value = '';
            document.getElementById('methodName').value = '';
            document.getElementById('methodCode').value = '';
            document.getElementById('methodActive').checked = true;
            methodImagePreview.src = '';
            methodImagePreview.style.display = 'none';
        }
        methodImageInput.value = '';
    }
    
    function closeModal() { document.getElementById('methodModal').style.display = 'none'; }

    methodImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                methodImagePreview.src = e.target.result;
                methodImagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html>
