<?php
/**
 * BRI SNAP BI - Client: Inquiry VA (transfer-va/inquiry-va)
 *
 * Endpoint : https://{domain}/snap/v1.0/transfer-va/inquiry-va
 * Method   : POST
 * Default  : sandbox.partner.api.bri.co.id
 *
 * Sesuai spec BRI: semua field Mandatory (M).
 *
 * Input POST body:
 *   partnerServiceId   string  M  8 char left-padded space, numeric
 *   customerNo         string  M  numeric, sampai 20 digit
 *   virtualAccountNo   string  M  28 char, partnerServiceId(8, 0-pad) + customerNo
 *   trxId              string  M  alphanumeric, sampai 64 char
 *
 * Token BRI di-fetch otomatis via MY_Snap_bri::fetchAccessToken().
 */

require_once APPPATH . 'core/MY_Snap_bri.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');

class Snap_inquiry_va extends MY_Snap_bri
{
    protected $vaEndpoint = '/snap/v1.0/transfer-va/inquiry-va';

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
                'message' => 'BRI SNAP BI - Inquiry VA endpoint aktif',
                'method'  => 'POST',
                'target'  => 'https://' . $this->baseDomain . $this->vaEndpoint,
                'note'    => 'POST body berisi partnerServiceId, customerNo, virtualAccountNo, trxId. Token BRI di-fetch otomatis.',
                'required_fields' => array(
                    'partnerServiceId', 'customerNo', 'virtualAccountNo', 'trxId',
                ),
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
        $trxId              = isset($input['trxId'])              ? $input['trxId']              : '';

        if ($partnerServiceId === '') { $missing[] = 'partnerServiceId'; }
        if ($customerNo       === '') { $missing[] = 'customerNo'; }
        if ($virtualAccountNo === '') { $missing[] = 'virtualAccountNo'; }
        if ($trxId            === '') { $missing[] = 'trxId'; }

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
            'trxId'            => $trxId,
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

        // Step 2: panggil inquiry-va
        $vaResp = $this->callVa($tokenResp['accessToken'], $dataReq, 'POST');

        $this->mymodel->insertid('bri_res', array(
            'text' => '[inquiry_va] req=' . json_encode($dataReq) . ' resp=' . json_encode($vaResp),
        ));

        return $this->json(array(
            'status'  => 1,
            'message' => 'Berhasil request inquiry VA ke BRI',
            'req'     => $dataReq,
            'token'   => $tokenResp,
            'bri'     => $vaResp,
        ), 200);
    }
}
