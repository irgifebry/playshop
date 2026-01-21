<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/voucher.php';
require_once __DIR__ . '/../includes/db_utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'POST required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$game_id = (int)($_POST['game_id'] ?? 0);
$product_id = (int)($_POST['product_id'] ?? 0);
$game_user_id = trim($_POST['user_id'] ?? '');
$game_zone_id = trim($_POST['zone_id'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? '');
$voucher_code = trim($_POST['voucher_code'] ?? '');

if ($game_id <= 0 || $product_id <= 0 || $game_user_id === '' || $payment_method === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'missing fields'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND game_id = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$product_id, $game_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'message' => 'product not found'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $admin_fee = 1000;
    $subtotal = (int)$product['price'];
    $voucher = voucher_apply($pdo, $voucher_code, $subtotal);
    $discount = (int)($voucher['discount'] ?? 0);
    $total = max(0, $subtotal + $admin_fee - $discount);

    $order_id = 'TRX' . time() . rand(1000, 9999);

    // Use new schema if present
    if (db_has_column($pdo, 'transactions', 'game_user_id')) {
        $stmt = $pdo->prepare("INSERT INTO transactions (order_id, game_id, product_id, user_id, zone_id, game_user_id, game_zone_id, payment_method, subtotal, admin_fee, discount_amount, voucher_code, amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([
            $order_id,
            $game_id,
            $product_id,
            $game_user_id,
            $game_zone_id !== '' ? $game_zone_id : null,
            $game_user_id,
            $game_zone_id !== '' ? $game_zone_id : null,
            $payment_method,
            $subtotal,
            $admin_fee,
            $discount,
            strtoupper($voucher_code),
            $total
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO transactions (order_id, game_id, product_id, user_id, zone_id, payment_method, amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$order_id, $game_id, $product_id, $game_user_id, $game_zone_id, $payment_method, $total]);
    }

    echo json_encode(['ok' => true, 'data' => ['order_id' => $order_id, 'amount' => $total, 'status' => 'pending']], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'server error'], JSON_UNESCAPED_UNICODE);
}
