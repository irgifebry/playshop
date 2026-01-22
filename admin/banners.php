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
        $description = trim($_POST['description'] ?? '');
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

                $stmt = $pdo->prepare("INSERT INTO banners (title, description, image_path, link_url, sort_order, is_active, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description ?: null, $relPath, $link_url ?: null, $sort_order, $is_active, $start_date ?: null, $end_date ?: null]);
                $success = 'Banner berhasil ditambahkan.';
            }
        }
    }

    if ($action === 'update') {
        $id = safe_int($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $link_url = trim($_POST['link_url'] ?? '');
        $sort_order = safe_int($_POST['sort_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;

        if ($id <= 0) {
            $error = 'Banner ID tidak valid.';
        } elseif ($title === '') {
            $error = 'Judul banner wajib diisi.';
        } else {
            $stmt = $pdo->prepare("SELECT image_path FROM banners WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            $image_path = $existing['image_path'] ?? null;

            if (!empty($_FILES['image']['tmp_name'])) {
                $upload = upload_image_dummy($_FILES['image'], __DIR__ . '/../uploads/banners');
                if (!($upload['ok'] ?? false)) {
                    $error = 'Upload gambar gagal: ' . ($upload['message'] ?? 'unknown');
                } else {
                    $absPath = (string)$upload['path'];
                    $image_path = str_replace(realpath(__DIR__ . '/..') ?: '', '', realpath($absPath) ?: $absPath);
                    $image_path = str_replace('\\', '/', $image_path);
                    if (substr($image_path, 0, 1) !== '/') $image_path = '/' . ltrim($image_path, '/');
                }
            }

            if (!$error) {
                $stmt = $pdo->prepare("UPDATE banners SET title = ?, description = ?, image_path = ?, link_url = ?, sort_order = ?, is_active = ?, start_date = ?, end_date = ? WHERE id = ?");
                $stmt->execute([$title, $description ?: null, $image_path, $link_url ?: null, $sort_order, $is_active, $start_date ?: null, $end_date ?: null, $id]);
                $success = 'Banner berhasil diupdate.';
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
                                    <?php
                                        $imgSrc = $b['image_path'] ?? '';
                                        if ($imgSrc !== '' && substr($imgSrc, 0, 1) === '/') {
                                            $imgSrc = '..' . $imgSrc; // admin/ berada satu level di bawah root
                                        }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="banner" style="width:120px;height:48px;object-fit:contain;border-radius:8px;border:1px solid #e5e7eb;background-color:#f3f4f6;" />
                                </td>
                                <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars((string)($b['link_url'] ?? '')); ?></td>
                                <td><?php echo (int)$b['sort_order']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo ((int)$b['is_active'] === 1) ? 'success' : 'failed'; ?>">
                                        <?php echo ((int)$b['is_active'] === 1) ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="toggle" />
                                            <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>" />
                                            <button type="submit" class="btn-secondary" style="padding: 8px 12px;">Toggle</button>
                                        </form>
                                        <button type="button" class="btn-secondary" style="padding: 8px 12px;" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($b)); ?>)">Edit</button>
                                        <form method="POST" onsubmit="return confirm('Yakin hapus banner ini?')">
                                            <input type="hidden" name="action" value="delete" />
                                            <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>" />
                                            <button type="submit" class="btn-danger-small">Hapus</button>
                                        </form>
                                    </div>
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
            <h2 id="modalTitle">Tambah Banner</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="modalAction" value="add" />
                <input type="hidden" name="id" id="bannerId" value="" />
                
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" id="bannerTitle" required />
                </div>

                <div class="form-group">
                    <label>Deskripsi (untuk halaman detail)</label>
                    <textarea name="description" id="bannerDescription" rows="4" placeholder="Deskripsi detail promo..."></textarea>
                </div>

                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="image" id="bannerImage" accept="image/*" />
                    <small id="imageNote"></small>
                    <img id="imagePreview" src="" alt="Image preview" style="display:none; max-width: 100%; margin-top: 10px; border-radius: 8px;" />
                </div>

                <div class="form-group">
                    <label>Link (opsional)</label>
                    <input type="text" name="link_url" id="bannerLink" placeholder="https://... atau /promo.php" />
                </div>

                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="sort_order" id="bannerSort" value="0" />
                </div>

                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="is_active" id="bannerActive" checked>
                        <label for="bannerActive">Aktif</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Start Date (opsional)</label>
                    <input type="date" name="start_date" id="bannerStartDate" />
                </div>

                <div class="form-group">
                    <label>End Date (opsional)</label>
                    <input type="date" name="end_date" id="bannerEndDate" />
                </div>

                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        const imagePreview = document.getElementById('imagePreview');
        const bannerImageInput = document.getElementById('bannerImage');

        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Banner';
            document.getElementById('modalAction').value = 'add';
            document.getElementById('bannerId').value = '';
            document.getElementById('bannerTitle').value = '';
            document.getElementById('bannerDescription').value = '';
            document.getElementById('bannerImage').value = '';
            document.getElementById('bannerImage').required = true;
            document.getElementById('imageNote').textContent = '';
            document.getElementById('bannerLink').value = '';
            document.getElementById('bannerSort').value = '0';
            document.getElementById('bannerActive').checked = true;
            document.getElementById('bannerStartDate').value = '';
            document.getElementById('bannerEndDate').value = '';
            
            imagePreview.src = '';
            imagePreview.style.display = 'none';
            
            document.getElementById('addModal').style.display = 'flex';
        }

        function showEditModal(banner) {
            document.getElementById('modalTitle').textContent = 'Edit Banner';
            document.getElementById('modalAction').value = 'update';
            document.getElementById('bannerId').value = banner.id;
            document.getElementById('bannerTitle').value = banner.title || '';
            document.getElementById('bannerDescription').value = banner.description || '';
            document.getElementById('bannerImage').value = '';
            document.getElementById('bannerImage').required = false;
            document.getElementById('imageNote').textContent = 'Kosongkan jika tidak ingin mengubah gambar';
            document.getElementById('bannerLink').value = banner.link_url || '';
            document.getElementById('bannerSort').value = banner.sort_order || '0';
            document.getElementById('bannerActive').checked = banner.is_active === 1;
            document.getElementById('bannerStartDate').value = banner.start_date || '';
            document.getElementById('bannerEndDate').value = banner.end_date || '';
            
            if (banner.image_path) {
                imagePreview.src = banner.image_path;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }

            document.getElementById('addModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
            imagePreview.src = '';
            imagePreview.style.display = 'none';
            bannerImageInput.value = '';
        }

        bannerImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
