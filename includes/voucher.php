<?php
// includes/voucher.php
require_once __DIR__ . '/db_utils.php';

/**
 * Dummy voucher engine:
 * - validates voucher code in table `vouchers`
 * - supports type: percentage|fixed
 * - checks active + not expired
 * - returns discount amount (integer, >=0)
 */
function voucher_apply(PDO $pdo, string $code, int $subtotal): array {
    $code = strtoupper(trim($code));
    if ($code === '') {
        return ['ok' => true, 'code' => '', 'discount' => 0, 'message' => ''];
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE code = ? LIMIT 1");
        $stmt->execute([$code]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$voucher) {
            return ['ok' => false, 'code' => $code, 'discount' => 0, 'message' => 'Kode voucher tidak ditemukan'];
        }

        if (($voucher['status'] ?? '') !== 'active') {
            return ['ok' => false, 'code' => $code, 'discount' => 0, 'message' => 'Voucher tidak aktif'];
        }

        $usageLimitRaw = $voucher['usage_limit'] ?? null;
        $usedCount = (int)($voucher['used_count'] ?? 0);
        if ($usageLimitRaw !== null) {
            $usageLimit = (int)$usageLimitRaw;
            if ($usageLimit > 0 && $usedCount >= $usageLimit) {
                return ['ok' => false, 'code' => $code, 'discount' => 0, 'message' => 'Voucher sudah mencapai limit penggunaan'];
            }
        }

        if (!empty($voucher['expired_date']) && strtotime($voucher['expired_date']) < strtotime(date('Y-m-d'))) {
            return ['ok' => false, 'code' => $code, 'discount' => 0, 'message' => 'Voucher sudah expired'];
        }

        $type = $voucher['type'] ?? 'percentage';
        $amount = (int)($voucher['amount'] ?? 0);

        if ($subtotal <= 0) {
            return ['ok' => true, 'code' => $code, 'discount' => 0, 'message' => 'Subtotal 0'];
        }

        if ($type === 'fixed') {
            $discount = min($subtotal, max(0, $amount));
        } else {
            // percentage
            $pct = max(0, min(100, $amount));
            $discount = (int) floor(($pct / 100) * $subtotal);
            $discount = min($subtotal, $discount);
        }

        return ['ok' => true, 'code' => $code, 'discount' => $discount, 'message' => 'Voucher terpakai'];
    } catch (Exception $e) {
        // If table doesn't exist yet, keep it dummy-safe
        return ['ok' => false, 'code' => $code, 'discount' => 0, 'message' => 'Voucher belum tersedia (DB belum siap)'];
    }
}

