# 📡 PLAYSHOP API Reference

Complete REST API documentation untuk Playshop.

## 📍 Base URL
```
http://localhost/playshop/api/
```

## 🔑 Authentication

Beberapa endpoint memerlukan authentication via session:

```php
session_start();
// Authenticated jika: isset($_SESSION['user_id'])
```

---

## 📋 Endpoints

### 🎮 Games Endpoints

#### 1. Get All Games
Retrieve daftar semua game yang aktif.

```http
GET /api/games.php
```

**Parameters:**
Tidak ada

**Response:**
```json
{
  "ok": true,
  "data": [
    {
      "id": 1,
      "name": "Mobile Legends",
      "icon": "🎮",
      "image_path": "uploads/games/ml.jpg",
      "color_start": "#10b981",
      "color_end": "#059669",
      "min_price": 5000,
      "is_active": 1
    },
    {
      "id": 2,
      "name": "PUBG Mobile",
      "icon": "🎯",
      "image_path": "uploads/games/pubg.jpg",
      "color_start": "#f59e0b",
      "color_end": "#d97706",
      "min_price": 10000,
      "is_active": 1
    }
  ]
}
```

**Status Code:**
- `200` - Success
- `500` - Server error

---

### 🛍️ Products Endpoints

#### 2. Get Products by Game
Retrieve daftar produk untuk game tertentu.

```http
GET /api/products.php?game_id=1
```

**Parameters:**
- `game_id` (integer, required) - ID game

**Response:**
```json
{
  "ok": true,
  "data": [
    {
      "id": 1,
      "game_id": 1,
      "name": "12 Diamonds",
      "price": 10000,
      "stock": null,
      "is_active": 1
    },
    {
      "id": 2,
      "game_id": 1,
      "name": "50 Diamonds",
      "price": 45000,
      "stock": 1000,
      "is_active": 1
    }
  ]
}
```

**Error Response:**
```json
{
  "ok": false,
  "message": "game_id parameter is required"
}
```

---

### 👤 User Endpoints

#### 3. Register (User Registration)
Buat akun user baru.

```http
POST /api/register.php
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "081234567890",
  "password": "password123",
  "confirm_password": "password123"
}
```

