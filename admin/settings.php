<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

function load_settings(PDO $pdo): array {
    try {
        $rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $out[(string)$r['setting_key']] = (string)$r['setting_value'];
        }
        return $out;
    } catch (Exception $e) {
        return [];
    }
}

function setting(array $settings, string $key, string $default = ''): string {
    return $settings[$key] ?? $default;
}

$settings = load_settings($pdo);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $site_name = trim($_POST['site_name'] ?? '');
        $contact_email = trim($_POST['contact_email'] ?? '');
        $contact_whatsapp = trim($_POST['contact_whatsapp'] ?? '');
        $payment_mode = trim($_POST['payment_mode'] ?? 'dummy');

        if ($site_name === '') {
            throw new RuntimeException('Nama website wajib diisi');
        }

        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $pairs = [
            ['site_name', $site_name],
            ['contact_email', $contact_email],
            ['contact_whatsapp', $contact_whatsapp],
            ['payment_mode', $payment_mode],
        ];
        foreach ($pairs as $p) {
            $stmt->execute([$p[0], $p[1]]);
        }

        $success = 'Pengaturan berhasil disimpan!';
        $settings = load_settings($pdo);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan | Admin PLAYSHOP.ID</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-optimization.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

            <?php if($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="settings-container">
                <form method="POST" class="settings-form">
                    <div class="settings-section">
                        <h2>Informasi Website</h2>
                        <div class="form-group">
                            <label>Nama Website</label>
                            <input type="text" name="site_name" value="<?php echo htmlspecialchars(setting($settings, 'site_name', 'PLAYSHOP.ID')); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email Kontak</label>
                            <input type="email" name="contact_email" value="<?php echo htmlspecialchars(setting($settings, 'contact_email', 'support@playshop.id')); ?>">
                        </div>
                        <div class="form-group">
                            <label>WhatsApp</label>
                            <input type="text" name="contact_whatsapp" value="<?php echo htmlspecialchars(setting($settings, 'contact_whatsapp', '+62 812-3456-7890')); ?>">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h2>Payment Gateway</h2>
                        <div class="form-group">
                            <label>Status Payment Gateway</label>
                            <select name="payment_mode">
                                <option value="dummy" <?php echo (setting($settings, 'payment_mode', 'dummy') === 'dummy') ? 'selected' : ''; ?>>✅ Aktif</option>
                                <option value="off" <?php echo (setting($settings, 'payment_mode', 'dummy') === 'off') ? 'selected' : ''; ?>>❌ Non-aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="settings-section">
                        <h2>Notifikasi Email (Dummy)</h2>
                        <div class="form-group">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="email_confirm" checked>
                                <label for="email_confirm">Kirim email konfirmasi transaksi</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="email_promo" checked>
                                <label for="email_promo">Kirim email promo & newsletter</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Simpan Pengaturan</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>