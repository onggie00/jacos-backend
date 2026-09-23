<?php
/**
 * BRI SNAP BI - Access Token Endpoint (B2B)
 *
 * Endpoint: POST /snap/v1.0/access-token/b2b
 * Purpose : Expose endpoint untuk BRI request access token.
 *            BRI akan kirim POST dengan header X-CLIENT-KEY, X-TIMESTAMP, X-SIGNATURE.
 *            Signature BRI bersifat ASIMETRIS (SHA256withRSA) — diverifikasi pakai
 *            public key BRI (key_bri_public.pem di FCPATH).
 *            String yang ditandatangani: clientId + "|" + timestamp
 *            (pola sama seperti generateSignature() di Snap_bri_test.php tapi DIBALIK:
 *             di sana kita generate untuk memanggil BRI, di sini kita VERIFY milik BRI).
 *
 * Response codes (service code "34" untuk B2B / Virtual Account):
 *   2003400  Success
 *   4003400  Bad Request
 *   4003401  Invalid Field Format
 *   4003402  Invalid Mandatory Field
 *   4013400  Unauthorized. Verify Client Secret Fail
 *   4043416  Partner Not Found
 *   5003400  General Error
 *   5003402  Unknown Error
 *   5043400  Timeout
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, x-client-key, x-timestamp, x-signature, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');

class Bri_snap_token_get extends MY_Controller
{
    // BRI SNAP clientId dari tabel pengaturan_akun (bri_snap_client_id)
    private $clientId     = '';
    private $partnerId    = 'labschool';
    private $publicKeyPath = 'key_bri_public.pem';

    // Default access token TTL (detik). BRI biasanya 900 (15 menit).
    private $tokenTtl = 900;

    public function __construct()
    {
        parent::__construct();
        $this->clientId = $this->briSnapSetting('bri_snap_client_id');
    }

    private function briSnapSetting($name)
    {
        $row = $this->mymodel->getbywhere('pengaturan_akun', 'name_setting', $name, 'row');
        return $row ? $row->value : '';
    }

    public function index()
    {
        $rawBody = file_get_contents('php://input');

        // 1. Log raw request untuk debug (konsisten dengan Notification.php lama)
        $this->mymodel->insertid('bri_res', array('text' => '[token_get] ' . $rawBody));

        // 2. Kumpulkan header
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        $clientKey   = isset($headers['X-CLIENT-KEY']) ? $headers['X-CLIENT-KEY'] : '';
        $timestamp   = isset($headers['X-TIMESTAMP'])  ? $headers['X-TIMESTAMP']  : '';
        $signature   = isset($headers['X-SIGNATURE'])  ? $headers['X-SIGNATURE']  : '';

        // 3. Validasi wajib header
        if (empty($clientKey) || empty($timestamp) || empty($signature)) {
            return $this->respondError('4013400', 'Unauthorized. Verify Client Secret Fail');
        }

        // 4. Validasi clientId cocok
        if (trim($clientKey) !== $this->clientId) {
            return $this->respondError('4013400', 'Unauthorized. Verify Client Secret Fail');
        }

        // 5. Decode body — minimal cek ada grantType
        $bodyJson = json_decode($rawBody, true);
        if (!$bodyJson || !isset($bodyJson['grantType']) || $bodyJson['grantType'] !== 'client_credentials') {
            return $this->respondError('4003400', 'Bad Request. Invalid grantType');
        }

        // 6. Verifikasi signature BRI (asimetris, SHA256withRSA)
        if (!$this->verifyBriSignature($clientKey, $timestamp, $signature)) {
            return $this->respondError('4013400', 'Unauthorized. Verify Client Secret Fail');
        }

        // 7. Generate access token sederhana.
        //    Untuk sandbox: gunakan random base64-url string. Simpan di memory (tidak persist).
        //    Untuk production: ganti dengan JWT signed atau token table.
        $accessToken = $this->generateAccessToken();

        $msg = array(
            'responseCode'    => '2003400',
            'responseMessage' => 'Successful',
            'accessToken'     => $accessToken,
            'tokenType'       => 'Bearer',
            'expiresIn'       => $this->tokenTtl,
        );

        $this->logResponse('token_get_ok', $msg);
        $this->response($msg, 200);
    }

    /**
     * Verifikasi signature BRI (asimetris).
     * StringToSign = clientId + "|" + timestamp
     * Signature    = base64( RSA_sign_SHA256( stringToSign, privateKey_BRI ) )
     * Kita verify pakai publicKey BRI yang dikirim via email sandbox.
     */
    private function verifyBriSignature($clientId, $timestamp, $signatureBase64)
    {
        $fullPath = FCPATH . $this->publicKeyPath;
        if (!file_exists($fullPath)) {
            // Tanpa public key kita TIDAK bisa verify dengan benar.
            // Untuk sandbox: tetap tolak agar tidak expose token sembarangan.
            return false;
        }

        $publicKey = openssl_pkey_get_public(file_get_contents($fullPath));
        if (!$publicKey) {
            return false;
        }

        $stringToSign = $clientId . '|' . $timestamp;
        $signature    = base64_decode($signatureBase64);

        $result = openssl_verify($stringToSign, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        // $result = 1 valid, 0 invalid, -1 error
        return ($result === 1);
    }

    /**
     * Generate access token untuk dipakai BRI call endpoint payment-notification.
     * Untuk sandbox: random base64-url string, cukup untuk validasi Bearer di notifikasi.
     * Untuk production: pertimbangkan JWT signed dengan expiry claim.
     */
    private function generateAccessToken()
    {
        // Pakai wrapper CI get_random_bytes (kompat PHP 5.x) + bin2hex agar base64-url safe.
        $bytes = $this->security->get_random_bytes(40);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    private function respondError($code, $message)
    {
        $msg = array(
            'responseCode'    => $code,
            'responseMessage' => $message,
        );
        $this->logResponse('token_get_err', $msg);
        $this->response($msg, 200);
    }

    private function logResponse($tag, $data)
    {
        $this->mymodel->insertid('bri_res', array('text' => '[' . $tag . '] ' . json_encode($data)));
    }
}
