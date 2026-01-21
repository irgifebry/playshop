<?php
// includes/db_utils.php

/**
 * Check whether a column exists on a table (MySQL).
 */
function db_has_column(PDO $pdo, string $table, string $column): bool {
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    if (!$dbName) return false;

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ?
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");
    $stmt->execute([$dbName, $table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

/**
 * Very small helper: safely read a POST field.
 */
function post(string $key, $default = '') {
    return $_POST[$key] ?? $default;
}

