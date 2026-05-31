<?php
/**
 * Digiflazz Callback Handler
 * KiosDigital PPOB
 */
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';

$callback_raw = file_get_contents('php://input');
$data = json_decode($callback_raw, true);

if (!$data || !isset($data['data'])) {
    exit();
}

$p = $data['data'];
$ref_id = $p['ref_id'];
$status = strtolower($p['status']); // Success, Pending, Failed
$sn = $p['sn'] ?? '';
$msg = $p['message'] ?? '';

// Find Transaction by Supplier Ref ID
$sql = "SELECT * FROM transactions WHERE supplier_ref_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ref_id);
$stmt->execute();
$trx = $stmt->get_result()->fetch_assoc();

if ($trx) {
    if ($trx['transaction_status'] != 'success' && $trx['transaction_status'] != 'failed') {
        $db_status = 'processing';
        if ($status == 'success') $db_status = 'success';
        if ($status == 'failed') $db_status = 'failed';

        $up_sql = "UPDATE transactions SET transaction_status = ?, serial_number = ?, note = ?, updated_at = NOW() WHERE id = ?";
        $up_stmt = $conn->prepare($up_sql);
        $up_stmt->bind_param("sssi", $db_status, $sn, $msg, $trx['id']);
        $up_stmt->execute();

        // Handle Refund if Failed and Payment was via Balance
        if ($db_status == 'failed' && $trx['payment_status'] == 'paid') {
             // We only auto-refund if it was not already refunded
             update_balance($trx['user_id'], $trx['selling_price'], 'refund', "Gagal Supplier: " . $trx['invoice_code'], $trx['invoice_code']);
             $conn->query("UPDATE transactions SET payment_status = 'refunded' WHERE id = " . $trx['id']);
        }
    }
}

echo json_encode(['success' => true]);
?>
