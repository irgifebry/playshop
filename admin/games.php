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

function to_public_path(string $absPath): string {
    $root = realpath(__DIR__ . '/..');
    $real = realpath($absPath) ?: $absPath;
    if ($root) {
        $rel = str_replace($root, '', $real);
    } else {
        $rel = $real;
    }
    $rel = str_replace('\\', '/', $rel);
    if (substr($rel, 0, 1) !== '/') $rel = '/' . ltrim($rel, '/');
    return $rel;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '🎮');
            $color_start = trim($_POST['color_start'] ?? '#10b981');
            $color_end = trim($_POST['color_end'] ?? '#059669');
            $min_price = safe_int($_POST['min_price'] ?? 5000, 5000);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $description = trim($_POST['description'] ?? '');
            $how_to_topup = trim($_POST['how_to_topup'] ?? '');
            $faq = trim($_POST['faq'] ?? '');

            if ($name === '') {
                throw new RuntimeException('Nama game wajib diisi');
            }

            $image_path = null;
            if (!empty($_FILES['image']['tmp_name'])) {
                $upload = upload_image_dummy($_FILES['image'], __DIR__ . '/../uploads/games');
                if (!($upload['ok'] ?? false)) {
                    throw new RuntimeException('Upload gambar gagal: ' . ($upload['message'] ?? 'unknown'));
                }
                $image_path = to_public_path((string)$upload['path']);
            }

            $stmt = $pdo->prepare("INSERT INTO games (name, icon, image_path, description, how_to_topup, faq, color_start, color_end, min_price, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $name,
                $icon !== '' ? $icon : '🎮',
                $image_path,
                $description !== '' ? $description : null,
                $how_to_topup !== '' ? $how_to_topup : null,
                $faq !== '' ? $faq : null,
                $color_start,
                $color_end,
                $min_price,
                $is_active
            ]);
            $success = 'Game berhasil ditambahkan!';
        }

        if ($action === 'update') {
            $id = safe_int($_POST['game_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '🎮');
            $color_start = trim($_POST['color_start'] ?? '#10b981');
            $color_end = trim($_POST['color_end'] ?? '#059669');
            $min_price = safe_int($_POST['min_price'] ?? 5000, 5000);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $description = trim($_POST['description'] ?? '');
            $how_to_topup = trim($_POST['how_to_topup'] ?? '');
            $faq = trim($_POST['faq'] ?? '');

            if ($id <= 0) throw new RuntimeException('game_id invalid');
            if ($name === '') throw new RuntimeException('Nama game wajib diisi');

            $stmt = $pdo->prepare("SELECT image_path FROM games WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            $image_path = $existing['image_path'] ?? null;

            if (!empty($_FILES['image']['tmp_name'])) {
                $upload = upload_image_dummy($_FILES['image'], __DIR__ . '/../uploads/games');
                if (!($upload['ok'] ?? false)) {
                    throw new RuntimeException('Upload gambar gagal: ' . ($upload['message'] ?? 'unknown'));
                }
                $image_path = to_public_path((string)$upload['path']);
            }

            $stmt = $pdo->prepare("UPDATE games SET name = ?, icon = ?, image_path = ?, description = ?, how_to_topup = ?, faq = ?, color_start = ?, color_end = ?, min_price = ?, is_active = ? WHERE id = ?");
            $stmt->execute([
                $name,
                $icon !== '' ? $icon : '🎮',
                $image_path,
                $description !== '' ? $description : null,
                $how_to_topup !== '' ? $how_to_topup : null,
                $faq !== '' ? $faq : null,
                $color_start,
                $color_end,
                $min_price,
                $is_active,
                $id
            ]);
            $success = 'Game berhasil diupdate!';
        }

        if ($action === 'delete') {
            $id = safe_int($_POST['game_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('game_id invalid');
            $stmt = $pdo->prepare("DELETE FROM games WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Game berhasil dihapus!';
        }

        if ($action === 'toggle') {
            $id = safe_int($_POST['game_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('game_id invalid');
            $stmt = $pdo->prepare("UPDATE games SET is_active = IF(is_active=1,0,1) WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Status game berhasil diubah!';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$games = $pdo->query("SELECT * FROM games ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Game | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Kelola Game</h1>
                <button onclick="openAddModal()" class="btn-primary">+ Tambah Game</button>
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
                            <th>Nama Game</th>
                            <th>Min. Harga</th>
                            <th>Status</th>
                            <th>Warna</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($games as $game): ?>
                        <tr>
                            <td><?php echo (int)$game['id']; ?></td>
                            <td>
                                <?php if (!empty($game['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($game['image_path']); ?>" alt="game" style="width:48px;height:48px;border-radius:12px;object-fit:cover;border:1px solid #e5e7eb;" />
                                <?php else: ?>
                                    <span style="font-size: 2rem;"><?php echo htmlspecialchars($game['icon']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($game['name']); ?></strong>
                                <div style="color:#6b7280; font-size:0.85rem;">ID: <?php echo (int)$game['id']; ?></div>
                            </td>
                            <td>Rp <?php echo number_format((int)$game['min_price'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="status-badge <?php echo ((int)$game['is_active'] === 1) ? 'success' : 'failed'; ?>">
                                    <?php echo ((int)$game['is_active'] === 1) ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <div style="width: 30px; height: 30px; background: <?php echo htmlspecialchars($game['color_start']); ?>; border-radius: 5px;"></div>
                                    <div style="width: 30px; height: 30px; background: <?php echo htmlspecialchars($game['color_end']); ?>; border-radius: 5px;"></div>
                                </div>
                            </td>
                            <td style="display:flex; gap: 8px;">
                                <button type="button" class="btn-secondary" style="padding: 8px 12px;" onclick='openEditModal(<?php echo json_encode([
                                    "id" => (int)$game["id"],
                                    "name" => (string)$game["name"],
                                    "icon" => (string)$game["icon"],
                                    "color_start" => (string)$game["color_start"],
                                    "color_end" => (string)$game["color_end"],
                                    "min_price" => (int)$game["min_price"],
                                    "is_active" => (int)$game["is_active"],
                                    "description" => (string)($game["description"] ?? ""),
                                    "how_to_topup" => (string)($game["how_to_topup"] ?? ""),
                                    "faq" => (string)($game["faq"] ?? ""),
                                ], JSON_UNESCAPED_UNICODE); ?>)'>Edit</button>

                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="game_id" value="<?php echo (int)$game['id']; ?>">
                                    <button type="submit" class="btn-secondary" style="padding: 8px 12px;">Toggle</button>
                                </form>

                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus game ini? Produk terkait akan ikut terhapus.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="game_id" value="<?php echo (int)$game['id']; ?>">
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

    <div id="gameModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width: 720px;">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Tambah Game</h2>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="modalAction" value="add">
                <input type="hidden" name="game_id" id="game_id" value="">

                <div class="form-group">
                    <label>Nama Game</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin:0;">
                        <label>Icon (Emoji)</label>
                        <input type="text" name="icon" id="icon" value="🎮" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Min. Harga</label>
                        <input type="number" name="min_price" id="min_price" value="5000" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin:0;">
                        <label>Warna Mulai</label>
                        <input type="color" name="color_start" id="color_start" value="#10b981" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Warna Akhir</label>
                        <input type="color" name="color_end" id="color_end" value="#059669" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Gambar Icon (opsional)</label>
                    <input type="file" name="image" accept="image/*">
                    <small style="color:#6b7280;">Jika diupload, halaman user akan menampilkan gambar; kalau tidak, pakai emoji icon.</small>
                </div>

                <div class="form-group">
                    <label><input type="checkbox" name="is_active" id="is_active" checked> Active</label>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat game..."></textarea>
                </div>

                <div class="form-group">
                    <label>Cara Top Up</label>
                    <textarea name="how_to_topup" id="how_to_topup" rows="4" placeholder="Tulis langkah-langkah... (tiap baris = paragraf)"></textarea>
                </div>

                <div class="form-group">
                    <label>FAQ Game</label>
                    <textarea name="faq" id="faq" rows="4" placeholder="Q: ...\nA: ..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Game';
            document.getElementById('modalAction').value = 'add';
            document.getElementById('game_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('icon').value = '🎮';
            document.getElementById('min_price').value = '5000';
            document.getElementById('color_start').value = '#10b981';
            document.getElementById('color_end').value = '#059669';
            document.getElementById('is_active').checked = true;
            document.getElementById('description').value = '';
            document.getElementById('how_to_topup').value = '';
            document.getElementById('faq').value = '';
            document.getElementById('gameModal').style.display = 'flex';
        }

        function openEditModal(game) {
            document.getElementById('modalTitle').textContent = 'Edit Game';
            document.getElementById('modalAction').value = 'update';
            document.getElementById('game_id').value = game.id;
            document.getElementById('name').value = game.name || '';
            document.getElementById('icon').value = game.icon || '🎮';
            document.getElementById('min_price').value = String(game.min_price || 0);
            document.getElementById('color_start').value = game.color_start || '#10b981';
            document.getElementById('color_end').value = game.color_end || '#059669';
            document.getElementById('is_active').checked = (game.is_active === 1);
            document.getElementById('description').value = game.description || '';
            document.getElementById('how_to_topup').value = game.how_to_topup || '';
            document.getElementById('faq').value = game.faq || '';
            document.getElementById('gameModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('gameModal').style.display = 'none';
        }
    </script>
</body>
</html>
