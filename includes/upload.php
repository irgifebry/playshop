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

