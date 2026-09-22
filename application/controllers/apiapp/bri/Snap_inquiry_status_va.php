<?php
/**
 * BRI SNAP BI - Client: Inquiry Status VA (transfer-va/status)
 *
 * Endpoint : https://{domain}/snap/v1.0/transfer-va/status
 * Method   : POST
 * Default  : sandbox.partner.api.bri.co.id
 *
 * Sesuai spec BRI: semua field Mandatory (M).
 *
 * Input POST body:
 *   partnerServiceId    string  M  8 char left-padded space, numeric
 *   customerNo          string  M  numeric, sampai 20 digit
 *   virtualAccountNo    string  M  28 char, partnerServiceId(8, 0-pad) + customerNo
 *   inquiryRequestId    string  M  alphanumeric, sampai 128 char
 *                                 (UUID/acuan unik dari partner untuk identifikasi inquiry.
 *                                  Wajib diisi; kalau kosong, server auto-generate UUIDv4
 *                                  sebagai fallback. Konfirmasi ke bank: cek pertanyaan.md.)
 *
 * Token BRI di-fetch otomatis via MY_Snap_bri::fetchAccessToken().
 */

require_once APPPATH . 'core/MY_Snap_bri.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');

class Snap_inquiry_status_va extends MY_Snap_bri
{
    protected $vaEndpoint = '/snap/v1.0/transfer-va/status';

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
                'message' => 'BRI SNAP BI - Inquiry Status VA endpoint aktif',
                'method'  => 'POST',
                'target'  => 'https://' . $this->baseDomain . $this->vaEndpoint,
                'note'    => 'POST body berisi partnerServiceId, customerNo, virtualAccountNo, inquiryRequestId. Token BRI di-fetch otomatis.',
                'required_fields' => array(
                    'partnerServiceId', 'customerNo', 'virtualAccountNo', 'inquiryRequestId',
                ),
                'inquiryRequestId_note' => 'Wajib diisi partner. Kalau kosong, server auto-generate UUIDv4 sebagai fallback (lihat Berkas_sample/ai-prompt/pertanyaan.md).',
            ), 200);
        }

        if ($method !== 'post') {
            return $this->json(array('status' => 0, 'message' => 'Method not allowed'), 405);
        }

        // Baca raw JSON kalau content-type JSON
        $rawBody = file_get_contents('php://input');
        $input   = array();
        if ($rawBody !== '' && $rawBody !== NULL) {
            $decoded = json_decode($rawBody, true);
            if (is_array($decoded)) {
                $input = $decoded;
            }
        }
        if (empty($input)) {
            $input = $this->input->post(NULL, true);
            if (!is_array($input)) {
                $input = array();
            }
        }

        // Validasi field mandatory
        // ponytail: JANGAN trim() value BRI — partnerServiceId left-padded space, numeric exact.
        $missing            = array();
        $partnerServiceId   = isset($input['partnerServiceId'])   ? $input['partnerServiceId']   : '';
        $customerNo         = isset($input['customerNo'])         ? $input['customerNo']         : '';
        $virtualAccountNo   = isset($input['virtualAccountNo'])   ? $input['virtualAccountNo']   : '';
        $inquiryRequestId   = isset($input['inquiryRequestId'])   ? $input['inquiryRequestId']   : '';

        if ($partnerServiceId === '') { $missing[] = 'partnerServiceId'; }
        if ($customerNo       === '') { $missing[] = 'customerNo'; }
        if ($virtualAccountNo === '') { $missing[] = 'virtualAccountNo'; }
        if ($inquiryRequestId === '') {
            // fallback: auto-generate UUIDv4 jika caller tidak sediakan
            $inquiryRequestId = $this->generateUuidV4();
        }

        if (!empty($missing)) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'Field mandatory kosong',
                'missing' => $missing,
            ), 400);
        }

        $dataReq = array(
            'partnerServiceId' => $partnerServiceId,
            'customerNo'       => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'inquiryRequestId' => $inquiryRequestId,
        );

        // Step 1: ambil token BRI
        $tokenResp = $this->fetchAccessToken();
        if (empty($tokenResp['accessToken'])) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'Gagal dapat access token dari BRI',
                'bri'     => $tokenResp,
            ), 500);
        }

        // Step 2: panggil inquiry-status
        $vaResp = $this->callVa($tokenResp['accessToken'], $dataReq, 'POST');

        $this->mymodel->insertid('bri_res', array(
            'text' => '[inquiry_status_va] req=' . json_encode($dataReq) . ' resp=' . json_encode($vaResp),
        ));

        return $this->json(array(
            'status'  => 1,
            'message' => 'Berhasil request inquiry status VA ke BRI',
            'req'     => $dataReq,
            'token'   => $tokenResp,
            'bri'     => $vaResp,
        ), 200);
    }

    /**
     * Generate UUIDv4 (RFC 4122).
     * Format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx (36 char, alphanumeric + dash).
     * Masuk spec BRI: alphanumeric, length maks 128 char.
     *
     * ponytail: pakai random_bytes + sprintf hex, kompat PHP 5.x (tanpa random_bytes PHP 7).
     * Fallback ke mt_rand kalau random_bytes tidak tersedia.
     */
    private function generateUuidV4()
    {
        $bytes = '';
        if (function_exists('random_bytes')) {
            $bytes = random_bytes(16);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(16);
        } else {
            // fallback terakhir: mt_rand — kualitas rendah tapi cukup untuk fallback
            for ($i = 0; $i < 16; $i++) {
                $bytes .= chr(mt_rand(0, 255));
            }
        }
        // Set version (4) dan variant (RFC 4122)
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);

        return sprintf(
            '%02x%02x%02x%02x-%02x%02x-%02x%02x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            ord($bytes[0]), ord($bytes[1]), ord($bytes[2]), ord($bytes[3]),
            ord($bytes[4]), ord($bytes[5]), ord($bytes[6]), ord($bytes[7]),
            ord($bytes[8]), ord($bytes[9]), ord($bytes[10]), ord($bytes[11]),
            ord($bytes[12]), ord($bytes[13]), ord($bytes[14]), ord($bytes[15])
        );
    }
}