**Success Response (201):**
```json
{
  "ok": true,
  "message": "Registration successful. Please login.",
  "user": {
    "id": 5,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

**Error Response (400):**
```json
{
  "ok": false,
  "message": "Email already registered"
}
```

**Validation:**
- `name`: min 3 characters
- `email`: valid email format, unique
- `phone`: min 10 characters
- `password`: min 6 characters, must match confirm_password

---

#### 4. Login (User Login)
Login user dengan email & password.

```http
POST /api/login.php
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "ok": true,
  "message": "Login successful",
  "user": {
    "id": 5,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890"
  }
}
```

**Error Response (401):**
```json
{
  "ok": false,
  "message": "Email or password incorrect"
}
```

**Note:** Session akan automatically set setelah login sukses.

---

### 📦 Order Endpoints

#### 5. Create Order
Buat order baru untuk pembelian top up.

```http
POST /api/order-create.php
Content-Type: application/json
```

**Headers:**
```
Cookie: PHPSESSID=<session-id>
```

**Request Body:**
```json
{
  "game_id": 1,
  "product_id": 1,
  "game_user_id": "123456",
  "game_zone_id": "7",
  "quantity": 1,
  "voucher_code": "PROMO10"
}
```

**Parameters:**
| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `game_id` | integer | ✅ | ID game |
| `product_id` | integer | ✅ | ID produk |
| `game_user_id` | string | ✅ | User ID di game (misal: nomor UID) |
| `game_zone_id` | string | ✅ | Server/Zone ID di game |
| `quantity` | integer | ⭕ | Jumlah (default: 1) |
| `voucher_code` | string | ⭕ | Kode voucher/promo |

**Success Response (201):**
```json
{
  "ok": true,
  "message": "Order created successfully",
  "order": {
    "id": 42,
    "order_id": "ORD-1704816000",
    "game_id": 1,
    "product_id": 1,
    "game_user_id": "123456",
    "game_zone_id": "7",
    "quantity": 1,
    "amount": 10000,
    "discount_amount": 1000,
    "final_amount": 9000,
    "voucher_id": 5,
    "status": "pending",
    "created_at": "2024-01-09 12:00:00"
  }
}
```

**Error Response (400):**
```json
{
  "ok": false,
  "message": "User must be logged in"
}
```

**Error Response (400):**
```json
{
  "ok": false,
  "message": "Insufficient stock"
}
```

**Error Response (400):**
```json
{
  "ok": false,
  "message": "Invalid voucher code"
}
```

---

#### 6. Get Order Detail
Retrieve detail order berdasarkan order ID.

```http
GET /api/order.php?order_id=ORD-1704816000
```

**Parameters:**
- `order_id` (string, required) - Order ID

**Response:**
```json
{
  "ok": true,
  "data": {
    "id": 42,
    "order_id": "ORD-1704816000",
    "game": {
      "id": 1,
      "name": "Mobile Legends"
    },
    "product": {
      "id": 1,
      "name": "12 Diamonds"
    },
    "game_user_id": "123456",
    "game_zone_id": "7",
    "quantity": 1,
    "amount": 10000,
    "discount_amount": 1000,
    "final_amount": 9000,
    "status": "pending",
    "payment_method": "bank_transfer",
    "created_at": "2024-01-09 12:00:00"
  }
}
```

---

#### 7. Check Order Status
Cek status order tanpa harus login.

```http
GET /api/status.php?order_id=ORD-1704816000
```

**Alternative:**
```http
GET /api/status.php?email=john@example.com
```

**Parameters:**
- `order_id` (string, optional) - Order ID
- `email` (string, optional) - Email user

**Response:**
```json
{
  "ok": true,
  "status": "completed",
  "message": "Transaksi berhasil diproses",
  "order": {
    "order_id": "ORD-1704816000",
    "game": "Mobile Legends",
    "product": "12 Diamonds",
    "final_amount": 9000,
    "status": "completed",
    "created_at": "2024-01-09 12:00:00",
    "completed_at": "2024-01-09 12:15:00"
  }
}
```

**Possible Status:**
- `pending` - Menunggu pembayaran
- `paid` - Pembayaran diterima
- `processing` - Sedang diproses
- `completed` - Transaksi selesai
- `failed` - Transaksi gagal
- `cancelled` - Transaksi dibatalkan

---

## 🔐 Error Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request berhasil |
| 201 | Created | Resource berhasil dibuat |
| 400 | Bad Request | Request tidak valid |
| 401 | Unauthorized | User belum login |
| 403 | Forbidden | Akses ditolak |
| 404 | Not Found | Resource tidak ditemukan |
| 500 | Server Error | Server error |

---

## 📝 Common Error Messages

```json
{
  "ok": false,
  "message": "User not found"
}
```

```json
{
  "ok": false,
  "message": "Invalid request method. Use POST"
}
```

```json
{
  "ok": false,
  "message": "Missing required field: email"
}
```

```json
{
  "ok": false,
  "message": "server error"
}
```

---

## 🧪 Testing dengan cURL

### Test Get Games
```bash
curl -X GET "http://localhost/playshop/api/games.php"
```

### Test Register
```bash
curl -X POST "http://localhost/playshop/api/register.php" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "phone": "081234567890",
    "password": "test123",
    "confirm_password": "test123"
  }'
```

### Test Login
```bash
curl -X POST "http://localhost/playshop/api/login.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "test123"
  }' \
  -c cookies.txt
```

### Test Create Order (dengan session)
```bash
curl -X POST "http://localhost/playshop/api/order-create.php" \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "game_id": 1,
    "product_id": 1,
    "game_user_id": "123456",
    "game_zone_id": "7",
    "quantity": 1
  }'
```

### Test Check Status
```bash
curl -X GET "http://localhost/playshop/api/status.php?order_id=ORD-1704816000"
```

---

## 🧪 Testing dengan Postman

### Import Collection
1. Download Postman: https://www.postman.com/downloads/
2. Import collection file (jika ada) atau buat manual
3. Set base URL: `http://localhost/playshop/api/`
4. Test setiap endpoint

### Tips
- Use environment variables untuk base URL
- Save authentication token di variable
- Create pre-request scripts untuk setup data
- Use Tests untuk validasi response

---

## 📚 Response Format

Semua response menggunakan JSON format dengan struktur:

```json
{
  "ok": true/false,
  "message": "optional status message",
  "data": {}
}
```

---

## 🔒 Security Notes

1. **Always use HTTPS** untuk production
2. **Validate input** di client dan server
3. **Use parameterized queries** untuk prevent SQL injection
4. **Sanitize output** untuk prevent XSS
5. **Implement rate limiting** untuk prevent abuse
6. **Use strong passwords** dan hash dengan bcrypt
7. **Implement CORS** jika diperlukan cross-origin requests

---

**Last Updated**: January 22, 2026
