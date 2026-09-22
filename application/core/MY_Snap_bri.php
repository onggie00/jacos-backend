<?php
/**
 * Base class untuk BRI SNAP BI client controllers (folder apiapp/bri/).
 *
 * Hanya di-load oleh controller di folder apiapp/bri/ (require_once manual
 * di awal file). CI3 tidak auto-load MY_* selain MY_Controller.
 *
 * Responsibility:
 *   - BRI sandbox credentials & endpoint constants
 *   - Fetch access token (client_credentials + RSA SHA256 signature)
 *   - Signature helpers (RSA SHA256 untuk token, HMAC SHA512 untuk VA request)
 *   - JSON response helper
 *
 * Child class (Snap_create_va / Snap_update_va / Snap_update_status_va):
 *   - Tentukan property $vaEndpoint (path SNAP)
 *   - Implement callVa($token, $data, $verb) sesuai shape body masing-masing
 *   - Index() sesuai validasi field mandatory dari spec BRI
 */

date_default_timezone_set('Asia/Jakarta');
// ponytail: fallback path, pindah ke config kalau perlu multi-env
$_pem_candidates = array(FCPATH, APPPATH . '..' . DIRECTORY_SEPARATOR);
foreach ($_pem_candidates as $_dir) {
    if (file_exists($_dir . 'key_bri_sd_private.pem')) {
        define('PRIVATE_BRI_KEY', $_dir . 'key_bri_sd_private.pem');
        break;
    }
}
if (!defined('PRIVATE_BRI_KEY')) {
    define('PRIVATE_BRI_KEY', FCPATH . 'key_bri_sd_private.pem');
}

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Snap_bri extends MY_Controller
{
    // BRI Sandbox credentials (konsisten dengan Snap_bri_test.php)
    protected $clientId     = 'bmf114bUB3BjHsJfRFqXtiafpcG6MMH5';
    protected $clientSecret = 'ed59fvnJx3VsA4lc';
    protected $partnerId    = 'labschool';
    protected $channelId    = '00004';

    protected $baseDomain    = 'sandbox.partner.api.bri.co.id';
    protected $tokenEndpoint = '/snap/v1.0/access-token/b2b';
    protected $partnerServiceIdDefault = '   22216';

    // Child WAJIB override $vaEndpoint dengan path SNAP yang dituju
    protected $vaEndpoint = NULL;

    /**
     * Ambil access token BRI (client_credentials + RSA SHA256 signature).
     * Return: array hasil json_decode response BRI (berisi accessToken, tokenType, expiresIn, ...)
     */
    protected function fetchAccessToken()
    {
        $url = 'https://' . $this->baseDomain . $this->tokenEndpoint;

        if (!file_exists(PRIVATE_BRI_KEY)) {
            return array('responseCode' => '4013400', 'responseMessage' => 'Private key not found');
        }
        $privateKey = file_get_contents(PRIVATE_BRI_KEY);
        $sig        = $this->generateSignature($this->clientId, $privateKey);
        $headers    = array(
            'X-CLIENT-KEY: ' . $this->clientId,
            'X-TIMESTAMP: '  . $sig['timestamp'],
            'X-SIGNATURE: '  . $sig['signature'],
            'Content-Type: application/json',
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('grantType' => 'client_credentials')));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return array('responseCode' => '5003400', 'responseMessage' => curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Panggil VA endpoint BRI dengan verb + body tertentu.
     * Return: array hasil json_decode response BRI.
     */
    protected function callVa($token, $data, $verb = 'POST')
    {
        $url      = 'https://' . $this->baseDomain . $this->vaEndpoint;
        $body     = json_encode($data);
        $endpoint = $this->vaEndpoint;

        // timestamp untuk signature VA
        $dt           = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $milliseconds = substr($dt->format('u'), 0, 3);
        $timestamp    = $dt->format('Y-m-d\TH:i:s') . '.' . $milliseconds . $dt->format('P');

        $signature = $this->generateSignatureVA(strtoupper($verb), $endpoint, $token, $body, $timestamp, $this->clientSecret);

        $headers = array(
            'Authorization: Bearer ' . $token,
            'X-TIMESTAMP: '    . $timestamp,
            'X-SIGNATURE: '    . $signature,
            'X-PARTNER-ID: '   . $this->partnerId,
            'CHANNEL-ID: '     . $this->channelId,
            'X-EXTERNAL-ID: '  . rand(100000000, 999999999),
            'Content-Type: application/json',
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($verb));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return array('responseCode' => '5003400', 'responseMessage' => curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Generate signature untuk request token BRI.
     * StringToSign = clientId + "|" + timestamp
     * Signature    = base64( RSA_sign_SHA256( stringToSign, privateKey ) )
     */
    protected function generateSignature($clientId, $privateKeyPem)
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

    /**
     * Generate signature untuk request VA (POST/PUT/DELETE).
     * StringToSign = verb + ":" + endpoint + ":" + token + ":" + sha256(body) + ":" + timestamp
     * Signature    = base64( HMAC_SHA512( stringToSign, clientSecret ) )
     */
    protected function generateSignatureVA($method, $endpoint, $token, $body, $timestamp, $clientSecret)
    {
        $hashedBody  = hash('sha256', $body);
        $stringToSign = $method . ':' . $endpoint . ':' . $token . ':' . $hashedBody . ':' . $timestamp;

        return base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));
    }

    /**
     * JSON response helper. Set header + return JSON body.
     */
    protected function json($data, $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
