<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $pdo->query("SELECT id, name, icon, image_path, color_start, color_end, min_price, is_active FROM games WHERE is_active = 1 ORDER BY name");
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['ok' => true, 'data' => $games], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'server error'], JSON_UNESCAPED_UNICODE);
}
