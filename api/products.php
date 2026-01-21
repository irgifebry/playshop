<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$gameId = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;
if ($gameId <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'game_id required'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, game_id, name, price, stock, is_active FROM products WHERE game_id = ? AND is_active = 1 ORDER BY price");
    $stmt->execute([$gameId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['ok' => true, 'data' => $products], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'server error'], JSON_UNESCAPED_UNICODE);
}
