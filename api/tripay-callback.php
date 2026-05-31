<?php
/**
 * Tripay Callback Handler
 * KiosDigital PPOB
 */
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/tripay_api.php';
require_once '../includes/digiflazz_api.php';

$tripay = new TripayAPI();
$callback_raw = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '';

// 1. Validate Signature
if (!$tripay->validateCallback($callback_raw, $signature)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Signature']);
    exit();
}

$data = json_decode($callback_raw, true);
$event = $_SERVER['HTTP_X_CALLBACK_EVENT'] ?? '';

if ($event == 'payment_status') {
    $reference = $data['reference'];
    $merchant_ref = $data['merchant_ref'];
    $status = strtoupper($data['status']);

    // Find Payment Order
    $po_sql = "SELECT * FROM payment_orders WHERE tripay_reference = ? AND status = 'unpaid' LIMIT 1";
    $stmt = $conn->prepare($po_sql);
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $po = $stmt->get_result()->fetch_assoc();

    if ($po) {
        if ($status == 'PAID') {
            $conn->begin_transaction();
            try {
                // Update Payment Order
                $up_po = "UPDATE payment_orders SET status = 'paid', paid_at = NOW() WHERE id = ?";
                $up_po_stmt = $conn->prepare($up_po);
                $up_po_stmt->bind_param("i", $po['id']);
                $up_po_stmt->execute();

                // Check Transaction Type
                if (!empty($po['transaction_id'])) {
                    // It's a product purchase
                    $trx_id = $po['transaction_id'];
                    $conn->query("UPDATE transactions SET payment_status = 'paid', transaction_status = 'processing' WHERE id = $trx_id");

                    // Send to Digiflazz
                    $trx_data_sql = "SELECT * FROM transactions WHERE id = ?";
                    $td_stmt = $conn->prepare($trx_data_sql);
                    $td_stmt->bind_param("i", $trx_id);
                    $td_stmt->execute();
                    $trx = $td_stmt->get_result()->fetch_assoc();

                    $digi = new DigiflazzAPI();
                    $ref_id = "KD-PO-" . $po['id'];
                    $response = $digi->createTransaction($trx['supplier_sku_code'], $trx['customer_target'], $ref_id);

                    if ($response && isset($response['data'])) {
                        $d_data = $response['data'];
                        $d_status = strtolower($d_data['status']);
                        $db_status = ($d_status == 'success') ? 'success' : (($d_status == 'failed') ? 'failed' : 'processing');
                        $sn = $d_data['sn'] ?? '';
                        $msg = $d_data['message'] ?? '';

                        $up_trx = "UPDATE transactions SET transaction_status = ?, serial_number = ?, supplier_ref_id = ?, note = ? WHERE id = ?";
                        $up_stmt = $conn->prepare($up_trx);
                        $up_stmt->bind_param("ssssi", $db_status, $sn, $ref_id, $msg, $trx_id);
                        $up_stmt->execute();
                    }
                } else {
                    // It's a balance deposit
                    $user_id = $po['user_id'];
                    $amount = $po['amount'];
                    update_balance($user_id, $amount, 'deposit', "Deposit Otomatis Tripay Ref: $reference", $reference);
                }

                $conn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } else if ($status == 'EXPIRED' || $status == 'FAILED') {
            $conn->query("UPDATE payment_orders SET status = '" . strtolower($status) . "' WHERE id = " . $po['id']);
            if (!empty($po['transaction_id'])) {
                $conn->query("UPDATE transactions SET payment_status = '" . strtolower($status) . "', transaction_status = 'failed' WHERE id = " . $po['transaction_id']);
            }
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'PO Not Found or Already Processed']);
    }
}
?>
