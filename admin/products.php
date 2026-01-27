<?php
session_start();
require_once '../config/database.php';

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

function safe_nullable_int($v): ?int {
    if ($v === null) return null;
    $s = trim((string)$v);
    if ($s === '') return null;
    $n = filter_var($s, FILTER_VALIDATE_INT);
    return $n === false ? null : (int)$n;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            $game_id = safe_int($_POST['game_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $price = safe_int($_POST['price'] ?? 0);
            $stock = safe_nullable_int($_POST['stock'] ?? null);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if ($game_id <= 0) throw new RuntimeException('Game wajib dipilih');
            if ($name === '') throw new RuntimeException('Nama produk wajib diisi');
            if ($price <= 0) throw new RuntimeException('Harga harus > 0');

            $stmt = $pdo->prepare("INSERT INTO products (game_id, name, price, stock, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$game_id, $name, $price, $stock, $is_active]);
            $success = 'Produk berhasil ditambahkan!';
        }

        if ($action === 'update') {
            $id = safe_int($_POST['product_id'] ?? 0);
            $game_id = safe_int($_POST['game_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $price = safe_int($_POST['price'] ?? 0);
            $stock = safe_nullable_int($_POST['stock'] ?? null);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if ($id <= 0) throw new RuntimeException('product_id invalid');
            if ($game_id <= 0) throw new RuntimeException('Game wajib dipilih');
            if ($name === '') throw new RuntimeException('Nama produk wajib diisi');
            if ($price <= 0) throw new RuntimeException('Harga harus > 0');

            $stmt = $pdo->prepare("UPDATE products SET game_id = ?, name = ?, price = ?, stock = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$game_id, $name, $price, $stock, $is_active, $id]);
            $success = 'Produk berhasil diupdate!';
        }

        if ($action === 'delete') {
            $id = safe_int($_POST['product_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('product_id invalid');
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Produk berhasil dihapus!';
        }

        if ($action === 'toggle') {
            $id = safe_int($_POST['product_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('product_id invalid');
            $stmt = $pdo->prepare("UPDATE products SET is_active = IF(is_active=1,0,1) WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Status produk berhasil diubah!';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$products = $pdo->query("SELECT p.*, g.name as game_name FROM products p JOIN games g ON p.game_id = g.id ORDER BY g.name, p.price")->fetchAll(PDO::FETCH_ASSOC);
$games = $pdo->query("SELECT id, name FROM games ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Kelola Produk</h1>
                <button onclick="openAddModal()" class="btn-primary">+ Tambah Produk</button>
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
                            <th>Game</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td><?php echo (int)$p['id']; ?></td>
                            <td><?php echo htmlspecialchars($p['game_name']); ?></td>
                            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                            <td>Rp <?php echo number_format((int)$p['price'], 0, ',', '.'); ?></td>
                            <td><?php echo ($p['stock'] === null) ? 'Unlimited' : (int)$p['stock']; ?></td>
                            <td>
                                <span class="status-badge <?php echo ((int)$p['is_active'] === 1) ? 'success' : 'failed'; ?>">
                                    <?php echo ((int)$p['is_active'] === 1) ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td style="display:flex; gap: 8px;">
                                <button type="button" class="btn-secondary" style="padding: 8px 12px;" onclick='openEditModal(<?php echo json_encode([
                                    "id" => (int)$p["id"],
                                    "game_id" => (int)$p["game_id"],
                                    "name" => (string)$p["name"],
                                    "price" => (int)$p["price"],
                                    "stock" => $p["stock"],
                                    "is_active" => (int)$p["is_active"],
                                ], JSON_UNESCAPED_UNICODE); ?>)'>Edit</button>

                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
                                    <button type="submit" class="btn-secondary" style="padding: 8px 12px;">Toggle</button>
                                </form>

                                <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus produk ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
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

    <div id="productModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Tambah Produk</h2>

            <form method="POST">
                <input type="hidden" name="action" id="modalAction" value="add">
                <input type="hidden" name="product_id" id="product_id" value="">

                <div class="form-group">
                    <label>Game</label>
                    <select name="game_id" id="game_id" required>
                        <option value="">-- Pilih Game --</option>
                        <?php foreach($games as $g): ?>
                            <option value="<?php echo (int)$g['id']; ?>"><?php echo htmlspecialchars($g['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" id="name" placeholder="Contoh: 100 Diamond" required>
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin:0;">
                        <label>Harga (Rp)</label>
                        <input type="number" name="price" id="price" placeholder="10000" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Stock</label>
                        <input type="number" name="stock" id="stock" placeholder="Kosong = Unlimited">
                        <small style="color:#6b7280;">Jika kosong, dianggap Unlimited.</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="is_active" id="is_active" checked>
                        <label for="is_active">Active</label>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Produk';
            document.getElementById('modalAction').value = 'add';
            document.getElementById('product_id').value = '';
            document.getElementById('game_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('price').value = '';
            document.getElementById('stock').value = '';
            document.getElementById('is_active').checked = true;
            document.getElementById('productModal').style.display = 'flex';
        }

        function openEditModal(p) {
            document.getElementById('modalTitle').textContent = 'Edit Produk';
            document.getElementById('modalAction').value = 'update';
            document.getElementById('product_id').value = p.id;
            document.getElementById('game_id').value = String(p.game_id);
            document.getElementById('name').value = p.name || '';
            document.getElementById('price').value = String(p.price || 0);
            document.getElementById('stock').value = (p.stock === null) ? '' : String(p.stock);
            document.getElementById('is_active').checked = (p.is_active === 1);
            document.getElementById('productModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }
    </script>
</body>
</html>
