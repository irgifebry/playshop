<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'POST required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($name === '' || $email === '' || $phone === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'missing fields'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'password min 6 chars'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['ok' => false, 'message' => 'email already registered'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, phone, password, status, created_at) VALUES (?, ?, ?, ?, \"active\", NOW())');
    $stmt->execute([$name, $email, $phone, $hash]);

    $id = (int)$pdo->lastInsertId();
    echo json_encode(['ok' => true, 'data' => ['id' => $id, 'name' => $name, 'email' => $email, 'phone' => $phone, 'status' => 'active']], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'server error'], JSON_UNESCAPED_UNICODE);
}
