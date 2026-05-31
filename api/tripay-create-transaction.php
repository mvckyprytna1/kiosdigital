<?php
/**
 * API Tripay Create Transaction
 * KiosDigital PPOB
 */
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/tripay_api.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$product_id = intval($input['product_id'] ?? 0);
$customer_target = sanitize($input['customer_target'] ?? '');
$method = sanitize($input['method'] ?? '');

// 1. Get Product
$prod_sql = "SELECT p.*, c.id as cat_id FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?";
$stmt = $conn->prepare($prod_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
    exit();
}

$invoice_code = generate_invoice('KD');

$conn->begin_transaction();
try {
    // 2. Create Local Transaction (status waiting_payment)
    $ins_trx = "INSERT INTO transactions (invoice_code, user_id, product_id, category_id, customer_target, base_price, selling_price, profit, payment_status, transaction_status, supplier_sku_code) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'unpaid', 'waiting_payment', ?)";
    $trx_stmt = $conn->prepare($ins_trx);
    $trx_stmt->bind_param("siiisddds", $invoice_code, $user_id, $product['id'], $product['cat_id'], $customer_target, $product['base_price'], $product['selling_price'], $product['profit'], $product['provider_code']);
    $trx_stmt->execute();
    $trx_id = $conn->insert_id;

    // 3. Create Tripay Payment Order
    $tripay = new TripayAPI();
    
    // Prepare Data for Tripay
    $merchant_ref = $invoice_code;
    $order_data = [
        'method'         => $method,
        'merchant_ref'   => $merchant_ref,
        'amount'         => intval($product['selling_price']),
        'customer_name'  => $_SESSION['name'],
        'customer_email' => $_SESSION['email'],
        'customer_phone' => '081234567890',
        'order_items'    => [
            [
                'sku'      => $product['provider_code'],
                'name'     => $product['product_name'],
                'price'    => intval($product['selling_price']),
                'quantity' => 1
            ]
        ]
    ];

    $response = $tripay->createTransaction($order_data);

    if ($response && $response['success']) {
        $po_data = $response['data'];
        
        $ins_po = "INSERT INTO payment_orders (invoice_code, user_id, transaction_id, tripay_reference, tripay_merchant_ref, payment_method, payment_name, amount, fee, total_amount, checkout_url, pay_code, qr_url, status, expired_at) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'unpaid', FROM_UNIXTIME(?))";
        $po_stmt = $conn->prepare($ins_po);
        $po_stmt->bind_param("siissssdddsssi", $invoice_code, $user_id, $trx_id, $po_data['reference'], $po_data['merchant_ref'], $po_data['method'], $po_data['payment_name'], $po_data['amount'], $po_data['total_fee'], $po_data['amount_received'], $po_data['checkout_url'], $po_data['pay_code'], $po_data['qr_url'], $po_data['expired_time']);
        $po_stmt->execute();

        $conn->commit();
        echo json_encode(['success' => true, 'checkout_url' => $po_data['checkout_url'], 'invoice_code' => $invoice_code]);
    } else {
        throw new Exception($response['message'] ?? 'Gagal membuat invoice Tripay');
    }

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
