# Changelog: Admin Games Management - Produk Nominal & Harga

## Update: Searchbar + Category Filter + Overall Layout Reduction (2025 - Update 8) 🎉

### Major Features Added
Implementasi 3 fitur besar untuk meningkatkan UX halaman utama dan mengurangi ukuran keseluruhan layout.

#### 1. Game Searchbar (Fitur Baru)
**Lokasi**: index.php, di bawah "Pilih Game Favoritmu"

- **Functionality**: Real-time search berdasarkan nama game
- **UI**: Search input dengan icon 🔍 dan styling green focus
- **Performance**: DOM filtering (no server calls)
- **Placeholder**: "Cari game..."
- **Responsive**: Tetap baik di mobile/tablet

**Styling**:
- Width: Full-width dengan max-width 500px di center
- Padding: 0.6rem 0.8rem
- Border: 2px solid #e5e7eb, focus: primary color
- Border-radius: 8px
- Font-size: 0.9rem

#### 2. Category Filter (Fitur Baru)
**Lokasi**: index.php, di atas games-grid

- **Categories**: RPG, MOBA, PC, Action, Sports, Strategy, Casual, Other
- **Functionality**: Filter games by kategori, kombinable dengan searchbar
- **UI**: Pill-shaped buttons dengan active state
- **Default**: "Semua" button active

**Button Styling**:
- Padding: 0.5rem 1rem
- Border: 2px solid #e5e7eb
- Border-radius: 20px
- Font-size: 0.8rem
- Active state: Background primary, white text, primary border

**JavaScript Functions Added**:
```javascript
function filterByCategory(category, btn) { ... }
function filterGames() { ... }
```

**Data Attributes on Game Cards**:
- `data-category`: Category dari database
- `data-name`: Lowercase nama game untuk search
- `data-game-id`: Game ID

#### 3. Database Category Column (Backend)
**Table**: games
**Kolom**: `category VARCHAR(50) DEFAULT 'Other'`

**Admin Management**:
- Form modal (admin/games.php) punya dropdown kategori
- Options: RPG, MOBA, PC, Action, Sports, Strategy, Casual, Other
- INSERT kategori saat add game
- UPDATE kategori saat edit game

