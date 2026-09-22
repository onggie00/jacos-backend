<?php
/**
 * BRI SNAP BI - Client: Send Payment Notification (Intrabank)
 *
 * Target endpoint: https://{domain}/snap/v1.0/transfer-va/notify-payment-intrabank
 * Default domain : sandbox.partner.api.bri.co.id
 *
 * Method BRI: POST. Client signature: HMAC_SHA512 (simetris), stringToSign =
 *   method + ":" + endpoint + ":" + accessToken + ":" + sha256(body) + ":" + timestamp
 *
 * Response codes (service code "34"):
 *   2003400  Success
 *   4003400  Bad Request
 *   4003402  Invalid Mandatory Field
 *   4013400  Unauthorized. Verify Client Secret Fail
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type, authorization, x-timestamp, x-signature, x-partner-id, channel-id, x-external-id, x-token');
date_default_timezone_set('Asia/Jakarta');
define('PRIVATE_BRI_KEY', FCPATH . 'key_bri_sd_private.pem');
define('BRI_PUBLIC_KEY', FCPATH . 'key_bri_sd_public.pem');

defined('BASEPATH') OR exit('No direct script access allowed');

class Bri_snap_notify_payment_intrabank extends MY_Controller
{
    // BRI Sandbox (konsisten dengan Snap_bri_test.php)
    private $clientId     = 'bmf114bUB3BjHsJfRFqXtiafpcG6MMH5';
    private $clientSecret = 'ed59fvnJx3VsA4lc';
    private $partnerId    = 'labschool';
    private $channelId    = '00004';
    private $baseDomain   = 'sandbox.partner.api.bri.co.id';
    private $endpoint     = '/snap/v1.0/transfer-va/notify-payment-intrabank';

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
                'message' => 'BRI SNAP BI - Notify Payment Intrabank endpoint aktif',
                'method'  => 'POST',
                'target'  => 'https://' . $this->baseDomain . $this->endpoint,
                'headers_we_to_bri' => array(
                    'Authorization'  => 'Bearer {accessToken}',
                    'X-TIMESTAMP'   => 'ISO 8601 dgn millis + offset',
                    'X-SIGNATURE'   => 'base64(HMAC_SHA512(POST + ":" + endpoint + ":" + token + ":" + sha256(body) + ":" + timestamp))',
                    'X-PARTNER-ID'  => $this->partnerId,
                    'CHANNEL-ID'    => $this->channelId,
                    'X-EXTERNAL-ID' => 'unique per request',
                ),
                'body_sample' => array(
                    'partnerServiceId' => '   22216',
                    'customerNo'       => '123456789',
                    'virtualAccountNo' => '   22216123456789',
                    'trxDateTime'      => '2026-01-15T10:00:00.000+07:00',
                    'paymentRequestId' => 'PR123456',
                    'additionalInfo'   => array(
                        'paymentAmount' => array('value' => '100000.00', 'currency' => 'IDR'),
                        'terminalId'    => '1',
                        'bankId'        => '002',
                    ),
                ),
                'token_options' => 'Kirim token via header X-Token ATAU field body token. Kosongkan = auto-fetch dari /access-token/b2b.',
                'response_codes' => array(
                    '2003400' => 'Success',
                    '4003400' => 'Bad Request',
                    '4003402' => 'Invalid Mandatory Field',
                    '4013400' => 'Unauthorized',
                ),
                'note' => 'GET cuma untuk test koneksi. BRI sendiri mengirim POST.',
            ), 200);
        }

        if ($method !== 'post') {
            return $this->json(array('status' => 0, 'message' => 'Method not allowed'), 405);
        }

        // Deteksi BRI push notification (X-PARTNER-ID + X-SIGNATURE = BRI mengirim)
        $briHeaders = array();
        foreach (getallheaders() as $name => $value) {
            $briHeaders[strtolower($name)] = $value;
        }
        if (!empty($briHeaders['x-partner-id']) && !empty($briHeaders['x-signature'])) {
            return $this->handleBriPushNotification();
        }

        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        if (!$input) {
            return $this->json(array('status' => 0, 'message' => 'Invalid JSON body'), 400);
        }

        // Deteksi push notif payment VA dari BRI lewat BENTUK BODY (header signature
        // kadang tidak lengkap, sebelumnya body ini malah di-forward ke BRI dan
        // menghasilkan error 500 /v2/exclusive/bri_va).
        // paymentAmount skalar (string) = notif; array {value,currency} = sample forward manual.
        if (isset($input['virtualAccountNo'])
            && isset($input['additionalInfo']['paymentAmount'])
            && !is_array($input['additionalInfo']['paymentAmount'])) {
            return $this->handleNotificationBody($rawInput);
        }

        // Simpan dulu parameter ke bri_res (seperti Notification)
        $this->mymodel->insertid('bri_res', array('text' => '[request] ' . $rawInput));

        // Ambil token: dari header X-Token, atau field body token, atau ambil otomatis.
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        $token = isset($headers['X-Token']) ? $headers['X-Token'] : '';
        if (empty($token) && isset($input['token'])) {
            $token = $input['token'];
        }
        if (empty($token)) {
            $token = $this->fetchAccessToken();
        }
        if (empty($token)) {
            return $this->json(array('status' => 0, 'message' => 'Gagal ambil access token'), 500);
        }

        // Token hanya untuk sign, tidak ikut body. Body = field SNAP BI saja.
        unset($input['token']);
        $body = json_encode($input);

        $dt           = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $milliseconds = substr($dt->format('u'), 0, 3);
        $timestamp    = $dt->format('Y-m-d\TH:i:s') . '.' . $milliseconds . $dt->format('P');

        $signature = $this->generateSignatureVA('POST', $this->endpoint, $token, $body, $timestamp, $this->clientSecret);

        $reqHeaders = array(
            'Authorization: Bearer ' . $token,
            'X-TIMESTAMP: '  . $timestamp,
            'X-SIGNATURE: '  . $signature,
            'X-PARTNER-ID: ' . $this->partnerId,
            'CHANNEL-ID: '   . $this->channelId,
            'X-EXTERNAL-ID: ' . rand(100000000, 999999999),
            'Content-Type: application/json',
        );

        $url = 'https://' . $this->baseDomain . $this->endpoint;
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $reqHeaders);
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

        $this->mymodel->insertid('bri_res', array('text' => '[notif_post] ' . $response));

        return $this->json(array(
            'status'  => 1,
            'message' => 'Notifikasi terkirim',
            'url'     => $url,
            'data'    => json_decode($response, true),
        ), 200);
    }

    /**
     * Body POST berbentuk push notif payment VA dari BRI.
     * Delegate ke Bri_snap_notification::processBody (logic bisnis reuse).
     * ponytail: jalur ini tanpa verifikasi signature (deteksi dari bentuk body);
     * verifikasi penuh tetap lewat jalur header x-partner-id + x-signature.
     */
    private function handleNotificationBody($rawBody)
    {
        $this->mymodel->insertid('bri_res', array('text' => '[notif_bri_va] ' . $rawBody));

        require_once APPPATH . 'controllers/apiapp/Bri_snap_notification.php';
        $handler = new Bri_snap_notification();
        return $handler->processBody($rawBody);
    }

    /**
     * Handle push notification dari BRI.
     * Delegate ke Bri_snap_notification — verifikasi HMAC_SHA512 (simetris,
     * clientSecret) dilakukan di sana sesuai spec SNAP BI.
     * ponytail: verifikasi RSA (clientId|timestamp) dihapus — skema salah
     * untuk notify-payment-intrabank, bikin request valid ditolak 401.
     */
    private function handleBriPushNotification()
    {
        require_once APPPATH . 'controllers/apiapp/Bri_snap_notification.php';
        $handler = new Bri_snap_notification();
        return $handler->index();
    }

    private function fetchAccessToken()
    {
        $privateKey = file_get_contents(PRIVATE_BRI_KEY);
        $sig        = $this->generateSignature($this->clientId, $privateKey);
        $tokenUrl   = 'https://' . $this->baseDomain . '/snap/v1.0/access-token/b2b';

        $headers = array(
            'X-CLIENT-KEY: ' . $this->clientId,
            'X-TIMESTAMP: '  . $sig['timestamp'],
            'X-SIGNATURE: '  . $sig['signature'],
            'Content-Type: application/json',
        );
        $body = json_encode(array('grantType' => 'client_credentials'));

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $resp = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($resp, true);
        return isset($decoded['accessToken']) ? $decoded['accessToken'] : '';
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

    private function generateSignatureVA($method, $endpoint, $token, $body, $timestamp, $clientSecret)
    {
        $hashedBody   = hash('sha256', $body);
        $stringToSign = $method . ':' . $endpoint . ':' . $token . ':' . $hashedBody . ':' . $timestamp;
        return base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));
    }

    private function json($data, $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output($data === null ? '' : json_encode($data));
    }
}
