<?php
/**
 * Tripay Payment Gateway Wrapper
 * KiosDigital PPOB
 */

class TripayAPI {
    private $config;
    private $api_key;
    private $private_key;
    private $merchant_code;
    private $base_url;

    public function __construct() {
        $this->config = include(__DIR__ . '/../config/tripay.php');
        $this->api_key = $this->config['api_key'];
        $this->private_key = $this->config['private_key'];
        $this->merchant_code = $this->config['merchant_code'];
        $this->base_url = $this->config['mode'] == 'production' 
            ? 'https://tripay.co.id/api/' 
            : 'https://tripay.co.id/api-sandbox/';
    }

    public function getChannels() {
        $payload = [];
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url . 'merchant/payment-channel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->api_key],
            CURLOPT_FAILONERROR => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        return $response ? json_decode($response, true) : ['success' => false, 'message' => $error];
    }

    public function createTransaction($data) {
        $signature = hash_hmac('sha256', $this->merchant_code . $data['merchant_ref'] . $data['amount'], $this->private_key);

        $payload = [
            'method'         => $data['method'],
            'merchant_ref'   => $data['merchant_ref'],
            'amount'         => $data['amount'],
            'customer_name'  => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'order_items'    => $data['order_items'],
            'callback_url'   => $this->config['callback_url'],
            'return_url'     => $this->config['return_url'],
            'expired_time'   => (time() + $this->config['expired_time']),
            'signature'      => $signature
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url . 'transaction/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->api_key],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_FAILONERROR => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        return $response ? json_decode($response, true) : ['success' => false, 'message' => $error];
    }

    public function validateCallback($raw_payload, $signature) {
        $local_signature = hash_hmac('sha256', $raw_payload, $this->private_key);
        return hash_equals($local_signature, $signature);
    }
}
?>
