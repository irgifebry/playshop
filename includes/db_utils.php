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

/**
 * Absolute filesystem path to the app root (the folder containing index.php).
 */
function app_root_dir(): string {
    return realpath(__DIR__ . '/..') ?: (__DIR__ . '/..');
}

/**
 * Base URL path to this app relative to the web server root.
 *
 * Examples:
 * - XAMPP + project under htdocs/playshop -> "/playshop"
 * - Project deployed at web root -> ""
 */
function app_base_url_path(): string {
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $appRoot = realpath(app_root_dir());

    if (!$docRoot || !$appRoot) return '';

    // Windows paths are case-insensitive.
    $docRootNorm = strtolower(str_replace('\\', '/', $docRoot));
    $appRootNorm = strtolower(str_replace('\\', '/', $appRoot));

    if (strpos($appRootNorm, $docRootNorm) !== 0) return '';

    $rel = substr($appRootNorm, strlen($docRootNorm));
    $rel = '/' . trim($rel, '/');
    return $rel === '/' ? '' : $rel;
}

/**
 * Convert an absolute filesystem path inside the app folder into a public relative path.
 * Example:
 *   C:\xampp\htdocs\playshop\uploads\games\a.jpg -> uploads/games/a.jpg
 */
function public_rel_path_from_abs(string $absPath): string {
    $root = realpath(app_root_dir());
    $abs = realpath($absPath) ?: $absPath;

    if ($root) {
        $rootNorm = str_replace('\\', '/', $root);
        $absNorm = str_replace('\\', '/', $abs);

        // Case-insensitive compare for Windows
        if (strpos(strtolower($absNorm), strtolower($rootNorm)) === 0) {
            $rel = substr($absNorm, strlen($rootNorm));
            $rel = ltrim($rel, '/');
            return $rel;
        }
    }

    // Fallback: just return basename (still prevents absolute path leakage).
    return basename($abs);
}

/**
 * Build a URL for an app-relative asset path.
 * Accepts:
 * - "uploads/games/x.jpg"
 * - "/uploads/games/x.jpg"
 * - "/playshop/uploads/games/x.jpg" (already base-prefixed)
 */
function asset_url(string $path): string {
    $path = trim((string)$path);
    if ($path === '') return '';

    // Absolute URL
    if (preg_match('#^https?://#i', $path)) return $path;

    $base = app_base_url_path();

    // Already includes base
    if ($base !== '' && (strpos($path, $base . '/') === 0 || $path === $base)) {
        return $path;
    }

    if (substr($path, 0, 1) === '/') {
        return $base . '/' . ltrim($path, '/');
    }

    return $base . '/' . ltrim($path, '/');
}
