-- ============================================
-- DATABASE PLAYSHOP.ID (Playshop)
-- Full schema (fresh install)
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
-- TRANSACTIONS
-- Note: keep legacy columns user_id/zone_id for compatibility.
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
-- CONTACTS (contact form)
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
-- BANNERS / SLIDER
-- =============================
CREATE TABLE IF NOT EXISTS banners (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(160) NOT NULL,
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
-- SETTINGS (key-value)
-- =============================
CREATE TABLE IF NOT EXISTS settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- NOTIFICATIONS (optional / future)
-- =============================
CREATE TABLE IF NOT EXISTS notifications (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  channel ENUM('email','whatsapp','inapp') DEFAULT 'inapp',
  title VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  meta_json JSON NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
);

-- =============================
-- REVIEWS (optional / future)
-- =============================
CREATE TABLE IF NOT EXISTS reviews (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  transaction_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_reviews_transaction FOREIGN KEY (transaction_id) REFERENCES transactions(id)
    ON DELETE CASCADE
);

-- =============================
-- Seed data
-- =============================
INSERT INTO games (name, icon, color_start, color_end, min_price, description, how_to_topup, faq) VALUES
('Mobile Legends', '⚔️', '#3b82f6', '#1e40af', 5000, 'Top up Diamond Mobile Legends resmi & cepat.', '1) Masukkan User ID dan Zone ID\n2) Pilih nominal\n3) Pilih pembayaran\n4) Bayar\n5) Diamond masuk.', 'Q: Berapa lama?\nA: 1-5 menit setelah pembayaran sukses.'),
('Free Fire', '🔥', '#ef4444', '#dc2626', 5000, 'Top up Diamond Free Fire cepat & aman.', '1) Masukkan User ID\n2) Pilih nominal\n3) Pilih pembayaran\n4) Bayar.', 'Q: Bisa refund?\nA: Mengikuti S&K.'),
('PUBG Mobile', '🎯', '#f59e0b', '#d97706', 10000, 'Top up UC PUBG Mobile.', '1) Masukkan Player ID\n2) Pilih UC\n3) Bayar.', 'Q: Berapa lama?\nA: Biasanya cepat.'),
('Genshin Impact', '⭐', '#8b5cf6', '#7c3aed', 15000, 'Top up Genesis Crystal Genshin Impact.', '1) Masukkan UID\n2) Pilih nominal\n3) Bayar.', 'Q: Apakah aman?\nA: Aman.'),
('Call of Duty Mobile', '🎖️', '#10b981', '#059669', 10000, 'Top up CP Call of Duty Mobile.', '1) Masukkan Player ID\n2) Pilih CP\n3) Bayar.', 'Q: Bisa pakai voucher?\nA: Bisa jika tersedia.');

INSERT INTO products (game_id, name, price, stock, is_active) VALUES
(1, '50 Diamond', 15000, NULL, 1),
(1, '100 Diamond', 28000, NULL, 1),
(1, '250 Diamond', 68000, NULL, 1),
(1, '500 Diamond', 135000, NULL, 1),
(1, '1000 Diamond', 265000, NULL, 1),
(2, '50 Diamond', 5000, NULL, 1),
(2, '100 Diamond', 10000, NULL, 1),
(2, '310 Diamond', 28000, NULL, 1),
(2, '520 Diamond', 47000, NULL, 1),
(2, '1060 Diamond', 95000, NULL, 1),
(3, '60 UC', 15000, NULL, 1),
(3, '325 UC', 75000, NULL, 1),
(3, '660 UC', 150000, NULL, 1),
(3, '1800 UC', 380000, NULL, 1),
(3, '3850 UC', 760000, NULL, 1),
(4, '60 Genesis Crystal', 15000, NULL, 1),
(4, '330 Genesis Crystal', 75000, NULL, 1),
(4, '1090 Genesis Crystal', 235000, NULL, 1),
(4, '2240 Genesis Crystal', 475000, NULL, 1),
(4, '3880 Genesis Crystal', 760000, NULL, 1),
(5, '80 CP', 15000, NULL, 1),
(5, '420 CP', 75000, NULL, 1),
(5, '880 CP', 150000, NULL, 1),
(5, '2400 CP', 380000, NULL, 1),
(5, '5600 CP', 760000, NULL, 1);

INSERT INTO vouchers (code, type, amount, description, expired_date, status, usage_limit, used_count) VALUES
('PLAYSHOP20', 'percentage', 20, 'Diskon 20% untuk semua game', '2026-12-31', 'active', NULL, 0),
('NEWUSER10K', 'fixed', 10000, 'Potongan Rp 10.000 untuk user baru', '2026-12-31', 'active', NULL, 0);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'PLAYSHOP.ID'),
('contact_email', 'support@playshop.id'),
('contact_whatsapp', '+62 812-3456-7890'),
('payment_mode', 'dummy');
