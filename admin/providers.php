<?php
session_start();
require_once '../config/database.php';

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
        $api_key = trim($_POST['api_key'] ?? '');
        $secret_key = trim($_POST['secret_key'] ?? '');
        $endpoint = trim($_POST['endpoint'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO api_providers (name, api_key, secret_key, endpoint, is_active, balance) VALUES (?, ?, ?, ?, ?, 0)");
            $stmt->execute([$name, $api_key, $secret_key, $endpoint, $is_active]);
            $success = 'Provider berhasil ditambahkan.';
        } else {
            $stmt = $pdo->prepare("UPDATE api_providers SET name = ?, api_key = ?, secret_key = ?, endpoint = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$name, $api_key, $secret_key, $endpoint, $is_active, $id]);
            $success = 'Provider berhasil diupdate.';
        }
    }
}

$providers = $pdo->query("SELECT * FROM api_providers")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Provider | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="content-header">
                <h1>API Provider & Balances</h1>
                <p style="color: #6b7280">Manajemen koneksi ke provider top up (Simulasi)</p>
            </div>

            <?php if($success) echo "<div class='alert success'>$success</div>"; ?>

            <div class="stats-grid">
                <?php foreach($providers as $p): ?>
                <div class="stat-card <?php echo $p['is_active'] ? 'blue' : 'gray'; ?>">
                    <div class="stat-icon">🔌</div>
                    <div class="stat-info">
                        <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                        <p>Saldo: Rp <?php echo number_format($p['balance'], 0, ',', '.'); ?></p>
                        <small>Status: <?php echo $p['is_active'] ? '✅ Terhubung' : '❌ Non-aktif'; ?></small>
                        <button class="btn-secondary" onclick='showModal(<?php echo json_encode($p); ?>)' style="margin-top:10px; width: 100%;">Config</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <div id="providerModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2>Konfigurasi API</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update" id="modalAction">
                <input type="hidden" name="id" id="providerId">
                <div class="form-group"><label>Nama Provider</label><input type="text" name="name" id="pName" required></div>
                <div class="form-group"><label>API Key</label><input type="text" name="api_key" id="pKey"></div>
                <div class="form-group"><label>Secret Key / Password</label><input type="password" name="secret_key" id="pSecret"></div>
                <div class="form-group"><label>Endpoint URL</label><input type="text" name="endpoint" id="pEndpoint"></div>
                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="is_active" id="pActive"> 
                        <label for="pActive">Aktif</label>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Simpan Konfigurasi</button>
            </form>
        </div>
    </div>

    <script>
    function showModal(data) {
        document.getElementById('providerModal').style.display = 'flex';
        document.getElementById('providerId').value = data.id;
        document.getElementById('pName').value = data.name;
        document.getElementById('pKey').value = data.api_key;
        document.getElementById('pEndpoint').value = data.endpoint;
        document.getElementById('pActive').checked = data.is_active == 1;
    }
    function closeModal() { document.getElementById('providerModal').style.display = 'none'; }
    </script>
</body>
</html>
