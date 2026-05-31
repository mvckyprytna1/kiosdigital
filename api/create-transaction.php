<?php
/**
 * API Create Transaction
 * KiosDigital PPOB
 */
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/digiflazz_api.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$product_id = intval($input['product_id'] ?? 0);
$customer_target = sanitize($input['customer_target'] ?? '');
$payment_method = sanitize($input['payment_method'] ?? 'balance');

// 1. Get Product Data from Database
$prod_sql = "SELECT p.*, c.id as cat_id FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?";
$stmt = $conn->prepare($prod_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Fallback for Demo/Mock
if (!$product && get_setting('digiflazz_mode') == 'mock') {
    $product = [
        'id' => $product_id,
        'category_id' => 1, // Pulsa
        'cat_id' => 1,
        'product_name' => 'Demo Product',
        'selling_price' => 5500,
        'base_price' => 5200,
        'profit' => 300,
        'provider_code' => 'demo_sku'
    ];
}

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
    exit();
}

$amount = $product['selling_price'];
$invoice_code = generate_invoice('KD');

// 2. Handle Balance Payment
if ($payment_method == 'balance') {
    // Check Current Balance
    $user_sql = "SELECT balance FROM users WHERE id = ?";
    $u_stmt = $conn->prepare($user_sql);
    $u_stmt->bind_param("i", $user_id);
    $u_stmt->execute();
    $user_balance = $u_stmt->get_result()->fetch_assoc()['balance'];

    if ($user_balance < $amount) {
        echo json_encode(['success' => false, 'message' => 'Saldo tidak cukup']);
        exit();
    }

    // Process Transaction
    $conn->begin_transaction();
    try {
        // Deduct Balance
        $desc = "Pembelian " . $product['product_name'] . " ke " . $customer_target;
        if (!update_balance($user_id, $amount, 'transaksi', $desc, $invoice_code)) {
            throw new Exception("Gagal memotong saldo");
        }

        // Create Local Transaction Record
        $ins_trx = "INSERT INTO transactions (invoice_code, user_id, product_id, category_id, customer_target, base_price, selling_price, profit, payment_status, transaction_status, supplier_sku_code) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'paid', 'processing', ?)";
        $trx_stmt = $conn->prepare($ins_trx);
        $trx_stmt->bind_param("siiisddds", $invoice_code, $user_id, $product['id'], $product['cat_id'], $customer_target, $product['base_price'], $product['selling_price'], $product['profit'], $product['provider_code']);
        $trx_stmt->execute();
        $trx_id = $conn->insert_id;

        // Call Provider API (Digiflazz)
        $digi = new DigiflazzAPI();
        $ref_id = "KD-" . time() . rand(10, 99);
        $response = $digi->createTransaction($product['provider_code'], $customer_target, $ref_id);

        if ($response && isset($response['data'])) {
            $data = $response['data'];
            $status = strtolower($data['status']); // Success, Pending, Failed
            
            $db_status = 'processing';
            if ($status == 'success') $db_status = 'success';
            if ($status == 'failed') $db_status = 'failed';
            
            $sn = $data['sn'] ?? '';
            $msg = $data['message'] ?? '';

            // Update Transaction
            $up_trx = "UPDATE transactions SET transaction_status = ?, serial_number = ?, supplier_ref_id = ?, note = ? WHERE id = ?";
            $up_stmt = $conn->prepare($up_trx);
            $up_stmt->bind_param("ssssi", $db_status, $sn, $ref_id, $msg, $trx_id);
            $up_stmt->execute();

            // Handle Auto Refund if Failed
            if ($db_status == 'failed') {
                update_balance($user_id, $amount, 'refund', "Refund Gagal: " . $product['product_name'], $invoice_code);
                $conn->query("UPDATE transactions SET payment_status = 'refunded' WHERE id = $trx_id");
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Pesanan berhasil diproses', 'invoice_code' => $invoice_code]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

} else {
    // Other payment methods (Tripay) will be handled in tripay-create-transaction.php
    echo json_encode(['success' => false, 'message' => 'Metode pembayaran tidak valid untuk endpoint ini']);
}
?>
