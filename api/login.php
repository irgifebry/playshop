<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'POST required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'email and password required'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, name, email, phone, password, status FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'invalid credentials'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (($user['status'] ?? 'active') !== 'active') {
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'user banned'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    unset($user['password']);
    echo json_encode(['ok' => true, 'data' => $user], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'server error'], JSON_UNESCAPED_UNICODE);
}
