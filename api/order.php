<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$orderId = $_GET['order_id'] ?? '';
if ($orderId === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'order_id required'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT order_id, status, amount, payment_method, created_at FROM transactions WHERE order_id = ? LIMIT 1");
    $stmt->execute([$orderId]);
    $trx = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trx) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'message' => 'not found'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['ok' => true, 'data' => $trx], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'server error'], JSON_UNESCAPED_UNICODE);
}
