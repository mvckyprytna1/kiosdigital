<?php
/**
 * Shared Functions
 * KiosDigital PPOB
 */

/**
 * Clean & Sanitize Input
 */
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_with_params($conn, htmlspecialchars(strip_tags(trim($data))));
}

/**
 * Handle mysqli real escape for compatibility
 */
function mysqli_real_escape_with_params($conn, $data) {
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Redirect User
 */
function redirect($path) {
    header("Location: $path");
    exit();
}

/**
 * Format Currency IDR
 */
function format_idr($amount) {
    return "Rp " . number_format($amount, 0, ',', '.');
}

/**
 * Generate Invoice Code
 */
function generate_invoice($prefix = 'INV') {
    return $prefix . date('YmdHis') . rand(100, 999);
}

/**
 * Get Setting Value
 */
function get_setting($key) {
    global $conn;
    $sql = "SELECT setting_value FROM settings WHERE setting_key = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    return null;
}

/**
 * Check User Login & Role
 */
function check_role($roles = []) {
    if (!isset($_SESSION['user_id'])) {
        redirect('/login.php');
    }
    if (!empty($roles) && !in_array($_SESSION['role'], $roles)) {
        redirect('/dashboard.php');
    }
}

/**
 * Log API Call
 */
function log_api($provider, $type, $endpoint, $request, $response, $http_code, $status) {
    global $conn;
    $sql = "INSERT INTO api_logs (provider, type, endpoint, request_payload, response_payload, http_code, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $req_json = is_array($request) ? json_encode($request) : $request;
    $res_json = is_array($response) ? json_encode($response) : $response;
    $stmt->bind_param("sssssis", $provider, $type, $endpoint, $req_json, $res_json, $http_code, $status);
    $stmt->execute();
}

/**
 * Update User Balance
 */
function update_balance($user_id, $amount, $type, $description, $ref_id = null) {
    global $conn;
    
    // Get Current Balance
    $sql = "SELECT balance FROM users WHERE id = ? FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $balance_before = $stmt->get_result()->fetch_assoc()['balance'];
    
    $balance_after = ($type == 'refund' || $type == 'deposit' || $type == 'manual_add') ? ($balance_before + $amount) : ($balance_before - $amount);
    
    if ($balance_after < 0 && ($type == 'transaksi' || $type == 'manual_reduce')) {
        return false; // Insufficient balance
    }
    
    // Update Users Table
    $up_sql = "UPDATE users SET balance = ? WHERE id = ?";
    $up_stmt = $conn->prepare($up_sql);
    $up_stmt->bind_param("di", $balance_after, $user_id);
    $up_stmt->execute();
    
    // Insert Logic for Mutation
    $mut_sql = "INSERT INTO wallet_mutations (user_id, type, amount, balance_before, balance_after, description, reference_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $mut_stmt = $conn->prepare($mut_sql);
    $mut_stmt->bind_param("isdddss", $user_id, $type, $amount, $balance_before, $balance_after, $description, $ref_id);
    $mut_stmt->execute();
    
    return true;
}

?>