**Game Display**:
- New label di game card: `<p class="game-category">`
- Font-size: 0.75rem
- Color: primary green (#10b981)
- Font-weight: 600

#### 4. Overall Layout & Typography Reduction (~20-25%)

**Navbar**:
- Logo icon: 60px → 50px
- Logo text: 1.5rem → 1.3rem
- Menu gap: 2rem → 1.5rem
- Menu font-size: default → 0.9rem

**Hero Section**:
- Padding: 4rem → 2.5rem (37.5% reduction)
- Title: 3rem → 2.2rem (26.7% reduction)
- Subtitle: 1.2rem → 0.95rem (20.8% reduction)
- Features gap: 3rem → 2rem (33.3% reduction)

**Games Section**:
- Padding: 4rem → 2.5rem (37.5% reduction)
- Section title: 2.5rem → 1.9rem (24% reduction)
- Section subtitle: default → 0.95rem

**Searchbar & Category**:
- Searchbar margin: 2rem → 1.5rem
- Category buttons margin: 2rem → 1.5rem
- Category buttons padding: 0.6rem 1.2rem → 0.5rem 1rem
- Category buttons font: 0.9rem → 0.8rem

**Forms**:
- Margin-bottom: 1.5rem → 1.2rem (20% reduction)
- Label font-size: default → 0.9rem
- Input padding: 0.75rem → 0.6rem (20% reduction)
- Input font-size: 1rem → 0.9rem (10% reduction)
- Checkbox size: 32px → 28px (12.5% reduction)

**Buttons**:
- Padding: 1rem → 0.8rem (20% reduction)
- Font-size: default → 0.9rem
- Border-radius: 10px → 8px

**Modal**:
- Content padding: 1.5rem → 1.2rem (20% reduction)
- Form gap: 1rem → 0.8rem (20% reduction)
- Textarea padding: 0.9rem → 0.7rem (22% reduction)

**Tables**:
- Cell padding: 1rem → 0.8rem (20% reduction)
- Font-size: default → 0.9rem

**Banner Slider**:
- Grid minmax: 260px → 240px (7.7% reduction)
- Image height: 160px → 130px (18.75% reduction)
- Gap: 1rem → 0.8rem (20% reduction)
- Border-radius: 16px → 12px

**Footer**:
- Padding: 2rem → 1.5rem (25% reduction)
- Font-size: default → 0.9rem

**Checkout Section**:
- Section padding: 2rem → 1.5rem (25% reduction)
- Progress margin: 3rem → 2rem (33% reduction)
- Step number size: 50px → 40px (20% reduction)

#### Files Modified:
- **index.php**: Searchbar HTML + Category buttons + filterGames() JS
- **admin/games.php**: Kategori dropdown form + INSERT/UPDATE kategori
- **css/style.css**: 200+ CSS property adjustments (reduction)
- **database/schema.sql**: Schema updated dengan kategori

#### New Files Created:
- **database/MIGRATION_ADD_CATEGORY.sql**: SQL migration script
- **DOCUMENTATION/UPDATE_SEARCHBAR_CATEGORY_REDUCTION.md**: Detail dokumentasi
- **DOCUMENTATION/IMPLEMENTATION_GUIDE.md**: Panduan implementasi
- **DOCUMENTATION/SUMMARY_CHANGES.md**: Ringkasan perubahan

#### Testing & Verification:
✅ Searchbar real-time filtering works
✅ Category filter buttons functional
✅ Combine search + category = AND logic
✅ Admin kategori dropdown shows/saves correctly
✅ Layout reduction konsisten ~20-25% per section
✅ Mobile responsive tetap baik
✅ Theme colors tetap sama (hijau #10b981)
✅ Styling konsisten di semua halaman

#### Performance Impact:
- 0 server calls untuk search/filter (DOM manipulation only)
- 1 extra column di database (minimal overhead)
- ~50 lines JavaScript added (minimal)
- CSS file size negligible change
- Overall: Negligible negative impact, positive UX impact

#### Database Migration Required:
```sql
ALTER TABLE games ADD COLUMN category VARCHAR(50) DEFAULT 'Other' AFTER is_active;
```

#### Result:
✅ User dapat **search game by name** secara real-time
✅ User dapat **filter by kategori** dengan mudah
✅ Admin dapat **manage kategori** di form
✅ Seluruh layout **20-25% lebih compact**
✅ Semua halaman **lebih efficient & modern**

---

## Update: Game Cards Size Reduction (22 Januari 2026 - Update 7)

### Game Cards Sizing Improvement
Game cards di homepage (index.php) telah diperkecil untuk layout yang lebih compact dan bisa menampilkan lebih banyak games sekaligus.

#### Perubahan Sizing:
- **Grid Column**: Dari minmax(280px) menjadi minmax(200px) - 28% lebih kecil
- **Gap**: Dari 2rem menjadi 1.5rem - lebih compact spacing
- **Image Height**: Dari 200px menjadi 140px - 30% lebih kecil
- **Icon Size**: Dari 5rem menjadi 3.5rem - lebih proporsional
- **Padding**: Dari 1.5rem menjadi 1rem - lebih minimal
- **Name Font**: Dari 1.3rem menjadi 1.1rem - lebih kecil
- **Price Font**: Ditambah font-size 0.9rem untuk proporsi
- **Button Padding**: Dari 0.75rem menjadi 0.6rem
- **Button Font**: Ditambah font-size 0.9rem
- **Detail Button**: Padding 0.5rem 0.75rem (dari 0.75rem 1rem) + font-size 0.85rem

#### Visual Adjustments:
- **Border Radius**: Dari 15px menjadi 12px (game-card) dan 6px (buttons)
- **Box Shadow**: Dari 0 4px 6px menjadi 0 2px 4px - lebih subtle
- **Hover Transform**: Dari translateY(-10px) menjadi translateY(-5px) - lebih halus
- **Hover Shadow**: Dari 0 10px 20px menjadi 0 8px 16px - lebih gentle

#### Result:
✅ Game cards **~28% lebih kecil**
✅ Dapat menampilkan **lebih banyak games** dalam satu viewport
✅ Layout lebih **compact dan rapi**
✅ Typography lebih **proporsional**
✅ Spacing lebih **minimal dan efficient**

#### File yang Dimodifikasi:
- `css/style.css` - Game card sizing, button sizing, dan spacing adjustments

#### Comparison:
| Element | Before | After | Reduction |
|---------|--------|-------|-----------|
| Column Width | 280px+ | 200px+ | 28% |
| Image Height | 200px | 140px | 30% |
| Icon Size | 5rem | 3.5rem | 30% |
| Padding | 1.5rem | 1rem | 33% |
| Gap | 2rem | 1.5rem | 25% |

---

## Update: Footer Layout Fix - Sticky Footer (22 Januari 2026 - Update 6)

### Footer Positioning Improvement
Footer di halaman check-order.php dan history-section sekarang berada di bawah dengan proper spacing, tidak lagi terlalu atas.

#### Perubahan Layout:
- **Body Layout**: Diubah ke flexbox dengan `flex-direction: column` dan `min-height: 100vh`
- **Header**: `flex-shrink: 0` agar tetap di atas dan tidak di-shrink
- **Main Content**: `.check-order-section` dan `.history-section` ditambah `flex: 1` agar grow dan push footer ke bawah
- **Footer**: `margin-top: auto` dan `flex-shrink: 0` agar tetap di bawah
- **Padding**: Ditingkatkan padding-bottom dari 4rem menjadi 6rem untuk spacing lebih baik

#### Technical Implementation:
```css
html { height: 100%; }
body { 
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
header { flex-shrink: 0; }
.check-order-section { flex: 1; }
.footer { 
    margin-top: auto;
    flex-shrink: 0;
}
```

#### Result:
✅ Footer selalu berada di bawah halaman
✅ Tidak masalah jika konten sedikit
✅ Automatic sticky layout tanpa JavaScript
✅ Consistent di semua halaman dengan footer

#### File yang Dimodifikasi:
- `css/style.css` - Body flexbox layout + footer positioning

#### Halaman yang Ter-affect:
✅ check-order.php - "Cek Status Pesanan"
✅ history.php - "Riwayat Transaksi"
✅ Semua halaman user dengan section + footer

---

## Update: Custom Dropdown/Select Styling (22 Januari 2026 - Update 5)

### Dropdown Styling Improvements
Custom dropdown/select styling telah ditambahkan ke semua form di admin panel untuk konsistensi dan tampilan yang lebih professional.

#### Fitur Dropdown Baru:
- **Custom SVG Arrow**: Panah dropdown custom dengan warna abu-abu (#6b7280)
- **Smooth Styling**: Border abu-abu default yang berubah hijau saat hover/focus
- **Hover Effect**: Border primary + glow shadow saat mouse hover
- **Focus State**: Border primary + stronger shadow untuk keyboard navigation
- **Active State**: Border primary-dark saat dropdown sedang dibuka
- **Disabled State**: Opacity 0.5 dan background abu-abu terang
- **Option Styling**: Background putih default, highlight hijau saat selected

#### CSS Applied:
- `.form-group select` - Styling untuk select di form modal
- `.modal-content select` - Styling untuk select di dalam modal
- Option styling untuk better visual feedback

#### Visual Details:
- **Padding**: 0.85rem padding untuk lebih spacious
- **Border Radius**: 8px untuk consistency dengan checkbox dan input
- **Arrow Position**: Right 0.75rem center
- **Arrow Size**: 20px untuk visibility yang bagus
- **Transition**: 0.3s ease untuk smooth state changes

#### File yang Ter-update:
- `css/style.css` - Ditambah `.form-group select` dan `.modal-content select` styling

#### Dropdown Locations:
✅ admin/games.php - Genre/Game selection
✅ admin/products.php - Game selection dropdown
✅ admin/banners.php - Semua select elements
✅ Admin pages lainnya yang memiliki dropdown

---

## Update: Checkbox Styling Applied to All Admin Pages (22 Januari 2026 - Update 4)

### Checkbox Consistency Across Admin Panel
Custom checkbox styling yang telah dibuat diterapkan ke semua halaman admin yang memiliki checkbox untuk konsistensi visual.

#### File yang Diperbarui dengan Wrapper Checkbox:
1. **admin/games.php** - Checkbox "Active" di form tambah/edit game ✅
2. **admin/products.php** - Checkbox "Active" di form tambah/edit produk ✅
3. **admin/banners.php** - Checkbox "Aktif" di form tambah/edit banner ✅

#### CSS Applied to All:
- `.form-group .checkbox-wrapper` - Override untuk form context
- Semua hover, checked, dan focus states bekerja konsisten
- Using `!important` flags untuk memastikan no override

#### What Users Will See:
✅ **32x32px checkbox** (bukan lagi default browser biru kecil)
✅ **Custom styled** dengan tema hijau (#10b981)
✅ **Light gray background** wrapper dengan border rapi
✅ **Smooth animations** pada hover dan state changes
✅ **Consistent** di semua admin pages

#### Hard Refresh Required:
Untuk melihat perubahan, users perlu:
- Windows: `Ctrl + Shift + R` atau `Ctrl + F5`
- Mac: `Cmd + Shift + R`
- Atau buka di Incognito/Private Window

---

## Update: Enhanced Checkbox Styling & Size (22 Januari 2026 - Update 3)

### Peningkatan Checkbox "Active" 
Checkbox telah diperbarui dengan ukuran yang lebih besar (32x32px), layout yang lebih rapih, dan visual yang lebih prominent.

#### Perbaikan Visual:
- **Ukuran**: Ditingkatkan dari 24x24px menjadi 32x32px
- **Border**: Lebih tebal (2.5px) untuk terlihat lebih jelas
- **SVG Checkmark**: Diperbesar dari 14px menjadi 18px
- **Wrapper Background**: Ditambah background #fafafa dan border untuk visual separation
- **Gap/Spacing**: Ditingkatkan dari 0.75rem menjadi 1rem untuk lebih spacious
- **Label Font Size**: Ditingkatkan menjadi 1.05rem agar seimbang dengan checkbox besar
- **Padding Wrapper**: 0.75rem untuk breathing room di dalam container

#### Layout Improvements:
- **Wrapper Container**: Background color #fafafa (light gray) dengan border 1px solid #e5e7eb
- **Alignment**: Flex dengan center alignment untuk checkbox dan label sejajar sempurna
- **Width**: Wrapper set ke `fit-content` agar tidak full-width seperti input normal
- **Visual Separation**: Form-group override untuk checkbox-wrapper agar tidak dipengaruhi style input umum

#### Interaksi Hover:
- **Transform Scale**: Ditambah scale(1.05) saat hover untuk micro-interaction feedback
- **Shadow**: Ditingkatkan dari 3px menjadi 4px untuk lebih prominent
- **Shadow Opacity**: Ditingkatkan dari 0.1 menjadi 0.15 untuk lebih terlihat

#### CSS Override Rules:
```css
.form-group .checkbox-wrapper {
    width: auto;
    margin-bottom: 0;
}

.form-group .checkbox-wrapper input[type="checkbox"] {
    width: 32px !important;
    height: 32px !important;
}
```
Memastikan checkbox tidak dihancurkan oleh style input form-group generic.

#### File yang Dimodifikasi:
- `css/style.css` - Enhanced checkbox styling dan form-group overrides

---

## Update: Custom Checkbox Styling (22 Januari 2026 - Update 2)

### Perubahan Tampilan Checkbox "Active"
Checkbox di form tambah/edit game telah diperbarui dengan desain custom yang senada dengan tema keseluruhan aplikasi PLAYSHOP.

#### Fitur Styling Baru:
- **Checkbox Custom Design**: Ukuran 24x24px dengan border radius 6px (senada dengan border radius komponen lain)
- **Warna Tema Hijau**: Menggunakan CSS variables `--primary` (#10b981) dan `--primary-dark` (#059669)
- **Animasi Hover**: Border glow effect dengan subtle shadow warna hijau transparan saat hover
- **Checkmark Icon**: SVG checkmark yang muncul saat checkbox di-check (warna putih)
- **Focus State**: Visual feedback border dan shadow untuk keyboard navigation
- **Transisi Smooth**: Transition effect 0.3s untuk semua state changes

#### State Checkbox:
1. **Default**: Border #d1d5db (abu-abu), background putih, cursor pointer
2. **Hover**: Border primary color (#10b981) dengan box-shadow glow
3. **Checked**: Background primary color dengan checkmark SVG putih
4. **Checked + Hover**: Background primary-dark (#059669) dengan shadow lebih gelap
5. **Focus**: Border primary dengan shadow untuk keyboard navigation

#### File yang Dimodifikasi:
- `css/style.css` - Ditambah `.checkbox-wrapper` styles (approx 55 lines)
- `admin/games.php` - Struktur HTML checkbox diperbarui dengan wrapper div

#### CSS Classes Baru:
```css
.checkbox-wrapper {} - Container flex untuk checkbox + label
.checkbox-wrapper input[type="checkbox"] {} - Styling checkbox custom
.checkbox-wrapper input[type="checkbox"]:hover {} - Hover state
.checkbox-wrapper input[type="checkbox"]:checked {} - Checked state dengan SVG checkmark
.checkbox-wrapper input[type="checkbox"]:focus {} - Focus state
.checkbox-wrapper label {} - Label styling senada dengan checkbox
```

---

## Update: Custom Checkbox Styling (22 Januari 2026 - Update 2)

### Perubahan Tampilan Checkbox "Active"
Checkbox di form tambah/edit game telah diperbarui dengan desain custom yang senada dengan tema keseluruhan aplikasi PLAYSHOP.

#### Fitur Styling Baru:
- **Checkbox Custom Design**: Ukuran 24x24px dengan border radius 6px (senada dengan border radius komponen lain)
- **Warna Tema Hijau**: Menggunakan CSS variables `--primary` (#10b981) dan `--primary-dark` (#059669)
- **Animasi Hover**: Border glow effect dengan subtle shadow warna hijau transparan saat hover
- **Checkmark Icon**: SVG checkmark yang muncul saat checkbox di-check (warna putih)
- **Focus State**: Visual feedback border dan shadow untuk keyboard navigation
- **Transisi Smooth**: Transition effect 0.3s untuk semua state changes

#### State Checkbox:
1. **Default**: Border #d1d5db (abu-abu), background putih, cursor pointer
2. **Hover**: Border primary color (#10b981) dengan box-shadow glow
3. **Checked**: Background primary color dengan checkmark SVG putih
4. **Checked + Hover**: Background primary-dark (#059669) dengan shadow lebih gelap
5. **Focus**: Border primary dengan shadow untuk keyboard navigation

#### File yang Dimodifikasi:
- `css/style.css` - Ditambah `.checkbox-wrapper` styles (approx 55 lines)
- `admin/games.php` - Struktur HTML checkbox diperbarui dengan wrapper div

#### CSS Classes Baru:
```css
.checkbox-wrapper {} - Container flex untuk checkbox + label
.checkbox-wrapper input[type="checkbox"] {} - Styling checkbox custom
.checkbox-wrapper input[type="checkbox"]:hover {} - Hover state
.checkbox-wrapper input[type="checkbox"]:checked {} - Checked state dengan SVG checkmark
.checkbox-wrapper input[type="checkbox"]:focus {} - Focus state
.checkbox-wrapper label {} - Label styling senada dengan checkbox
```

---

## Tanggal: 22 Januari 2026

## Ringkasan Perubahan
Admin sekarang dapat mengelola nominal diamond/asset dan harga langsung saat menambah atau mengedit game di halaman `admin/games.php`.

## Fitur Baru

### 1. **Manajemen Produk dalam Form Game**
   - Saat menambah game baru, admin dapat langsung menambahkan nominalan dan harganya
   - Saat mengedit game, admin dapat melihat, menambah, mengubah, atau menghapus nominalan yang ada

### 2. **Antarmuka Dinamis untuk Produk**
   - Rows dinamis untuk setiap produk dengan input:
     - **Nama Nominal** (contoh: "50 Diamond", "100 Gems", dll)
     - **Harga** (dalam Rupiah)
     - **Tombol Hapus** untuk menghapus nominal
   - Tombol "+ Tambah Nominal" untuk menambah row produk baru
   - Separator visual antara info game dan section produk

### 3. **Proses Penyimpanan Otomatis**
   - **Tambah Game**: Semua nominal dan harga disimpan ke tabel `products` saat game dibuat
   - **Edit Game**: 
     - Produk yang diedit akan diupdate
     - Produk baru akan ditambahkan
     - Produk yang dihapus akan dihapus dari database

## Perubahan Teknis

### File yang Dimodifikasi
- `admin/games.php`

### Perubahan Backend (PHP)

#### Action "add"
```php
// Setelah INSERT games, sekarang juga INSERT products
$game_id = $pdo->lastInsertId();
$product_names = $_POST['product_names'] ?? [];
$product_prices = $_POST['product_prices'] ?? [];

// Loop dan insert semua produk
```

#### Action "update"
```php
// Sekarang menangani 3 operasi pada products:
1. Delete produk yang ditandai untuk dihapus
2. Update produk yang sudah ada (diidentifikasi oleh product_id)
3. Insert produk baru (product_id = 0 atau kosong)
```

### Perubahan Frontend (JavaScript & HTML)

#### Form Modal
- Ditambah `<input type="hidden" name="products_action">` untuk menandai apakah ini operasi update
- Ditambah `<input type="hidden" id="deleteProductIds">` untuk tracking produk yang dihapus
- Ditambah section baru "Produk (Nominal & Harga)" dengan `id="productsContainer"`
- Ditambah tombol "+ Tambah Nominal"

#### Fungsi JavaScript Baru
- `addProductRow(id, name, price)` - Membuat row input baru untuk produk
- `openAddModal()` - Diupdate untuk menginisialisasi 2 empty product rows
- `openEditModal(game)` - Diupdate untuk load existing products

#### Struktur Data Form Input
```
product_ids[]        : Array of existing product IDs (0 untuk produk baru)
product_names[]      : Array of product names (nominal descriptions)
product_prices[]     : Array of product prices
product_delete_ids[] : Array of product IDs yang akan dihapus
```

## Cara Penggunaan

### Menambah Game Baru dengan Produk
1. Klik "Tambah Game"
2. Isi info game (nama, icon, warna, dll)
3. Di section "Produk (Nominal & Harga)", isi:
   - Nama nominal (contoh: "50 Diamond")
   - Harga (contoh: 15000)
4. Klik "+ Tambah Nominal" untuk menambah row lain
5. Klik "Simpan"

### Edit Game dan Produknya
1. Klik "Edit" pada game yang ingin diubah
2. Ubah info game sesuai kebutuhan
3. Di section produk:
   - **Ubah produk**: Ganti nama/harga existing dan simpan
   - **Tambah produk**: Isi row kosong baru dan simpan
   - **Hapus produk**: Klik "Hapus" pada row yang ingin dihapus
4. Klik "Simpan"

## Database Queries
Tidak ada perubahan schema database. Fitur ini memanfaatkan tabel `products` yang sudah ada dengan relasi `game_id`.

### Struktur Tabel Products (untuk referensi)
```sql
CREATE TABLE products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  game_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,           -- Nama nominal (50 Diamond, dll)
  price INT NOT NULL,                   -- Harga
  stock INT NULL DEFAULT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_game FOREIGN KEY (game_id) REFERENCES games(id)
    ON DELETE CASCADE
);
```

## Validasi Data
- Nama nominal tidak boleh kosong (jika ada harga)
- Harga harus > 0 (jika ada nama nominal)
- Semua input divalidasi di backend sebelum penyimpanan

## Catatan Penting
1. Produk lama yang tidak ditampilkan di modal saat edit tetap aman di database
2. Penghapusan produk hanya bisa dilakukan melalui tombol "Hapus" di modal edit
3. Tidak ada riwayat/log perubahan produk (fitur optional untuk fase selanjutnya)

## Testing Checklist
- [ ] Tambah game dengan 2-3 nominal dan harga
- [ ] Verifikasi produk tersimpan di database
- [ ] Edit game: ubah nominal, tambah nominal baru
- [ ] Edit game: hapus salah satu nominal
- [ ] Verifikasi halaman user menampilkan produk dengan benar
