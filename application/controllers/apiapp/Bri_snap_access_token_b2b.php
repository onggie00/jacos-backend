<?php
/**
 * BRI SNAP BI - Client: Get Access Token (B2B)
 *
 * Target endpoint: https://{domain}/snap/v1.0/access-token/b2b
 * Default domain : sandbox.partner.api.bri.co.id
 * Method BRI     : POST, signature SHA256withRSA
 *                  stringToSign = clientId + "|" + timestamp
 *
 * Extends MY_Snap_bri (application/core/MY_Snap_bri.php) untuk shared logic:
 * credentials, fetchAccessToken, generateSignature, json().
 */

require_once APPPATH . 'core/MY_Snap_bri.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');

class Bri_snap_access_token_b2b extends MY_Snap_bri
{
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
                'target'  => 'https://' . $this->baseDomain . $this->tokenEndpoint,
                'headers_bri_to_us' => array(
                    'X-CLIENT-KEY' => 'clientId BRI',
                    'X-TIMESTAMP'  => 'ISO 8601 dengan millis + offset',
                    'X-SIGNATURE'  => 'base64(RSA_SHA256(clientId + "|" + timestamp))',
                ),
                'body' => array('grantType' => 'client_credentials'),
                'response_codes' => array(
                    '2003400' => 'Success',
                    '4003400' => 'Bad Request',
                    '4013400' => 'Unauthorized',
                ),
                'note' => 'GET hanya untuk test koneksi. BRI menggunakan POST.',
            ), 200);
        }

        if ($method !== 'post') {
            return $this->json(array('status' => 0, 'message' => 'Method not allowed'), 405);
        }

        // fetchAccessToken() di MY_Snap_bri: RSA sign + POST ke BRI,
        // return array hasil json_decode (atau array responseCode utk error).
        $decoded = $this->fetchAccessToken();

        $this->mymodel->insertid(
            'bri_res',
            array('text' => '[token_post] ' . json_encode($decoded))
        );

        // Teruskan semangat http code BRI: 401xx00 = Unauthorized, lainnya 200.
        $code = 200;
        if (isset($decoded['responseCode']) && substr($decoded['responseCode'], 0, 3) === '401') {
            $code = 401;
        }

        return $this->json($decoded, $code);
    }
}
