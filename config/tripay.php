<?php
/**
 * Tripay Payment Gateway Config
 */
return [
    'mode'          => 'sandbox', // sandbox or production
    'merchant_code' => 'T12345',
    'api_key'       => 'DEV-TRIPAY-KEY',
    'private_key'   => 'TRIPAY-PRIVATE-KEY',
    'callback_url'  => 'https://domain.com/api/tripay-callback.php',
    'return_url'    => 'https://domain.com/user/transactions.php',
    'expired_time'  => 86400, // 24 hours
];
?>
