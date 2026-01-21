<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['add_voucher'])) {
        $code = strtoupper($_POST['code']);
        $type = $_POST['type'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];
        $expired_date = $_POST['expired_date'];
        
        $stmt = $pdo->prepare("INSERT INTO vouchers (code, type, amount, description, expired_date, status) VALUES (?, ?, ?, ?, ?, 'active')");
        if($stmt->execute([$code, $type, $amount, $description, $expired_date])) {
            $success = 'Voucher berhasil ditambahkan!';
        }
    }
    
    if(isset($_POST['delete_voucher'])) {
        $id = $_POST['voucher_id'];
        $stmt = $pdo->prepare("DELETE FROM vouchers WHERE id = ?");
        if($stmt->execute([$id])) {
            $success = 'Voucher berhasil dihapus!';
        }
    }
}

// Get all vouchers
$stmt = $pdo->query("SELECT * FROM vouchers ORDER BY created_at DESC");
$vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Diskon | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Kelola Diskon & Voucher</h1>
                <button onclick="showAddModal()" class="btn-primary">+ Tambah Voucher</button>
            </div>

            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Deskripsi</th>
                            <th>Expired</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($vouchers as $voucher): ?>
                        <tr>
                            <td><strong><?php echo $voucher['code']; ?></strong></td>
                            <td><?php echo ucfirst($voucher['type']); ?></td>
                            <td>
                                <?php 
                                if($voucher['type'] === 'percentage') {
                                    echo $voucher['amount'] . '%';
                                } else {
                                    echo 'Rp ' . number_format($voucher['amount'], 0, ',', '.');
                                }
                                ?>
                            </td>
                            <td><?php echo $voucher['description']; ?></td>
                            <td><?php echo date('d M Y', strtotime($voucher['expired_date'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $voucher['status']; ?>">
                                    <?php echo ucfirst($voucher['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus voucher ini?')">
                                    <input type="hidden" name="voucher_id" value="<?php echo $voucher['id']; ?>">
                                    <button type="submit" name="delete_voucher" class="btn-danger-small">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Voucher Modal -->
    <div id="addModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2>Tambah Voucher Baru</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Kode Voucher</label>
                    <input type="text" name="code" placeholder="Contoh: PLAYSHOP20" required>
                </div>
                <div class="form-group">
                    <label>Tipe Diskon</label>
                    <select name="type" required>
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nominal (Rp)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nilai Diskon</label>
                    <input type="number" name="amount" placeholder="20" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <input type="text" name="description" placeholder="Diskon untuk semua game" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Expired</label>
                    <input type="date" name="expired_date" required>
                </div>
                <button type="submit" name="add_voucher" class="btn-submit">Tambah Voucher</button>
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