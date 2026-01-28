-- ============================================
-- DATABASE PLAYSHOP.ID (Playshop)
-- Full schema (synced with production)
-- ============================================

CREATE DATABASE IF NOT EXISTS playshop_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE playshop_db;

-- =============================
-- USERS
-- =============================
CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(40) NOT NULL,
  password VARCHAR(255) NOT NULL,
  balance INT NOT NULL DEFAULT 0,
  status ENUM('active','banned') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- GAMES
-- =============================
CREATE TABLE IF NOT EXISTS games (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  icon VARCHAR(10) DEFAULT '🎮',
  image_path VARCHAR(255) NULL,
  description TEXT NULL,
  how_to_topup TEXT NULL,
  faq TEXT NULL,
  color_start VARCHAR(7) DEFAULT '#10b981',
  color_end VARCHAR(7) DEFAULT '#059669',
  min_price INT DEFAULT 5000,
  is_active TINYINT(1) DEFAULT 1,
  category VARCHAR(50) DEFAULT 'Other',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- PRODUCTS
-- =============================
CREATE TABLE IF NOT EXISTS products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  game_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  price INT NOT NULL,
  stock INT NULL DEFAULT NULL, -- NULL=unlimited
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_game FOREIGN KEY (game_id) REFERENCES games(id)
    ON DELETE CASCADE
);

-- =============================
-- VOUCHERS
-- =============================
CREATE TABLE IF NOT EXISTS vouchers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(50) NOT NULL UNIQUE,
  type ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
  amount INT NOT NULL DEFAULT 0,
  description VARCHAR(255) NOT NULL,
  expired_date DATE NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  usage_limit INT NULL DEFAULT NULL,
  used_count INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- PAYMENT METHODS
