<?php
/**
 * BRI SNAP BI - Client: Create VA (transfer-va/create-va)
 *
 * Endpoint : https://{domain}/snap/v1.0/transfer-va/create-va
 * Method   : POST
 * Default  : sandbox.partner.api.bri.co.id
 *
 * Extends MY_Snap_bri (application/core/MY_Snap_bri.php) untuk shared logic:
 * fetchAccessToken, generateSignature, generateSignatureVA, json().
 */

require_once APPPATH . 'core/MY_Snap_bri.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');

class Snap_create_va extends MY_Snap_bri
{
    protected $vaEndpoint = '/snap/v1.0/transfer-va/create-va';

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
                'message' => 'BRI SNAP BI - Create VA endpoint aktif',
                'method'  => 'POST',
                'target'  => 'https://' . $this->baseDomain . $this->vaEndpoint,
                'note'    => 'POST body berisi parameter VA. Token BRI di-fetch otomatis.',
                'required_fields' => array('virtualAccountName', 'totalAmount'),
            ), 200);
        }

        if ($method !== 'post') {
            return $this->json(array('status' => 0, 'message' => 'Method not allowed'), 405);
        }

        // Baca raw JSON kalau content-type JSON (CI default $_POST cuma untuk form-urlencoded)
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

        // Validasi minimal
        // ponytail: JANGAN trim() value BRI — partnerServiceId left-padded space, numeric exact.
        $virtualAccountName = isset($input['virtualAccountName']) ? $input['virtualAccountName'] : '';
        $totalAmount        = isset($input['totalAmount'])        ? $input['totalAmount']        : '';
        if ($virtualAccountName === '' || $totalAmount === '') {
            return $this->json(array(
                'status'  => 0,
                'message' => 'virtualAccountName & totalAmount wajib diisi',
            ), 400);
        }

        $partnerServiceId   = isset($input['partnerServiceId'])   ? $input['partnerServiceId']   : $this->partnerServiceIdDefault;
        $customerNo         = isset($input['customerNo'])         ? $input['customerNo']         : (string) rand(100000000, 999999999);
        $virtualAccountNo   = isset($input['virtualAccountNo'])   ? $input['virtualAccountNo']   : ($partnerServiceId . $customerNo);
        $trxId              = isset($input['trxId'])              ? $input['trxId']              : ('TSLC ' . $customerNo);
        $expiredDate        = isset($input['expiredDate'])        ? $input['expiredDate']        : date('Y-m-d\TH:i:s', strtotime('+1 day')) . '+07:00';
        $additionalInfo     = isset($input['additionalInfo'])     ? $input['additionalInfo']     : ('Tagihan Sandbox Labschool Cibubur ' . $customerNo);

        // Step 1: ambil token BRI
        $tokenResp = $this->fetchAccessToken();
        if (empty($tokenResp['accessToken'])) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'Gagal dapat access token dari BRI',
                'bri'     => $tokenResp,
            ), 500);
        }
        $token = $tokenResp['accessToken'];

        // Step 2: panggil create-va
        $dataReq = array(
            'partnerServiceId'   => $partnerServiceId,
            'customerNo'         => $customerNo,
            'virtualAccountNo'   => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'trxId'              => $trxId,
            'totalAmount'        => array(
                'value'    => $totalAmount,
                'currency' => 'IDR',
            ),
            'expiredDate'        => $expiredDate,
            'additionalInfo'     => array(
                'description' => $additionalInfo,
            ),
        );

        $vaResp = $this->callVa($token, $dataReq, 'POST');

        $this->mymodel->insertid('bri_res', array(
            'text' => '[create_va] req=' . json_encode($dataReq) . ' resp=' . json_encode($vaResp),
        ));

        return $this->json(array(
            'status'  => 1,
            'message' => 'Berhasil request create VA ke BRI',
            'req'     => $dataReq,
            'token'   => $tokenResp,
            'bri'     => $vaResp,
        ), 200);
    }
}
