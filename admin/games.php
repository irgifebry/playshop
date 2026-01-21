<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle Create/Update/Delete
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['add_game'])) {
        $name = $_POST['name'];
        $icon = $_POST['icon'];
        $color_start = $_POST['color_start'];
        $color_end = $_POST['color_end'];
        $min_price = $_POST['min_price'];
        
        $stmt = $pdo->prepare("INSERT INTO games (name, icon, color_start, color_end, min_price) VALUES (?, ?, ?, ?, ?)");
        if($stmt->execute([$name, $icon, $color_start, $color_end, $min_price])) {
            $success = 'Game berhasil ditambahkan!';
        }
    }
    
    if(isset($_POST['delete_game'])) {
        $id = $_POST['game_id'];
        $stmt = $pdo->prepare("DELETE FROM games WHERE id = ?");
        if($stmt->execute([$id])) {
            $success = 'Game berhasil dihapus!';
        }
    }
}

// Get all games
$stmt = $pdo->query("SELECT * FROM games ORDER BY name");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <button onclick="showAddModal()" class="btn-primary">+ Tambah Game</button>
            </div>

            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Icon</th>
                            <th>Nama Game</th>
                            <th>Min. Harga</th>
                            <th>Warna</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($games as $game): ?>
                        <tr>
                            <td><?php echo $game['id']; ?></td>
                            <td><span style="font-size: 2rem;"><?php echo $game['icon']; ?></span></td>
                            <td><strong><?php echo $game['name']; ?></strong></td>
                            <td>Rp <?php echo number_format($game['min_price'], 0, ',', '.'); ?></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <div style="width: 30px; height: 30px; background: <?php echo $game['color_start']; ?>; border-radius: 5px;"></div>
                                    <div style="width: 30px; height: 30px; background: <?php echo $game['color_end']; ?>; border-radius: 5px;"></div>
                                </div>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus game ini?')">
                                    <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                                    <button type="submit" name="delete_game" class="btn-danger-small">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Game Modal -->
    <div id="addModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2>Tambah Game Baru</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Game</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Icon (Emoji)</label>
                    <input type="text" name="icon" value="🎮" required>
                </div>
                <div class="form-group">
                    <label>Warna Mulai (Hex)</label>
                    <input type="color" name="color_start" value="#10b981" required>
                </div>
                <div class="form-group">
                    <label>Warna Akhir (Hex)</label>
                    <input type="color" name="color_end" value="#059669" required>
                </div>
                <div class="form-group">
                    <label>Harga Minimum</label>
                    <input type="number" name="min_price" value="5000" required>
                </div>
                <button type="submit" name="add_game" class="btn-submit">Tambah Game</button>
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