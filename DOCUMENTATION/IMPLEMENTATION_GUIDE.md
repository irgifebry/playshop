# 🚀 IMPLEMENTASI PANDUAN: Searchbar + Category Filter + Layout Reduction

## 📋 Daftar Tugas

Ikuti langkah-langkah berikut untuk mengaktifkan semua fitur baru:

### Phase 1: Database Migration ⚠️ PENTING

**Langkah 1**: Buka **phpMyAdmin**
- Go to `playshop_db` database
- Click "SQL" tab
- Copy-paste query dari file: `database/MIGRATION_ADD_CATEGORY.sql`
- Click "Go" untuk execute

```sql
ALTER TABLE games ADD COLUMN category VARCHAR(50) DEFAULT 'Other' AFTER is_active;
```

**Status Check**: 
- Go to **Structure** tab → Lihat apakah kolom `category` sudah ada
- Jika sudah ada, skip ke Phase 2 ✅

---

### Phase 2: Verifikasi File Code ✅

File-file berikut sudah di-update otomatis:

✅ **index.php** 
- Searchbar HTML + CSS
- Category filter buttons
- JavaScript filterGames() & filterByCategory()

✅ **admin/games.php**
- Kategori dropdown di form modal
- INSERT kategori saat add game
- UPDATE kategori saat edit game

✅ **css/style.css**
- Searchbar styling
- Category filter buttons styling
- Overall layout reduction (~20-25%)

✅ **database/schema.sql**
- Schema diupdate dengan kategori

---

### Phase 3: Testing 🧪

#### Test 1: Homepage Searchbar
1. Buka browser → `http://localhost/playshop/index.php`
2. Lihat searchbar di bawah "Pilih Game Favoritmu"
3. Type nama game (mis: "Mobile" atau "Legends")
4. Game cards harus filter otomatis

**Expected Result**:
- Hanya game dengan nama matching yang ditampilkan
- Real-time filtering saat typing
- "Game tidak ditemukan" message jika tidak ada hasil

#### Test 2: Category Filter
1. Masih di halaman yang sama (index.php)
2. Lihat tombol kategori: Semua, RPG, MOBA, PC, Action, Sports, Strategy
3. Klik tombol kategori (mis: "RPG")
4. Game cards harus filter ke kategori tersebut

**Expected Result**:
- Tombol yang dipilih highlight biru hijau
- Hanya game dengan kategori RPG yang ditampilkan
- Bisa combine dengan searchbar

#### Test 3: Combine Search + Category
1. Klik tombol "MOBA"
2. Type "Mobile" di searchbar
3. Hanya "Mobile Legends" (MOBA) yang muncul

**Expected Result**:
- Both filters bekerja bersamaan
- AND logic (harus match category AND search term)

#### Test 4: Admin Add Game
1. Login ke admin panel
2. Go to "Kelola Game"
3. Klik "+ Tambah Game"
4. Lihat form modal
5. Ada dropdown "Kategori" dengan pilihan

**Expected Result**:
- Dropdown dengan: RPG, MOBA, PC, Action, Sports, Strategy, Casual, Other
- Bisa select kategori saat add game baru

#### Test 5: Admin Edit Game
1. Di halaman "Kelola Game"
2. Klik tombol edit di game manapun
3. Form modal terbuka
4. Lihat dropdown Kategori sudah ter-load dengan nilai game tersebut

**Expected Result**:
- Kategori yang dipilih saat add sudah tersimpan
- Bisa update kategori game

#### Test 6: Layout Reduction
1. Compare UI sebelum vs sesudah
2. Perhatikan:
   - Navbar lebih compact
   - Hero section lebih kecil
   - Game cards spacing lebih rapat
   - Form fields lebih kecil
   - Overall page lebih padat

**Expected Result**:
- Semua element 20-25% lebih kecil
- Tetap responsif di mobile/tablet
- Theme dan warna tetap sama

---

## 🔍 Troubleshooting

### Problem: Searchbar tidak berfungsi
**Solution**: 
- Buka browser console (F12)
- Lihat error message
- Pastikan `js/script.js` sudah ter-load
- Check apakah element `id="searchInput"` ada di index.php

### Problem: Category tidak ter-load di admin
**Solution**:
- Pastikan database migration sudah dijalankan
- Run query: `SELECT * FROM games LIMIT 1;` di phpMyAdmin
- Lihat apakah kolom `category` ada

### Problem: Layout terlihat berantakan
**Solution**:
- Clear browser cache (Ctrl+Shift+Del)
- Reload halaman (Ctrl+F5)
- Check apakah `css/style.css` ter-load di Network tab

### Problem: Games tidak ter-filter saat search
**Solution**:
- Buka console (F12 → Console tab)
- Type: `document.querySelectorAll('.game-card').length`
- Lihat berapa banyak game cards
- Type: `filterGames()`
- Lihat apakah fungsi berjalan

---

## 📚 File Documentation

Untuk dokumentasi lengkap, lihat:
- `DOCUMENTATION/UPDATE_SEARCHBAR_CATEGORY_REDUCTION.md` - Detail semua perubahan
- `database/MIGRATION_ADD_CATEGORY.sql` - SQL migration queries
- `database/schema.sql` - Updated database schema

---

## ✨ Fitur Summary

| Fitur | Status | File |
|-------|--------|------|
| Searchbar | ✅ Active | index.php |
| Category Filter | ✅ Active | index.php |
| Admin Category Form | ✅ Active | admin/games.php |
| Layout Reduction | ✅ Active | css/style.css |
| Database Category Column | ⏳ Pending Manual | database/MIGRATION_ADD_CATEGORY.sql |

---

## 🎯 Next Steps (Opsional)

Jika ingin lebih customize:

1. **Edit kategori pilihan** - Ubah di index.php line ~120
2. **Edit search placeholder** - Ubah "Cari game..." text
3. **Edit layout reduction lebih lanjut** - Tweak CSS values di style.css
4. **Add kategori baru ke admin form** - Edit `<select>` di admin/games.php

---

## 📞 Quick Reference

### Jalankan Database Migration:
```bash
# Via phpMyAdmin SQL tab
ALTER TABLE games ADD COLUMN category VARCHAR(50) DEFAULT 'Other' AFTER is_active;
```

### Search Functionality Code:
**File**: `index.php` (lines 141-170)
```javascript
document.getElementById('searchInput').addEventListener('input', filterGames);
```

### Filter by Category Code:
**File**: `index.php` (lines 131-140)
```javascript
function filterByCategory(category, btn) { ... }
```

### Category Display Code:
**File**: `index.php` (line 128)
```html
<p class="game-category"><?php echo htmlspecialchars($game['category'] ?? 'Other'); ?></p>
```

### CSS Styling:
**File**: `css/style.css` (lines 217-275)
- Searchbar styling
- Category buttons styling
- Game category label styling

---

**Implementasi Status**: 95% Complete ✅
**Pending**: Database Migration (Manual Step)

Setelah menjalankan database migration, semua fitur akan 100% active! 🚀
