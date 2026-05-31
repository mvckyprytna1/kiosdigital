<?php
/**
 * API Get Products
 * KiosDigital PPOB
 */
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$category_slug = sanitize($_GET['category'] ?? '');
$brand = sanitize($_GET['brand'] ?? '');

$sql = "SELECT p.* FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE c.slug = ? AND p.status = 'active'";

if (!empty($brand)) {
    $sql .= " AND p.brand = ?";
}

$stmt = $conn->prepare($sql);

if (!empty($brand)) {
    $stmt->bind_param("ss", $category_slug, $brand);
} else {
    $stmt->bind_param("s", $category_slug);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    // If mock mode and products table is empty, insert some dummy products if category is pulsa
    $products[] = $row;
}

// Fallback for Demo/Mock (If database is empty)
if (empty($products) && get_setting('digiflazz_mode') == 'mock') {
    if ($category_slug == 'pulsa') {
        $products = [
            ['id' => 1, 'product_name' => "$brand 5.000", 'selling_price' => 5500, 'description' => 'Pulsa Reguler'],
            ['id' => 2, 'product_name' => "$brand 10.000", 'selling_price' => 10500, 'description' => 'Pulsa Reguler'],
            ['id' => 3, 'product_name' => "$brand 20.000", 'selling_price' => 20500, 'description' => 'Pulsa Reguler'],
            ['id' => 4, 'product_name' => "$brand 50.000", 'selling_price' => 50500, 'description' => 'Pulsa Reguler'],
            ['id' => 5, 'product_name' => "$brand 100.000", 'selling_price' => 100500, 'description' => 'Pulsa Reguler']
        ];
    }
}

echo json_encode(['success' => true, 'data' => $products]);
?>
