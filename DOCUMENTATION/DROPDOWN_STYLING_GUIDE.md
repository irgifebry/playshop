# Custom Dropdown/Select Styling Guide

## Dropdown Appearance

### Default State
```
┌─────────────────────────────────┐
│ Call of Duty Mobile         ▼   │  Gray border
└─────────────────────────────────┘  White background
```

### Hover State
```
┌─────────────────────────────────┐
│ Call of Duty Mobile         ▼   │  Green border (#10b981)
└─────────────────────────────────┘  Glow shadow effect
  ✨ Shadow glow
```

### Focus State (When Clicked)
```
┌─────────────────────────────────┐
│ Call of Duty Mobile         ▼   │  Green border
├─────────────────────────────────┤
│ --- Pilih Game ---              │  White option bg
│ Call of Duty Mobile             │
│ Free Fire                        │  Dark text
│ Genshin Impact                   │
│ Mobile Legends                   │
│ PUBG Mobile                      │
│ Valorant                         │  Green bg saat selected
└─────────────────────────────────┘
```

### Focused/Selected Option
```
  Option dengan background HIJAU (#10b981)
  dan text PUTIH untuk highlight
```

## CSS Properties

### Main Styling
| Property | Value | Catatan |
|----------|-------|---------|
| Width | 100% | Full width dalam container |
| Padding | 0.85rem 0.75rem | Comfortable spacing |
| Border | 2px solid #e5e7eb | Light gray default |
| Border Radius | 8px | Rounded corners |
| Font Size | 1rem | Readable font |
| Cursor | pointer | Interactive indicator |
| Appearance | none | Remove browser default |

### Arrow Icon
| Property | Value |
|----------|-------|
| Type | SVG |
| Color | #6b7280 (gray) |
| Position | right 0.75rem center |
| Size | 20px |
| Browser Support | All modern browsers |

### States

#### Hover
```css
border-color: #10b981;
box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
```

#### Focus
```css
outline: none;
border-color: #10b981;
box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
```

#### Active (Open)
```css
border-color: #059669; (primary-dark)
```

#### Disabled
```css
opacity: 0.5;
cursor: not-allowed;
background-color: #f3f4f6;
```

## Browser Compatibility

✅ Chrome/Edge - Full support
✅ Firefox - Full support (dengan SVG arrow)
✅ Safari - Full support
⚠️ IE 11 - Basic styling, SVG arrow fallback

## Visual Consistency

### Color Palette
- **Default Border**: #e5e7eb (light gray)
- **Hover/Focus Border**: #10b981 (primary green)
- **Active Border**: #059669 (primary dark)
- **Background**: #ffffff (white)
- **Arrow**: #6b7280 (gray)
- **Selected Option**: #10b981 (primary green)
- **Option Text**: #111827 (dark text)

### Font & Sizing
- **Font Family**: Inherit dari parent (Plus Jakarta Sans)
- **Font Size**: 1rem
- **Padding**: 0.85rem 0.75rem
- **Arrow Size**: 20px
- **Arrow Distance**: 0.75rem dari kanan

## Usage

### In Form Groups
```php
<div class="form-group">
    <label>Game</label>
    <select name="game_id" required>
        <option value="">-- Pilih Game --</option>
        <option value="1">Call of Duty Mobile</option>
        <option value="2">Free Fire</option>
    </select>
</div>
```

### In Modal Content
```php
<div class="modal-content">
    <select name="game_id">
        <option value="">-- Pilih Game --</option>
        <!-- options -->
    </select>
</div>
```

Styling akan otomatis ter-apply!

## Hard Refresh untuk Melihat Perubahan

Jika dropdown masih terlihat default:
- Windows: `Ctrl + Shift + R`
- Mac: `Cmd + Shift + R`
- Atau buka di Incognito/Private Window
