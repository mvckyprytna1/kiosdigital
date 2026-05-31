<?php
/**
 * Digiflazz Supplier Config
 */
return [
    'mode'                   => 'mock', // mock or live
    'username'               => 'digiusername',
    'api_key'                => 'digi-api-key',
    'endpoint_price_list'    => 'https://api.digiflazz.com/v1/price-list',
    'endpoint_transaction'   => 'https://api.digiflazz.com/v1/transaction',
    'endpoint_check_balance' => 'https://api.digiflazz.com/v1/cek-saldo',
    'callback_url'           => 'https://domain.com/api/digiflazz-callback.php'
];
?>
