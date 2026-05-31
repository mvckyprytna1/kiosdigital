<?php
/**
 * Digiflazz API Wrapper
 * KiosDigital PPOB
 */

class DigiflazzAPI {
    private $config;
    private $username;
    private $api_key;
    private $mode;

    public function __construct() {
        $this->config = include(__DIR__ . '/../config/digiflazz.php');
        $this->username = $this->config['username'];
        $this->api_key = $this->config['api_key'];
        $this->mode = $this->config['mode'];
    }

    private function generateSign($command) {
        return md5($this->username . $this->api_key . $command);
    }

    public function getBalance() {
        if ($this->mode == 'mock') {
            return ['data' => ['deposit' => 10000000]];
        }

        $payload = [
            'cmd' => 'deposit',
            'username' => $this->username,
            'sign' => $this->generateSign('depo')
        ];

        return $this->request($this->config['endpoint_check_balance'], $payload);
    }

    public function getPriceList($category = null) {
        if ($this->mode == 'mock') {
            // Return some dummy data for mock mode
            return [
                'data' => [
                    ['product_name' => 'Pulsa Telkomsel 5rb', 'category' => 'Pulsa', 'brand' => 'Telkomsel', 'seller_name' => 'Telkomsel', 'price' => 5200, 'buyer_sku_code' => 'tsel5', 'buyer_product_status' => true, 'seller_product_status' => true, 'type' => 'Umum'],
                    ['product_name' => 'Pulsa Indosat 10rb', 'category' => 'Pulsa', 'brand' => 'Indosat', 'seller_name' => 'Indosat', 'price' => 10100, 'buyer_sku_code' => 'isat10', 'buyer_product_status' => true, 'seller_product_status' => true, 'type' => 'Umum']
                ]
            ];
        }

        $payload = [
            'cmd' => 'prepaid',
            'username' => $this->username,
            'sign' => $this->generateSign('pricelist')
        ];

        return $this->request($this->config['endpoint_price_list'], $payload);
    }

    public function createTransaction($sku, $customer_no, $ref_id) {
        if ($this->mode == 'mock') {
            return [
                'data' => [
                    'ref_id' => $ref_id,
                    'status' => 'Success',
                    'sn' => 'SN' . rand(1000000, 9999999),
                    'message' => 'Transaksi Berhasil (Mock)'
                ]
            ];
        }

        $payload = [
            'username' => $this->username,
            'buyer_sku_code' => $sku,
            'customer_no' => $customer_no,
            'ref_id' => $ref_id,
            'sign' => $this->generateSign($ref_id)
        ];

        return $this->request($this->config['endpoint_transaction'], $payload);
    }

    private function request($url, $payload) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_FAILONERROR => false
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return json_decode($response, true);
    }
}
?>
