<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dummy save settings
    $success = 'Pengaturan berhasil disimpan!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="content-header">
                <h1>Pengaturan Website</h1>
            </div>

            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="settings-container">
                <form method="POST" class="settings-form">
                    <div class="settings-section">
                        <h2>Informasi Website</h2>
                        <div class="form-group">
                            <label>Nama Website</label>
                            <input type="text" value="PLAYSHOP.ID" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email Kontak</label>
                            <input type="email" value="support@playshop.id">
                        </div>
                        <div class="form-group">
                            <label>WhatsApp</label>
                            <input type="text" value="+62 812-3456-7890">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h2>Payment Gateway (Dummy)</h2>
                        <div class="form-group">
                            <label>Status Payment Gateway</label>
                            <select>
                                <option>✅ Aktif (Dummy Mode)</option>
                                <option>❌ Non-aktif</option>
                            </select>
                        </div>
                        <p class="setting-note">⚠️ Website menggunakan payment gateway dummy untuk simulasi</p>
                    </div>

                    <div class="settings-section">
                        <h2>Notifikasi Email (Dummy)</h2>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" checked> Kirim email konfirmasi transaksi
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" checked> Kirim email promo & newsletter
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Simpan Pengaturan</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>