<?php
// includes/payment_gateway_dummy.php

/**
 * Dummy payment gateway:
 * - create payment token
 * - simulate callback status
 */
function payment_dummy_create(string $orderId, int $amount, string $method): array {
    return [
        'ok' => true,
        'provider' => 'dummy',
        'payment_token' => 'DUMMY-' . $orderId,
        'amount' => $amount,
        'method' => $method,
        'expires_in_sec' => 900
    ];
}

