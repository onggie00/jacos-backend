<?php
/**
 * BRI SNAP BI - Client: Get Access Token (B2B)
 *
 * Salinan berdiri sendiri dari Bri_snap_access_token_b2b (apiapp/) agar caller
 * bisa import/panggil path spesifik ini tanpa tergantung route lama.
 *
 * Target endpoint: https://{domain}/snap/v1.0/access-token/b2b
 * Default domain : sandbox.partner.api.bri.co.id
 *
 * Method BRI: POST. Client signature: SHA256withRSA, stringToSign = clientId + "|" + timestamp.
 *
 * Response codes (service code "34"):
 *   2003400  Success
 *   4003400  Bad Request
 *   4013400  Unauthorized. Verify Client Secret Fail
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type, x-client-key, x-timestamp, x-signature');
date_default_timezone_set('Asia/Jakarta');
// ponytail: fallback path, pindah ke config kalau perlu multi-env
$_pem_candidates = array(FCPATH, APPPATH . '..' . DIRECTORY_SEPARATOR);
foreach ($_pem_candidates as $_dir) {
    if (file_exists($_dir . 'key_bri_sd_private.pem') && file_exists($_dir . 'key_bri_sd_public.pem')) {
        define('PRIVATE_BRI_KEY', $_dir . 'key_bri_sd_private.pem');
        define('BRI_PUBLIC_KEY', $_dir . 'key_bri_sd_public.pem');
        break;
    }
}
if (!defined('PRIVATE_BRI_KEY')) {
    // Dummy agar tidak fatal error saat define; handler di bawah cek file_exists
    define('PRIVATE_BRI_KEY', FCPATH . 'key_bri_sd_private.pem');
    define('BRI_PUBLIC_KEY', FCPATH . 'key_bri_sd_public.pem');
}

defined('BASEPATH') OR exit('No direct script access allowed');

class Snap_token_request extends MY_Controller
{
    // BRI Sandbox (konsisten dengan Snap_bri_test.php)
    private $clientId   = 'bmf114bUB3BjHsJfRFqXtiafpcG6MMH5';
    private $baseDomain = 'sandbox.partner.api.bri.co.id';
    private $endpoint   = '/snap/v1.0/access-token/b2b';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $method = $this->input->method();

        if ($method === 'options') {
            return $this->json(null, 200);
        }

        if ($method === 'get') {
            return $this->json(array(
                'status'  => 1,
                'message' => 'BRI SNAP BI - Access Token B2B endpoint aktif',
                'method'  => 'POST',
                'target'  => 'https://' . $this->baseDomain . $this->endpoint,
                'headers_bri_to_us' => array(
                    'X-CLIENT-KEY' => 'clientId BRI',
                    'X-TIMESTAMP'  => 'ISO 8601 dgn millis + offset',
                    'X-SIGNATURE'  => 'base64(RSA_SHA256(clientId + "|" + timestamp))',
                ),
                'body' => array('grantType' => 'client_credentials'),
                'response_codes' => array(
                    '2003400' => 'Success',
                    '4003400' => 'Bad Request',
                    '4013400' => 'Unauthorized',
                ),
                'note' => 'GET cuma untuk test koneksi. BRI sendiri mengirim POST.',
            ), 200);
        }

        if ($method !== 'post') {
            return $this->json(array('status' => 0, 'message' => 'Method not allowed'), 405);
        }

        $url = 'https://' . $this->baseDomain . $this->endpoint;

        // Deteksi BRI push notification (X-PARTNER-ID + X-SIGNATURE = BRI mengirim)
        $briHeaders = array();
        foreach (getallheaders() as $name => $value) {
            $briHeaders[strtolower($name)] = $value;
        }
        if (!empty($briHeaders['x-partner-id']) && !empty($briHeaders['x-signature'])) {
            return $this->handleBriPushNotification();
        }

        if (!file_exists(PRIVATE_BRI_KEY)) {
            return $this->json(array(
                'responseCode' => '4013400',
                'responseMessage' => 'Unauthorized. Private key file not found on server',
            ), 401);
        }
        $privateKey = file_get_contents(PRIVATE_BRI_KEY);
        try {
            $sig = $this->generateSignature($this->clientId, $privateKey);
        } catch (Exception $e) {
            return $this->json(array(
                'responseCode' => '4013400',
                'responseMessage' => 'Unauthorized. ' . $e->getMessage(),
            ), 401);
        }
        $timestamp = $sig['timestamp'];
        $signature = $sig['signature'];
        $headers = array(
            'X-CLIENT-KEY: ' . $this->clientId,
            'X-TIMESTAMP: '  . $timestamp,
            'X-SIGNATURE: '  . $signature,
            'Content-Type: application/json',
        );

        $body = json_encode(array('grantType' => 'client_credentials'));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            return $this->json(array('status' => 0, 'message' => $err), 500);
        }
        curl_close($ch);

        $this->mymodel->insertid('bri_res', array('text' => '[token_post] ' . $response));

        // Forward langsung response BRI sesuai standar SNAP
        $briResponse = json_decode($response, true);
        $httpCode = isset($briResponse['responseCode']) ? 401 : 200;
        return $this->json($briResponse, $httpCode);
    }

    /**
     * Handle push notification dari BRI.
     * Verifikasi RSA signature, lalu delegate ke Bri_snap_notification.
     */
    private function handleBriPushNotification()
    {
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            $headers[strtolower($name)] = $value;
        }

        $timestamp = isset($headers['x-timestamp']) ? $headers['x-timestamp'] : '';
        $signature = isset($headers['x-signature']) ? $headers['x-signature'] : '';

        // Verifikasi RSA signature dari BRI
        if (!file_exists(BRI_PUBLIC_KEY)) {
            return $this->json(array('responseCode' => '4013400', 'responseMessage' => 'Unauthorized. Public key file not found: ' . BRI_PUBLIC_KEY), 200);
        }
        $stringToSign = $this->clientId . '|' . $timestamp;
        $publicKey = openssl_pkey_get_public(file_get_contents(BRI_PUBLIC_KEY));
        if (!$publicKey) {
            return $this->json(array('responseCode' => '4013400', 'responseMessage' => 'Unauthorized. Invalid Public Key'), 200);
        }
        $verifyResult = openssl_verify($stringToSign, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256);
        if ($verifyResult !== 1) {
            return $this->json(array('responseCode' => '4013400', 'responseMessage' => 'Unauthorized. Signature Verification Failed'), 200);
        }

        // Delegate ke Bri_snap_notification (reuse logic bisnis)
        require_once APPPATH . 'controllers/apiapp/Bri_snap_notification.php';
        $handler = new Bri_snap_notification();
        return $handler->index();
    }

    private function generateSignature($clientId, $privateKeyPem)
    {
        $dt           = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $milliseconds = substr($dt->format('u'), 0, 3);
        $timestamp    = $dt->format('Y-m-d\TH:i:s') . '.' . $milliseconds . $dt->format('P');

        $stringToSign = $clientId . '|' . $timestamp;
        $privateKey   = openssl_pkey_get_private($privateKeyPem);
        if (!$privateKey) {
            throw new Exception('Invalid private key');
        }

        openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return array(
            'timestamp'      => $timestamp,
            'signature'      => base64_encode($signature),
            'string_to_sign' => $stringToSign,
        );
    }

    private function json($data, $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
