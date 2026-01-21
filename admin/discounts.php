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
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $type = $_POST['type'] ?? 'percentage';
            $amount = safe_int($_POST['amount'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $expired_date = $_POST['expired_date'] ?? null;
            $usage_limit = safe_nullable_int($_POST['usage_limit'] ?? null);
            $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

            if ($code === '') throw new RuntimeException('Kode voucher wajib diisi');
            if (!in_array($type, ['percentage','fixed'], true)) throw new RuntimeException('Tipe tidak valid');
            if ($amount < 0) throw new RuntimeException('Nilai diskon tidak valid');
            if ($description === '') throw new RuntimeException('Deskripsi wajib diisi');

            $stmt = $pdo->prepare("INSERT INTO vouchers (code, type, amount, description, expired_date, status, usage_limit, used_count) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
            $stmt->execute([$code, $type, $amount, $description, $expired_date ?: null, $status, $usage_limit]);
            $success = 'Voucher berhasil ditambahkan!';
        }

        if ($action === 'update') {
            $id = safe_int($_POST['voucher_id'] ?? 0);
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $type = $_POST['type'] ?? 'percentage';
            $amount = safe_int($_POST['amount'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $expired_date = $_POST['expired_date'] ?? null;
            $usage_limit = safe_nullable_int($_POST['usage_limit'] ?? null);
            $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

            if ($id <= 0) throw new RuntimeException('voucher_id invalid');
            if ($code === '') throw new RuntimeException('Kode voucher wajib diisi');
            if (!in_array($type, ['percentage','fixed'], true)) throw new RuntimeException('Tipe tidak valid');
            if ($amount < 0) throw new RuntimeException('Nilai diskon tidak valid');
            if ($description === '') throw new RuntimeException('Deskripsi wajib diisi');

            $stmt = $pdo->prepare("UPDATE vouchers SET code = ?, type = ?, amount = ?, description = ?, expired_date = ?, status = ?, usage_limit = ? WHERE id = ?");
            $stmt->execute([$code, $type, $amount, $description, $expired_date ?: null, $status, $usage_limit, $id]);
            $success = 'Voucher berhasil diupdate!';
        }

        if ($action === 'delete') {
            $id = safe_int($_POST['voucher_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('voucher_id invalid');
            $stmt = $pdo->prepare("DELETE FROM vouchers WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Voucher berhasil dihapus!';
        }

        if ($action === 'toggle') {
            $id = safe_int($_POST['voucher_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('voucher_id invalid');
            $stmt = $pdo->prepare("UPDATE vouchers SET status = IF(status='active','inactive','active') WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Status voucher berhasil diubah!';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$vouchers = $pdo->query("SELECT * FROM vouchers ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
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
                <button onclick="openAddModal()" class="btn-primary">+ Tambah Voucher</button>
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
                            <th>Kode</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Limit</th>
                            <th>Terpakai</th>
                            <th>Expired</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($vouchers as $v): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($v['code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($v['type']); ?></td>
                            <td>
                                <?php if($v['type'] === 'percentage'): ?>
                                    <?php echo (int)$v['amount']; ?>%
                                <?php else: ?>
                                    Rp <?php echo number_format((int)$v['amount'], 0, ',', '.'); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo ($v['usage_limit'] === null) ? 'Unlimited' : (int)$v['usage_limit']; ?></td>
                            <td><?php echo (int)$v['used_count']; ?></td>
                            <td><?php echo empty($v['expired_date']) ? '-' : date('d M Y', strtotime($v['expired_date'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo ($v['status'] === 'active') ? 'success' : 'failed'; ?>">
                                    <?php echo ucfirst($v['status']); ?>
                                </span>
                            </td>
                            <td style="display:flex; gap: 8px;">
                                <button type="button" class="btn-secondary" style="padding: 8px 12px;" onclick='openEditModal(<?php echo json_encode([
                                    "id" => (int)$v["id"],
                                    "code" => (string)$v["code"],
                                    "type" => (string)$v["type"],
                                    "amount" => (int)$v["amount"],
                                    "description" => (string)$v["description"],
                                    "expired_date" => (string)($v["expired_date"] ?? ""),
                                    "usage_limit" => $v["usage_limit"],
                                    "status" => (string)$v["status"],
                                ], JSON_UNESCAPED_UNICODE); ?>)'>Edit</button>

                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="voucher_id" value="<?php echo (int)$v['id']; ?>">
                                    <button type="submit" class="btn-secondary" style="padding: 8px 12px;">Toggle</button>
                                </form>

                                <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus voucher ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="voucher_id" value="<?php echo (int)$v['id']; ?>">
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

    <div id="voucherModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Tambah Voucher</h2>

            <form method="POST">
                <input type="hidden" name="action" id="modalAction" value="add">
                <input type="hidden" name="voucher_id" id="voucher_id" value="">

                <div class="form-group">
                    <label>Kode Voucher</label>
                    <input type="text" name="code" id="code" placeholder="Contoh: PLAYSHOP20" required>
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin:0;">
                        <label>Tipe</label>
                        <select name="type" id="type" required>
                            <option value="percentage">percentage</option>
                            <option value="fixed">fixed</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Nilai</label>
                        <input type="number" name="amount" id="amount" placeholder="20" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <input type="text" name="description" id="description" placeholder="Diskon untuk semua game" required>
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin:0;">
                        <label>Expired Date</label>
                        <input type="date" name="expired_date" id="expired_date">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Usage Limit</label>
                        <input type="number" name="usage_limit" id="usage_limit" placeholder="Kosong = Unlimited">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="active">active</option>
                        <option value="inactive">inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Voucher';
            document.getElementById('modalAction').value = 'add';
            document.getElementById('voucher_id').value = '';
            document.getElementById('code').value = '';
            document.getElementById('type').value = 'percentage';
            document.getElementById('amount').value = '';
            document.getElementById('description').value = '';
            document.getElementById('expired_date').value = '';
            document.getElementById('usage_limit').value = '';
            document.getElementById('status').value = 'active';
            document.getElementById('voucherModal').style.display = 'flex';
        }

        function openEditModal(v) {
            document.getElementById('modalTitle').textContent = 'Edit Voucher';
            document.getElementById('modalAction').value = 'update';
            document.getElementById('voucher_id').value = v.id;
            document.getElementById('code').value = v.code || '';
            document.getElementById('type').value = v.type || 'percentage';
            document.getElementById('amount').value = String(v.amount || 0);
            document.getElementById('description').value = v.description || '';
            document.getElementById('expired_date').value = v.expired_date || '';
            document.getElementById('usage_limit').value = (v.usage_limit === null) ? '' : String(v.usage_limit);
            document.getElementById('status').value = v.status || 'active';
            document.getElementById('voucherModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('voucherModal').style.display = 'none';
        }
    </script>
</body>
</html>
