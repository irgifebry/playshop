# Dokumentasi API

Playshop menyediakan beberapa endpoint API sederhana untuk kebutuhan pengambilan data secara asinkron (AJAX) atau integrasi eksternal. Semua endpoint mengembalikan respons dalam format **JSON**.

Base URL: `{host}/playshop/api/`

---

## Format Respons Standar

**Sukses (200 OK)**
```json
{
  "ok": true,
  "data": { ... } // atau [ ... ]
}
```

**Gagal (4xx / 5xx)**
```json
{
  "ok": false,
  "message": "Deskripsi error"
}
```

---

## Daftar Endpoint

### 1. Get List Games
Mengambil daftar semua game yang aktif.

*   **URL**: `/api/games.php`
*   **Method**: `GET`
*   **Parameter**: Tidak ada.
*   **Contoh Respons**:
    ```json
    {
      "ok": true,
      "data": [
        {
          "id": 1,
          "name": "Mobile Legends",
          "icon": "⚔️",
          "min_price": 5000,
          ...
        },
        ...
      ]
    }
    ```

### 2. Get Products by Game
Mengambil daftar produk (nominal topup) berdasarkan ID Game.

*   **URL**: `/api/products.php`
*   **Method**: `GET`
*   **Parameter**: `game_id` (Integer, Required)
*   **Contoh Request**: `/api/products.php?game_id=1`

### 3. Create Order
Membuat pesanan baru (Checkout).

*   **URL**: `/api/order-create.php`
*   **Method**: `POST`
*   **Body (JSON)**:
    ```json
    {
      "game_id": 1,
      "product_id": 5,
      "user_id": "12345678",
      "zone_id": "1234",
      "payment_method": "QRIS",
      "voucher_code": "PROMO10"
    }
    ```
*   **Respons Sukses**:
    ```json
    {
      "ok": true,
      "data": {
        "order_id": "TRX-1748239...",
        "redirect_url": "/playshop/success.php?order_id=..."
      }
    }
    ```

### 4. Check Order Status
Mengecek status transaksi berdasarkan Order ID.

*   **URL**: `/api/status.php`
*   **Method**: `GET`
*   **Parameter**: `order_id` (String)

---

## Catatan Keamanan
*   Saat ini API bersifat publik (tanpa token autentikasi) untuk endpoint `GET`.
*   Validasi input wajib dilakukan sebelum mengirim data ke endpoint `POST`.