-- =============================
CREATE TABLE IF NOT EXISTS payment_methods (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  code VARCHAR(50) NOT NULL,
  type VARCHAR(50) DEFAULT 'E-Wallet', -- E-Wallet, Bank Transfer, etc
  fee_flat INT DEFAULT 0,
  fee_percent DECIMAL(5,2) DEFAULT 0,
  image_path VARCHAR(255) NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- TRANSACTIONS
-- =============================
CREATE TABLE IF NOT EXISTS transactions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_id VARCHAR(50) UNIQUE NOT NULL,

  game_id INT NOT NULL,
  product_id INT NOT NULL,

  -- Legacy (still used by some pages)
  user_id VARCHAR(190) NOT NULL,
  zone_id VARCHAR(100) NULL,

  -- Account (logged-in user)
  account_user_id INT NULL,
  account_email VARCHAR(190) NULL,

  -- Game identifiers
  game_user_id VARCHAR(100) NULL,
  game_zone_id VARCHAR(100) NULL,

  payment_method VARCHAR(50) NOT NULL,
  subtotal INT NOT NULL DEFAULT 0,
  admin_fee INT NOT NULL DEFAULT 0,
  discount_amount INT NOT NULL DEFAULT 0,
  voucher_code VARCHAR(50) NULL,
  amount INT NOT NULL,

  status ENUM('pending','success','failed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_transactions_game FOREIGN KEY (game_id) REFERENCES games(id),
  CONSTRAINT fk_transactions_product FOREIGN KEY (product_id) REFERENCES products(id),
  CONSTRAINT fk_transactions_account_user FOREIGN KEY (account_user_id) REFERENCES users(id)
    ON DELETE SET NULL
);

-- =============================
-- DEPOSITS
-- =============================
CREATE TABLE IF NOT EXISTS deposits (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  payment_method_id INT NOT NULL,
  amount INT NOT NULL,
  status ENUM('pending','success','failed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_deposits_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_deposits_payment FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
);

-- =============================
-- API PROVIDERS
-- =============================
CREATE TABLE IF NOT EXISTS api_providers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  api_key VARCHAR(255) NULL,
  secret_key VARCHAR(255) NULL,
  endpoint VARCHAR(255) NULL,
  balance INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- POSTS (BLOG)
-- =============================
CREATE TABLE IF NOT EXISTS posts (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  content LONGTEXT NULL,
  image_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- TESTIMONIALS
-- =============================
CREATE TABLE IF NOT EXISTS testimonials (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  rating TINYINT NOT NULL DEFAULT 5,
  comment TEXT NULL,
  is_shown TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================
-- CONTACTS
-- =============================
CREATE TABLE IF NOT EXISTS contacts (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(120) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new','closed') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================
-- BANNERS
-- =============================
CREATE TABLE IF NOT EXISTS banners (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(160) NOT NULL,
  description TEXT NULL,
  image_path VARCHAR(255) NOT NULL,
  link_url VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  start_date DATE NULL,
  end_date DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- NOTIFICATIONS LOG
-- =============================
CREATE TABLE IF NOT EXISTS notifications_log (
  id INT PRIMARY KEY AUTO_INCREMENT,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================
-- SETTINGS
-- =============================
CREATE TABLE IF NOT EXISTS settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- Seed data
-- =============================
INSERT INTO games (id, name, icon, image_path, description, how_to_topup, faq, color_start, color_end, min_price, is_active, category) VALUES
(1, 'Mobile Legends', '⚔️', 'uploads/games/mobile legends.jpg', 'Top up Diamond Mobile Legends resmi & cepat.', '1) Masukkan User ID dan Zone ID\n2) Pilih nominal\n3) Pilih pembayaran\n4) Bayar\n5) Diamond masuk.', 'Q: Berapa lama?\nA: 1-5 menit setelah pembayaran sukses.', '#3b82f6', '#1e40af', 15000, 1, 'MOBA'),
(2, 'Free Fire', '🔥', 'uploads/games/ff.jpg', 'Top up Diamond Free Fire cepat & aman.', '1) Masukkan User ID\n2) Pilih nominal\n3) Pilih pembayaran\n4) Bayar.', 'Q: Bisa refund?\nA: Mengikuti S&K.', '#232c5f', '#4e676d', 5000, 1, 'Action'),
(3, 'PUBG Mobile', '🎯', 'uploads/games/pubgm.jpg', 'Top up UC PUBG Mobile.', '1) Masukkan Player ID\n2) Pilih UC\n3) Bayar.', 'Q: Berapa lama?\nA: Biasanya cepat.', '#1a3a55', '#30556c', 15000, 1, 'Action'),
(4, 'Genshin Impact', '⭐', 'uploads/games/genshin.jpg', 'Top up Genesis Crystal Genshin Impact.', '1) Masukkan UID\n2) Pilih nominal\n3) Bayar.', 'Q: Apakah aman?\nA: Aman.', '#131219', '#737373', 15000, 1, 'RPG'),
(5, 'Call of Duty Mobile', '🎖️', 'uploads/games/codm.jpg', 'Top up CP Call of Duty Mobile.', '1) Masukkan Player ID\n2) Pilih CP\n3) Bayar.', 'Q: Bisa pakai voucher?\nA: Bisa jika tersedia.', '#b0a427', '#726b19', 15000, 1, 'Action'),
(6, 'Valorant', '🎮', 'uploads/games/valorant.jpg', 'Top up VP Valorant.', '1) Masukkan Player ID\n2) Pilih VP\n3) Bayar.', 'Q: Bisa pakai voucher?\nA: Bisa jika tersedia.', '#732525', '#ba2121', 50000, 1, 'PC');

INSERT INTO products (id, game_id, name, price, stock, is_active) VALUES
(1, 1, '50 Diamond', 15000, NULL, 1),
(2, 1, '100 Diamond', 28000, NULL, 1),
(3, 1, '250 Diamond', 68000, NULL, 1),
(4, 1, '500 Diamond', 135000, NULL, 1),
(5, 1, '1000 Diamond', 265000, NULL, 1),
(6, 2, '50 Diamond', 5000, NULL, 1),
(7, 2, '100 Diamond', 10000, NULL, 1),
(8, 2, '310 Diamond', 28000, NULL, 1),
(9, 2, '520 Diamond', 47000, NULL, 1),
(10, 2, '1060 Diamond', 95000, NULL, 1),
(11, 3, '60 UC', 15000, NULL, 1),
(12, 3, '325 UC', 75000, NULL, 1),
(13, 3, '660 UC', 150000, NULL, 1),
(14, 3, '1800 UC', 380000, NULL, 1),
(15, 3, '3850 UC', 760000, NULL, 1),
(16, 4, '60 Genesis Crystal', 15000, NULL, 1),
(17, 4, '330 Genesis Crystal', 75000, NULL, 1),
(18, 4, '1090 Genesis Crystal', 235000, NULL, 1),
(19, 4, '2240 Genesis Crystal', 475000, NULL, 1),
(20, 4, '3880 Genesis Crystal', 760000, NULL, 1),
(21, 5, '80 CP', 15000, NULL, 1),
(22, 5, '420 CP', 75000, NULL, 1),
(23, 5, '880 CP', 150000, NULL, 1),
(24, 5, '2400 CP', 380000, NULL, 1),
(25, 5, '5600 CP', 760000, NULL, 1),
(26, 6, '475 VP', 55000, NULL, 1),
(27, 6, '1000 VP', 110000, NULL, 1),
(28, 6, '1475 VP', 160000, NULL, 1),
(29, 6, '2050 VP', 215000, NULL, 1),
(30, 6, '3050 VP', 317000, NULL, 1);


INSERT INTO vouchers (code, type, amount, description, expired_date, status, usage_limit, used_count) VALUES
('PLAYSHOP20', 'percentage', 20, 'Diskon 20% untuk semua game', '2026-12-31', 'active', NULL, 0),
('NEWUSER10K', 'fixed', 10000, 'Potongan Rp 10.000 untuk user baru', '2026-12-31', 'active', NULL, 0);

INSERT INTO payment_methods (name, code, type, fee_flat, fee_percent, image_path, is_active) VALUES
('BCA Virtual Account', 'BCA_VA', 'Virtual Account', 2000, 0, NULL, 1),
('QRIS', 'QRIS', 'E-Wallet', 0, 0.7, NULL, 1),
('GoPay', 'GOPAY', 'E-Wallet', 1000, 2.0, NULL, 1);

INSERT INTO banners (image_path, title, description, link_url, sort_order, is_active, start_date, end_date) VALUES
('uploads/banners/banner_mlbb_promo.png', 'Promo Diamond MLBB 50% OFF', 'Promo spesial top up Diamond Mobile Legends! Dapatkan diskon hingga 50% untuk semua nominal. Periode terbatas!', 'game-detail.php?id=1', 1, 1, '2026-01-01', '2026-12-31'),
('uploads/banners/banner_ff_flashsale.png', 'Flash Sale Free Fire', 'Flash Sale Diamond Free Fire! Diskon hingga 90% untuk waktu terbatas. Buruan sebelum kehabisan!', 'game-detail.php?id=2', 2, 1, '2026-01-01', '2026-12-31'),
('uploads/banners/banner_welcome.png', 'Top Up Instant 24 Jam', 'Selamat datang di PLAYSHOP.ID! Top up game favorit kamu kapan saja, proses instan 24 jam non-stop.', 'index.php', 3, 1, '2026-01-01', '2026-12-31');

INSERT INTO api_providers (name, api_key, secret_key, endpoint, balance, is_active) VALUES
('Digiflazz', 'digi-api-key-xxxxx', 'digi-secret-xxxxx', 'https://api.digiflazz.com/v1', 5000000, 1),
('VIP Reseller', 'vip-api-key-xxxxx', 'vip-secret-xxxxx', 'https://vip-reseller.co.id/api', 2500000, 1);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'PLAYSHOP.ID'),
('contact_email', 'support@playshop.id'),
('contact_whatsapp', '+62 812-3456-7890'),
('payment_mode', 'dummy');

INSERT INTO posts (title, slug, content, image_path, created_at) VALUES
('Promo Kemerdekaan: Top Up Mobile Legends Hemat hingga 50%!', 'promo-kemerdekaan-mlbb', 'Rayakan semangat kemerdekaan dengan promo spesial dari PLAYSHOP.ID! Dapatkan bonus diamond berlimpah untuk setiap pembelian di atas Rp 100.000. Promo ini berlaku terbatas hanya sampai akhir bulan ini. Jangan sampai ketinggalan kesempatan untuk mendapatkan skin impianmu dengan harga miring!', NULL, NOW()),
('Cara Mengamankan Akun Game agar Tidak Kena Hack', 'tips-aman-akun-game', 'Keamanan akun adalah prioritas utama setiap gamer. Banyak kasus akun sultan hilang karena kelalaian sederhana. Simak 5 tips ampuh menjaga keamanan akun game kamu: 1. Gunakan verifikasi 2 langkah (2FA). 2. Jangan pernah bagikan password atau OTP ke siapapun. 3. Hindari login di device umum...', NULL, NOW()),
('Bocoran Skin Starlight Bulan Depan, Wajib Beli?', 'bocoran-skin-starlight', 'Moonton kembali merilis skin Starlight yang keren abis untuk hero favorit sejuta umat. Dengan desain futuristik dan efek skill yang memukau, skin ini diprediksi bakal jadi incaran banyak player. Yuk intip detail efek skill dan animasi recall-nya di sini!', NULL, NOW()),
('Update Besar PUBG Mobile: Peta Baru & Senjata Baru', 'update-pubgm-terbaru', 'PUBG Mobile kembali menghadirkan update besar-besaran versi 3.0. Ada peta baru bertema salju yang menantang dan senjata sniper rifle terbaru yang sangat sakit damage-nya. Siapkan strategi tim kamu untuk conqueror season ini!', NULL, NOW()),
('Tips Push Rank Free Fire Cepat ke Grandmaster', 'tips-push-rank-ff', 'Susah naik rank? Sering too soon? Tenang, kami punya rahasia para pro player FF dalam melakukan push rank. Mulai dari pemilihan karakter, kombinasi skill, hingga rotasi map yang efektif. Baca selengkapnya untuk jadi jagoan di squad kamu!', NULL, NOW());

INSERT INTO testimonials (name, rating, comment, is_shown, created_at) VALUES
('Rizky Gaming', 5, 'Gila cepet banget masuknya! Baru bayar langsung blink notif diamond masuk. Mantap jiwa PLAYSHOP!', 1, NOW()),
('Siti Nurhaliza', 5, 'Aman dan terpercaya. CS-nya juga ramah banget pas nanya cara top up via QRIS. Recommended seller!', 1, NOW()),
('Budi Santoso', 4, 'Harganya bersaing bgt sama toko sebelah. Sering-sering promo ya min biar makin rajin top up.', 1, NOW()),
('Dimas Andrean', 5, 'Top up Genesis Crystal disini gapernah minus. Legal 100% anti banned club. Thanks min!', 1, NOW()),
('Citra Kirana', 5, 'Udah langganan dari jaman game warnet sampe sekarang mobile. Best lah pelayanan Playshop selalu sat set.', 1, NOW());

