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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "includes/header.php"; ?>
    


    <section class="legal-header">
        <div class="container">
            <span class="legal-badge">Bantuan Pelanggan</span>
            <h1>Hubungi Kami</h1>
            <p>Ada pertanyaan atau kendala? Kami siap membantu Anda 24/7.</p>
        </div>
    </section>

    <div class="container">
        <div class="legal-layout">
            <aside class="legal-sidebar">
                <nav class="legal-nav">
                    <a href="privacy.php" class="legal-nav-item">Kebijakan Privasi</a>
                    <a href="partnership.php" class="legal-nav-item">Partnership</a>
                    <a href="about.php" class="legal-nav-item">Tentang Kami</a>
                    <a href="career.php" class="legal-nav-item">Karier</a>
                    <a href="blog.php" class="legal-nav-item">Blog</a>
                    <a href="contact.php" class="legal-nav-item active">Kontak</a>
                    <a href="faq.php" class="legal-nav-item">FAQ</a>
                    <a href="testimonials.php" class="legal-nav-item">Testimoni</a>
                </nav>
            </aside>

            <main class="legal-content">
                <div class="legal-block">
                    <h2>Kirim Pesan</h2>
                    <p>Silakan isi formulir di bawah ini. Tim kami akan merespons pesan Anda maksimal dalam waktu 1x24 jam kerja.</p>
                    
                    <?php if($success): ?>
                        <div class="alert success" style="background: rgba(16, 185, 129, 0.1); color: var(--primary); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid var(--primary);"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert error" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid var(--danger);"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="contact-form">
                        <div class="grid-2-col" style="margin-bottom: 1rem;">
                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <label style="font-weight:600; font-size:0.9rem;">Nama Lengkap</label>
                                <input type="text" name="name" placeholder="Nama Anda" style="padding: 0.8rem; border: 1px solid #e5e7eb; border-radius: 8px;" required>
                            </div>
                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <label style="font-weight:600; font-size:0.9rem;">Email</label>
                                <input type="email" name="email" placeholder="email@example.com" style="padding: 0.8rem; border: 1px solid #e5e7eb; border-radius: 8px;" required>
                            </div>
                        </div>

                        <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem; margin-bottom: 1rem;">
                            <label style="font-weight:600; font-size:0.9rem;">Subjek</label>
                            <select name="subject" style="padding: 0.8rem; border: 1px solid #e5e7eb; border-radius: 8px;" required>
                                <option value="">Pilih subjek</option>
                                <option value="Pertanyaan Umum">Pertanyaan Umum</option>
                                <option value="Masalah Transaksi">Masalah Transaksi</option>
                                <option value="Refund/Komplain">Refund/Komplain</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem; margin-bottom: 1.5rem;">
                            <label style="font-weight:600; font-size:0.9rem;">Pesan</label>
                            <textarea name="message" rows="5" placeholder="Tulis pesan Anda..." style="padding: 0.8rem; border: 1px solid #e5e7eb; border-radius: 8px; resize:vertical;" required></textarea>
                        </div>

                        <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; border-radius: 10px; font-weight: 700;">Kirim Sekarang</button>
                    </form>
                </div>

                <div class="legal-block" style="margin-top: 4rem;">
                    <h2>Informasi Kontak Lainnya</h2>
                    <div class="grid-2-col" style="margin-top: 1rem;">
                        <div>
                            <h3 style="font-size: 1rem; margin-bottom: 0.5rem;">📱 WhatsApp</h3>
                            <p style="font-size: 0.9rem; color: var(--text-light);">+62 812-3456-7890 (Chat Only)</p>
                        </div>
                        <div>
                            <h3 style="font-size: 1rem; margin-bottom: 0.5rem;">📧 Official Email</h3>
                            <p style="font-size: 0.9rem; color: var(--text-light);">support@playshop.id</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

