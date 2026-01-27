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

function safe_int($v, $default = 0): int {
    $n = filter_var($v, FILTER_VALIDATE_INT);
    return $n === false ? (int)$default : (int)$n;
}

function to_public_path(string $absPath): string {
    return public_rel_path_from_abs($absPath);
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '🎮');
            $category = trim($_POST['category'] ?? 'Other');
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

            $stmt = $pdo->prepare("INSERT INTO games (name, icon, image_path, description, how_to_topup, faq, color_start, color_end, min_price, is_active, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
                $category !== '' ? $category : 'Other'
            ]);
            $game_id = $pdo->lastInsertId();
            
            // Add products (nominal/asset) if provided
            $product_names = $_POST['product_names'] ?? [];
            $product_prices = $_POST['product_prices'] ?? [];
            
            if (!empty($product_names)) {
                $stmt = $pdo->prepare("INSERT INTO products (game_id, name, price, is_active) VALUES (?, ?, ?, 1)");
                for ($i = 0; $i < count($product_names); $i++) {
                    $pname = trim($product_names[$i] ?? '');
                    $pprice = safe_int($product_prices[$i] ?? 0);
                    
                    if (!empty($pname) && $pprice > 0) {
                        $stmt->execute([$game_id, $pname, $pprice]);
                    }
                }
            }
            
            $success = 'Game berhasil ditambahkan!';
        }

        if ($action === 'update') {
            $id = safe_int($_POST['game_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '🎮');
            $category = trim($_POST['category'] ?? 'Other');
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
                
                // Delete old file if update success
                $old_image_to_delete = $existing['image_path'] ?? null;
            }

            $stmt = $pdo->prepare("UPDATE games SET name = ?, icon = ?, image_path = ?, description = ?, how_to_topup = ?, faq = ?, color_start = ?, color_end = ?, min_price = ?, is_active = ?, category = ? WHERE id = ?");
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
                $category !== '' ? $category : 'Other',
                $id
            ]);
            
            // If we have an old image to delete, do it now
            if (isset($old_image_to_delete)) {
                delete_uploaded_file($old_image_to_delete);
            }
            
            // Handle products (nominal/asset) update
            if (isset($_POST['products_action'])) {
                // Delete products marked for deletion
                $product_delete_ids = $_POST['product_delete_ids'] ?? [];
                if (!empty($product_delete_ids)) {
                    // product_delete_ids bisa array atau string comma-separated
                    $delete_ids = [];
                    foreach ($product_delete_ids as $pid_str) {
                        $pid = safe_int($pid_str);
                        if ($pid > 0) {
                            $delete_ids[] = $pid;
                        }
                    }
                    
                    if (!empty($delete_ids)) {
                        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND game_id = ?");
                        foreach ($delete_ids as $pid) {
                            $stmt->execute([$pid, $id]);
                        }
                    }
                }
                
                // Update existing products
                $product_ids = $_POST['product_ids'] ?? [];
                $product_names = $_POST['product_names'] ?? [];
                $product_prices = $_POST['product_prices'] ?? [];
                
                $update_stmt = $pdo->prepare("UPDATE products SET name = ?, price = ? WHERE id = ? AND game_id = ?");
                $insert_stmt = $pdo->prepare("INSERT INTO products (game_id, name, price, is_active) VALUES (?, ?, ?, 1)");
                
                for ($i = 0; $i < count($product_names); $i++) {
                    $pname = trim($product_names[$i] ?? '');
                    $pprice = safe_int($product_prices[$i] ?? 0);
                    
                    if (!empty($pname) && $pprice > 0) {
                        $pid = safe_int($product_ids[$i] ?? 0);
                        
                        if ($pid > 0) {
                            $update_stmt->execute([$pname, $pprice, $pid, $id]);
                        } else {
                            $insert_stmt->execute([$id, $pname, $pprice]);
                        }
                    }
                }
            }
            
            $success = 'Game berhasil diupdate!';
        }

        if ($action === 'delete') {
            $id = safe_int($_POST['game_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('game_id invalid');

            // Get image path before delete
            $stmt = $pdo->prepare("SELECT image_path FROM games WHERE id = ?");
            $stmt->execute([$id]);
            $g = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("DELETE FROM games WHERE id = ?");
            if ($stmt->execute([$id]) && !empty($g['image_path'])) {
                delete_uploaded_file($g['image_path']);
            }
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
$products = $pdo->query("SELECT * FROM products ORDER BY game_id, price")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Game | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                                    <img src="<?php echo htmlspecialchars(asset_url($game['image_path'])); ?>" alt="game" style="width:48px;height:48px;border-radius:12px;object-fit:cover;border:1px solid #e5e7eb;" />
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
                                    "products" => array_values(array_filter($products, function($p) use ($game) { return $p["game_id"] == $game["id"]; }))
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
                <input type="hidden" name="products_action" id="products_action" value="0">
                <input type="hidden" id="deleteProductIds" name="product_delete_ids[]" value="">

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
                        <label>Kategori</label>
                        <select name="category" id="category" required>
                            <option value="RPG">RPG</option>
                            <option value="MOBA">MOBA</option>
                            <option value="PC">PC</option>
                            <option value="Action">Action</option>
                            <option value="Sports">Sports</option>
                            <option value="Strategy">Strategy</option>
                            <option value="Casual">Casual</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
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
                    <input type="file" name="image" id="gameImage" accept="image/*">
                    <img id="gameImagePreview" src="" alt="preview" style="display:none; max-width: 100px; margin-top: 10px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <small style="color:#6b7280; display: block; margin-top: 5px;">Jika diupload, halaman user akan menampilkan gambar; kalau tidak, pakai emoji icon.</small>
                </div>

                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="is_active" id="is_active" checked>
                        <label for="is_active">Active</label>
                    </div>
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

                <!-- Section Produk (Nominal/Asset dan Harga) -->
                <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">
                <h3 style="margin-bottom: 15px;">Produk (Nominal & Harga)</h3>
                
                <div id="productsContainer">
                    <!-- Products akan ditambahkan di sini oleh JavaScript -->
                </div>

                <button type="button" class="btn-secondary" style="margin-bottom: 20px;" onclick="addProductRow()">+ Tambah Nominal</button>

                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        let productRowCounter = 0;

        function addProductRow(id = '', name = '', price = '') {
            const container = document.getElementById('productsContainer');
            const rowId = 'product-row-' + productRowCounter++;
            
            const row = document.createElement('div');
            row.id = rowId;
            row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end;';
            
            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.name = 'product_names[]';
            nameInput.placeholder = 'Contoh: 50 Diamond, 100 Gems, etc';
            nameInput.value = name;
            nameInput.style.flex = '1';
            nameInput.style.padding = '8px';
            nameInput.style.border = '1px solid #d1d5db';
            nameInput.style.borderRadius = '6px';
            
            const priceInput = document.createElement('input');
            priceInput.type = 'number';
            priceInput.name = 'product_prices[]';
            priceInput.placeholder = 'Harga (Rp)';
            priceInput.value = price;
            priceInput.min = '0';
            priceInput.style.flex = '1';
            priceInput.style.padding = '8px';
            priceInput.style.border = '1px solid #d1d5db';
            priceInput.style.borderRadius = '6px';
            
            if (id > 0) {
                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = 'product_ids[]';
                hiddenId.value = id;
                row.appendChild(hiddenId);
            } else {
                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = 'product_ids[]';
                hiddenId.value = '0';
                row.appendChild(hiddenId);
            }
            
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.textContent = 'Hapus';
            deleteBtn.style.cssText = 'padding: 8px 12px; background-color: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem;';
            deleteBtn.onclick = function() {
                if (id > 0) {
                    const deleteIds = document.getElementById('deleteProductIds');
                    deleteIds.value += (deleteIds.value ? ',' : '') + id;
                }
                row.remove();
            };
            
            row.appendChild(nameInput);
            row.appendChild(priceInput);
            row.appendChild(deleteBtn);
            container.appendChild(row);
        }

        const gameImagePreview = document.getElementById('gameImagePreview');
        const gameImageInput = document.getElementById('gameImage');

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Game';
            document.getElementById('modalAction').value = 'add';
            document.getElementById('game_id').value = '';
            document.getElementById('products_action').value = '0';
            document.getElementById('name').value = '';
            document.getElementById('icon').value = '🎮';
            document.getElementById('category').value = 'Other';
            document.getElementById('min_price').value = '5000';
            document.getElementById('color_start').value = '#10b981';
            document.getElementById('color_end').value = '#059669';
            document.getElementById('is_active').checked = true;
            document.getElementById('description').value = '';
            document.getElementById('how_to_topup').value = '';
            document.getElementById('faq').value = '';
            document.getElementById('productsContainer').innerHTML = '';
            
            gameImagePreview.src = '';
            gameImagePreview.style.display = 'none';
            gameImageInput.value = '';

            productRowCounter = 0;
            addProductRow();
            addProductRow();
            document.getElementById('gameModal').style.display = 'flex';
        }

        function openEditModal(game) {
            document.getElementById('modalTitle').textContent = 'Edit Game';
            document.getElementById('modalAction').value = 'update';
            document.getElementById('game_id').value = game.id;
            document.getElementById('products_action').value = '1';
            document.getElementById('name').value = game.name || '';
            document.getElementById('icon').value = game.icon || '🎮';
            document.getElementById('category').value = game.category || 'Other';
            document.getElementById('min_price').value = String(game.min_price || 0);
            document.getElementById('color_start').value = game.color_start || '#10b981';
            document.getElementById('color_end').value = game.color_end || '#059669';
            document.getElementById('is_active').checked = (game.is_active === 1);
            document.getElementById('description').value = game.description || '';
            document.getElementById('how_to_topup').value = game.how_to_topup || '';
            document.getElementById('faq').value = game.faq || '';
            
            if (game.image_path) {
                let src = game.image_path;
                if (!src.startsWith('http')) {
                    src = '../' + src.replace(/^\//, '');
                }
                gameImagePreview.src = src;
                gameImagePreview.style.display = 'block';
            } else {
                gameImagePreview.src = '';
                gameImagePreview.style.display = 'none';
            }
            gameImageInput.value = '';
            
            // Clear and load products
            document.getElementById('productsContainer').innerHTML = '<input type="hidden" id="deleteProductIds" name="product_delete_ids[]" value="">';
            productRowCounter = 0;
            
            if (game.products && game.products.length > 0) {
                game.products.forEach(product => {
                    addProductRow(product.id, product.name, product.price);
                });
            } else {
                addProductRow();
                addProductRow();
            }
            
            document.getElementById('gameModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('gameModal').style.display = 'none';
        }

        gameImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    gameImagePreview.src = e.target.result;
                    gameImagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
