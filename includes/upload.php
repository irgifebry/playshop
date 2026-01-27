<?php
// includes/upload.php

/**
 * Dummy upload handler (no resize, just basic move + allowlist).
 * Use for admin banners/icons later.
 */
function upload_image_dummy(array $file, string $destDir, array $allowedExt = ['jpg','jpeg','png','webp']): array {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['ok' => false, 'path' => '', 'message' => 'File tidak valid'];
    }
    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return ['ok' => false, 'path' => '', 'message' => 'Format file tidak didukung'];
    }
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0777, true);
    }
    $filename = uniqid('upload_', true) . '.' . $ext;
    $path = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $path)) {
        return ['ok' => false, 'path' => '', 'message' => 'Gagal menyimpan file'];
    }
    return ['ok' => true, 'path' => $path, 'message' => 'OK'];
}

/**
 * Safely delete a file given its app-relative path.
 */
function delete_uploaded_file(?string $relPath): bool {
    if (!$relPath) return false;
    
    // Normalize path to prevent directory traversal
    $relPath = ltrim(str_replace(['..', '\\'], ['', '/'], $relPath), '/');
    if ($relPath === '') return false;

    // Use absolute path relative to project root
    $absPath = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relPath);
    
    if (file_exists($absPath) && is_file($absPath)) {
        return @unlink($absPath);
    }
    return false;
}
