<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle Create/Delete
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['add_product'])) {
        $game_id = $_POST['game_id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        
        $stmt = $pdo->prepare("INSERT INTO products (game_id, name, price) VALUES (?, ?, ?)");
        if($stmt->execute([$game_id, $name, $price])) {
            $success = 'Produk berhasil ditambahkan!';
        }
    }
    
    if(isset($_POST['delete_product'])) {
        $id = $_POST['product_id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if($stmt->execute([$id])) {
            $success = 'Produk berhasil dihapus!';
        }
    }
}

// Get all products with game info
$stmt = $pdo->query("SELECT p.*, g.name as game_name FROM products p JOIN games g ON p.game_id = g.id ORDER BY g.name, p.price");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all games for dropdown
$games = $pdo->query("SELECT * FROM games ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Kelola Produk</h1>
                <button onclick="showAddModal()" class="btn-primary">+ Tambah Produk</button>
            </div>

            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Game</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo $product['game_name']; ?></td>
                            <td><strong><?php echo $product['name']; ?></strong></td>
                            <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus produk ini?')">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="btn-danger-small">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2>Tambah Produk Baru</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Pilih Game</label>
                    <select name="game_id" required>
                        <option value="">-- Pilih Game --</option>
                        <?php foreach($games as $game): ?>
                            <option value="<?php echo $game['id']; ?>"><?php echo $game['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" placeholder="Contoh: 100 Diamond" required>
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="price" placeholder="10000" required>
                </div>
                <button type="submit" name="add_product" class="btn-submit">Tambah Produk</button>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
        }
    </script>
</body>
</html>