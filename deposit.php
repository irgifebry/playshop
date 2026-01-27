<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/includes/db_utils.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch active payment methods
$stmt = $pdo->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY type, name");
$payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (int)($_POST['amount'] ?? 0);
    $payment_code = $_POST['payment_method'] ?? '';

    if ($amount < 10000) {
        $error = 'Minimal deposit adalah Rp 10.000';
    } elseif (empty($payment_code)) {
        $error = 'Silakan pilih metode pembayaran';
    } else {
        // Fetch method ID
        $st = $pdo->prepare("SELECT id FROM payment_methods WHERE code = ?");
        $st->execute([$payment_code]);
        $pm_id = $st->fetchColumn();

        if (!$pm_id) {
            $error = 'Metode pembayaran tidak valid';
        } else {
            try {
                // Insert into deposits as pending
                $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, payment_method_id, status) VALUES (?, ?, ?, 'pending')");
                $stmt->execute([$user_id, $amount, $pm_id]);
                $deposit_id = $pdo->lastInsertId();

                // Redirect ke halaman instruksi pembayaran
                header("Location: deposit-pay.php?id=" . $deposit_id);
                exit;

            } catch (Exception $e) {
                $error = 'Terjadi kesalahan: ' . $e->getMessage();
            }
        }
    }
}

// Get current balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_balance = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Up Saldo | PLAYSHOP.ID</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>

    <section class="checkout-section">
        <div class="container">
            <h1 class="page-title">💰 Top Up Saldo (Deposit)</h1>
            <p class="page-subtitle">Isi saldo akun untuk kemudahan transaksi tanpa perlu transfer berkali-kali.</p>

            <div class="checkout-container" style="max-width: 600px;">


                <?php if($success): ?>
                    <div class="alert success"><?php echo $success; ?></div>
                    <div style="text-align:center; margin-top:1rem;">
                        <a href="profile.php" class="btn-primary">Kembali ke Profil</a>
                    </div>
                <?php else: ?>
                    <?php if($error): ?>
                        <div class="alert error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <!-- Step 1: Nominal -->
                        <div class="form-section">
                            <h3>1. Pilih Nominal Deposit</h3>
                            <div class="nominal-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                                <label class="nominal-option">
                                    <input type="radio" name="amount" value="10000" onclick="document.getElementById('custom_amount').value=10000">
                                    <div class="nominal-card">Rp 10.000</div>
                                </label>
                                <label class="nominal-option">
                                    <input type="radio" name="amount" value="50000" onclick="document.getElementById('custom_amount').value=50000">
                                    <div class="nominal-card">Rp 50.000</div>
                                </label>
                                <label class="nominal-option">
                                    <input type="radio" name="amount" value="100000" onclick="document.getElementById('custom_amount').value=100000">
                                    <div class="nominal-card">Rp 100.000</div>
                                </label>
                                <label class="nominal-option">
                                    <input type="radio" name="amount" value="500000" onclick="document.getElementById('custom_amount').value=500000">
                                    <div class="nominal-card">Rp 500.000</div>
                                </label>
                            </div>
                            <div style="margin-top: 15px;">
                                <input type="number" name="amount" id="custom_amount" class="form-control" placeholder="Atau masukkan nominal lain (Min. 10.000)" min="10000">
                            </div>
                        </div>

                        <!-- Step 2: Pembayaran -->
                        <div class="form-section">
                            <h3>2. Pilih Metode Pembayaran</h3>
                            <div class="payment-methods">
                                <?php foreach($payment_methods as $pm): ?>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="<?php echo $pm['code']; ?>" required>
                                    <div class="payment-card">
                                        <?php if($pm['image_path']): ?>
                                            <img src="<?php echo asset_url($pm['image_path']); ?>" alt="logo" style="height:20px; margin-right:8px;">
                                        <?php endif; ?>
                                        <span><?php echo $pm['name']; ?></span>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" class="btn-checkout">TOP UP SEKARANG</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <style>
        .nominal-option input { display: none; }
        .nominal-card {
            border: 2px solid #e5e7eb;
            padding: 1.25rem;
            border-radius: 12px;
            text-align: center;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .nominal-option input:checked + .nominal-card {
            border-color: var(--primary);
            background: #ecfdf5;
            color: var(--primary);
        }
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-family: inherit;
        }
    </style>
</body>
</html>

