<?php
/**
 * BRI SNAP BI - Client: Update Status VA (transfer-va/update-status)
 *
 * Endpoint : https://{domain}/snap/v1.0/transfer-va/update-status
 * Method   : PUT
 * Default  : sandbox.partner.api.bri.co.id
 *
 * Sesuai spec BRI: paidStatus = "Y" (Paid) atau "N" (Not Paid).
 *
 * Input POST body:
 *   partnerServiceId   string  M
 *   customerNo         string  M
 *   virtualAccountNo   string  M
 *   trxId              string  M
 *   paidStatus         string  M  "Y" | "N"
 *
 * Token BRI di-fetch otomatis via MY_Snap_bri::fetchAccessToken().
 */

require_once APPPATH . 'core/MY_Snap_bri.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');

class Snap_update_status_va extends MY_Snap_bri
{
    protected $vaEndpoint = '/snap/v1.0/transfer-va/update-status';

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
                'message' => 'BRI SNAP BI - Update Status VA endpoint aktif',
                'method'  => 'PUT',
                'target'  => 'https://' . $this->baseDomain . $this->vaEndpoint,
                'note'    => 'PUT body berisi parameter VA + paidStatus. Token BRI di-fetch otomatis.',
                'required_fields' => array(
                    'partnerServiceId', 'customerNo', 'virtualAccountNo', 'trxId', 'paidStatus',
                ),
                'paidStatus_values' => array('Y' => 'Paid', 'N' => 'Not Paid'),
            ), 200);
        }

        // Accept PUT atau POST (beberapa client/proxy strip PUT)
        if ($method !== 'put' && $method !== 'post') {
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
        $missing = array();
        $partnerServiceId = isset($input['partnerServiceId']) ? $input['partnerServiceId'] : '';
        $customerNo       = isset($input['customerNo'])       ? $input['customerNo']       : '';
        $virtualAccountNo = isset($input['virtualAccountNo']) ? $input['virtualAccountNo'] : '';
        $trxId            = isset($input['trxId'])            ? $input['trxId']            : '';
        $paidStatus       = isset($input['paidStatus'])       ? strtoupper($input['paidStatus']) : '';

        if ($partnerServiceId === '') { $missing[] = 'partnerServiceId'; }
        if ($customerNo       === '') { $missing[] = 'customerNo'; }
        if ($virtualAccountNo === '') { $missing[] = 'virtualAccountNo'; }
        if ($trxId            === '') { $missing[] = 'trxId'; }
        if ($paidStatus       === '') { $missing[] = 'paidStatus'; }

        if (!empty($missing)) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'Field mandatory kosong',
                'missing' => $missing,
            ), 400);
        }

        // Validasi paidStatus harus Y atau N
        if ($paidStatus !== 'Y' && $paidStatus !== 'N') {
            return $this->json(array(
                'status'  => 0,
                'message' => 'paidStatus harus Y (Paid) atau N (Not Paid)',
                'received'=> $paidStatus,
            ), 400);
        }

        $dataReq = array(
            'partnerServiceId' => $partnerServiceId,
            'customerNo'       => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxId'            => $trxId,
            'paidStatus'       => $paidStatus,
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

        // Step 2: panggil update-status
        $vaResp = $this->callVa($tokenResp['accessToken'], $dataReq, 'PUT');

        $this->mymodel->insertid('bri_res', array(
            'text' => '[update_status_va] req=' . json_encode($dataReq) . ' resp=' . json_encode($vaResp),
        ));

        return $this->json(array(
            'status'  => 1,
            'message' => 'Berhasil request update status VA ke BRI',
            'req'     => $dataReq,
            'token'   => $tokenResp,
            'bri'     => $vaResp,
        ), 200);
    }
}
