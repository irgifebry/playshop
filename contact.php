<?php
session_start();
require_once 'config/database.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Simpan ke database (tabel contacts - buat dulu)
    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = 'Pesan Anda berhasil dikirim! Kami akan segera merespons.';
    } catch(Exception $e) {
        $error = 'Gagal mengirim pesan. Coba lagi nanti.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami | PLAYSHOP.ID</title>
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
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php" class="active">Kontak</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="contact-section">
        <div class="container">
            <h1 class="page-title">Hubungi Kami</h1>
            <p class="page-subtitle">Ada pertanyaan? Kami siap membantu Anda</p>

            <div class="contact-grid">
                <!-- Contact Info -->
                <div class="contact-info-cards">
                    <div class="contact-card">
                        <div class="contact-icon">📧</div>
                        <h3>Email</h3>
                        <p>support@playshop.id</p>
                        <p>cs@playshop.id</p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">📱</div>
                        <h3>WhatsApp</h3>
                        <p>+62 812-3456-7890</p>
                        <a href="https://wa.me/6281234567890" target="_blank" class="btn-wa">Chat WhatsApp</a>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">🕐</div>
                        <h3>Jam Operasional</h3>
                        <p>Senin - Jumat: 09:00 - 21:00</p>
                        <p>Sabtu - Minggu: 10:00 - 18:00</p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">🌐</div>
                        <h3>Social Media</h3>
                        <div class="social-links">
                            <a href="#" target="_blank">📘 Facebook</a>
                            <a href="#" target="_blank">📷 Instagram</a>
                            <a href="#" target="_blank">🐦 Twitter</a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h2>Kirim Pesan</h2>
                    
                    <?php if($success): ?>
                        <div class="alert success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="contact-form">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" placeholder="Masukkan nama Anda" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="email@example.com" required>
                        </div>

                        <div class="form-group">
                            <label>Subjek</label>
                            <select name="subject" required>
                                <option value="">Pilih subjek</option>
                                <option value="Pertanyaan Umum">Pertanyaan Umum</option>
                                <option value="Masalah Transaksi">Masalah Transaksi</option>
                                <option value="Refund/Komplain">Refund/Komplain</option>
                                <option value="Saran & Kritik">Saran & Kritik</option>
                                <option value="Kerjasama">Kerjasama</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pesan</label>
                            <textarea name="message" rows="6" placeholder="Tulis pesan Anda di sini..." required></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Kirim Pesan</button>
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