<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        if($stmt->execute([$name, $phone, $user_id])) {
            $success = 'Profil berhasil diupdate!';
            $_SESSION['user_name'] = $name;
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    if(isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        
        if(password_verify($old_password, $user['password'])) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if($stmt->execute([$hashed, $user_id])) {
                $success = 'Password berhasil diubah!';
            }
        } else {
            $error = 'Password lama salah!';
        }
    }
}

// Get transaction stats
$statsQuery = "SELECT COUNT(*) as total, SUM(amount) as total_spent FROM transactions WHERE status = 'success' AND ";
$statsParams = [];
if (db_has_column($pdo, 'transactions', 'account_user_id')) {
    $statsQuery .= " account_user_id = ? ";
    $statsParams[] = (int)$user_id;
} else {
    $statsQuery .= " user_id = ? ";
    $statsParams[] = $user['email']; // legacy
}
$stmt = $pdo->prepare($statsQuery);
$stmt->execute($statsParams);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <a href="index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                        <span class="logo-icon">🎮</span>
                        <span class="logo-text">PLAYSHOP<span class="highlight">.ID</span></span>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="history.php">Riwayat</a></li>
                    <li><a href="profile.php" class="active">Profil</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="profile-section">
        <div class="container">
            <h1 class="page-title">Profil Saya</h1>

            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="profile-grid">
                <!-- Profile Info Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h3><?php echo $user['name']; ?></h3>
                            <p><?php echo $user['email']; ?></p>
                        </div>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo $stats['total'] ?? 0; ?></span>
                            <span class="stat-label">Total Transaksi</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">Rp <?php echo number_format($stats['total_spent'] ?? 0, 0, ',', '.'); ?></span>
                            <span class="stat-label">Total Belanja</span>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile -->
                <div class="profile-card">
                    <h3>Edit Profil</h3>
                    <form method="POST" class="profile-form">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo $user['email']; ?>" disabled>
                            <small>Email tidak bisa diubah</small>
                        </div>
                        
                        <div class="form-group">
                            <label>No. WhatsApp</label>
                            <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn-submit">Simpan Perubahan</button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="profile-card">
                    <h3>Ubah Password</h3>
                    <form method="POST" class="profile-form">
                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" name="old_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="new_password" minlength="6" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn-submit">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 PLAYSHOP.ID - Transaksi Cepat & Aman</p>
        </div>
    </footer>
</body>
</html>