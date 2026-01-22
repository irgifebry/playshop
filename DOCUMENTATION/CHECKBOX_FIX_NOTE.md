# Checkbox Styling - Fix Kecil Baru

## Problem yang Diperbaiki
Checkbox "Active" di form games.php mungkin masih terlihat kecil atau dengan styling yang lama karena CSS cache browser.

## Solution

### Jika Checkbox Masih Terlihat Kecil:
1. **Hard Refresh Browser**
   - Windows: `Ctrl + Shift + R` atau `Ctrl + F5`
   - Mac: `Cmd + Shift + R`
   - Atau: Buka DevTools (F12) → Settings → Disable Cache → Refresh

2. **Clear Browser Cache**
   - Chrome: Settings → Privacy and Security → Clear Browsing Data
   - Firefox: History → Clear Recent History
   - Safari: Develop → Empty Caches

3. **Atau buka halaman di Incognito/Private Window**
   - Tidak akan menggunakan cache
   - Langsung load CSS terbaru

## CSS yang Diperbaiki

### 1. **Form-Group Level Override**
Ditambah complete override di level `.form-group .checkbox-wrapper` untuk memastikan:
- Wrapper styling ter-apply (background, border, padding, gap)
- Checkbox sizing tetap 32x32px
- Semua states (hover, checked, focus) bekerja

### 2. **!important Flags**
Ditambahkan `!important` pada semua properties untuk memastikan tidak ter-override oleh CSS manapun.

### 3. **Complete State Coverage**
- `:hover` - Border hijau, shadow, scale up
- `:checked` - Background hijau, checkmark
- `:checked:hover` - Background hijau tua, shadow lebih gelap
- `:focus` - Keyboard navigation support

## File yang Dimodifikasi
- `css/style.css` - Form-group level checkbox overrides ditambah

## Checklist
- [ ] Hard refresh browser (Ctrl+Shift+R)
- [ ] Checkbox sekarang terlihat 32x32px (lebih besar)
- [ ] Checkbox memiliki background light gray (#fafafa)
- [ ] Label "Active" sejajar dengan checkbox
- [ ] Saat hover: border hijau, shadow, scale up
- [ ] Saat checked: background hijau dengan checkmark putih
- [ ] Tidak lagi terlihat browser default checkbox biru kecil
