<?php
// includes/whatsapp.php

/**
 * Dummy WhatsApp notifier: writes to a log file instead of calling WA API.
 */
function whatsapp_send_dummy(string $phone, string $message, array $meta = []): bool {
    $dir = __DIR__ . '/../storage';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    $logFile = $dir . '/whatsapp.log';

    $payload = [
        'ts' => date('c'),
        'phone' => $phone,
        'message' => $message,
        'meta' => $meta,
    ];

    return (bool)file_put_contents($logFile, json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
}

