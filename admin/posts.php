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
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        $image_path = $_POST['existing_image'] ?? null;
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = upload_image_dummy($_FILES['image'], __DIR__ . '/../uploads/blog');
            if ($upload['ok']) {
                $image_path = 'uploads/blog/' . basename($upload['path']);
            }
        }

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, image_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $content, $image_path]);
            $success = 'Artikel berhasil diterbitkan.';
        } else {
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, content = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $content, $image_path, $id]);
            $success = 'Artikel berhasil diperbarui.';
        }
    }

    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([(int)$_POST['id']]);
        $success = 'Artikel dihapus.';
    }
}

$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Blog | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="content-header">
                <h1>Post & Blog</h1>
                <button onclick="showModal()" class="btn-primary">+ Tulis Artikel</button>
            </div>

            <?php if($success) echo "<div class='alert success'>$success</div>"; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Thumbnail</th>
                            <th>Judul</th>
                            <th>Slug</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($posts as $p): ?>
                        <tr>
                            <td>
                                <?php if(!empty($p['image_path'])): ?>
                                    <img src="<?php echo asset_url($p['image_path']); ?>" alt="thumb" style="height:40px; width:70px; object-fit:cover; border-radius:4px;">
                                <?php else: ?>
                                    <span style="font-size: 1.5rem;">📰</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                            <td><?php echo $p['slug']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
                            <td>
                                <button class="btn-secondary" onclick='showModal(<?php echo json_encode($p); ?>)'>Edit</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
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

    <div id="postModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width: 900px">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Tulis Artikel</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="modalAction" value="add">
                <input type="hidden" name="id" id="postId">
                <input type="hidden" name="existing_image" id="eImg">
                <div class="form-group"><label>Judul Artikel</label><input type="text" name="title" id="pTitle" required></div>
                <div class="form-group"><label>Konten</label><textarea name="content" id="pContent" rows="10"></textarea></div>
                <div class="form-group">
                    <label>Thumbnail</label>
                    <input type="file" name="image" id="postImage" accept="image/*">
                    <img id="postImagePreview" src="" alt="preview" style="display:none; max-width: 200px; margin-top: 10px; border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <button type="submit" class="btn-submit">Selesai & Terbitkan</button>
            </form>
        </div>
    </div>

    <script>
    const postImagePreview = document.getElementById('postImagePreview');
    const postImageInput = document.getElementById('postImage');

    function showModal(data = null) {
        document.getElementById('postModal').style.display = 'flex';
        if (data) {
            document.getElementById('modalTitle').innerText = 'Edit Artikel';
            document.getElementById('modalAction').value = 'update';
            document.getElementById('postId').value = data.id;
            document.getElementById('pTitle').value = data.title;
            document.getElementById('pContent').value = data.content;
            document.getElementById('eImg').value = data.image_path;

            if (data.image_path) {
                let src = data.image_path;
                if (!src.startsWith('http')) {
                    src = '../' + src.replace(/^\//, '');
                }
                postImagePreview.src = src;
                postImagePreview.style.display = 'block';
            } else {
                postImagePreview.src = '';
                postImagePreview.style.display = 'none';
            }
        } else {
            document.getElementById('modalTitle').innerText = 'Tulis Artikel';
            document.getElementById('modalAction').value = 'add';
            document.getElementById('postId').value = '';
            document.getElementById('pTitle').value = '';
            document.getElementById('pContent').value = '';
            postImagePreview.src = '';
            postImagePreview.style.display = 'none';
        }
        postImageInput.value = '';
    }
    
    function closeModal() { document.getElementById('postModal').style.display = 'none'; }

    postImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                postImagePreview.src = e.target.result;
                postImagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html>
