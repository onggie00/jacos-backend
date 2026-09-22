<?php
/**
 * BRI SNAP BI - Client: Delete VA (transfer-va/delete-va)
 *
 * Endpoint : https://{domain}/snap/v1.0/transfer-va/delete-va
 * Method   : DELETE
 * Default  : sandbox.partner.api.bri.co.id
 *
 * Sesuai spec BRI:
 *   partnerServiceId   M  8 char left-padded space, numeric
 *   customerNo         M  numeric, sampai 20 digit
 *   virtualAccountNo   M  28 char, partnerServiceId(8, 0-pad) + customerNo
 *   trxId              O  alphanumeric, sampai 64 char (tidak dikirim kalau kosong)
 *
 * Token BRI di-fetch otomatis via MY_Snap_bri::fetchAccessToken().
 */

require_once APPPATH . 'core/MY_Snap_bri.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, DELETE, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');

class Snap_delete_va extends MY_Snap_bri
{
    protected $vaEndpoint = '/snap/v1.0/transfer-va/delete-va';

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
                'message' => 'BRI SNAP BI - Delete VA endpoint aktif',
                'method'  => 'DELETE',
                'target'  => 'https://' . $this->baseDomain . $this->vaEndpoint,
                'note'    => 'DELETE body berisi partnerServiceId, customerNo, virtualAccountNo (trxId opsional). Token BRI di-fetch otomatis.',
                'required_fields' => array('partnerServiceId', 'customerNo', 'virtualAccountNo'),
                'optional_fields' => array('trxId'),
            ), 200);
        }

        // Accept DELETE atau POST (beberapa client/proxy strip DELETE)
        if ($method !== 'delete' && $method !== 'post') {
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

        if (!empty($missing)) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'Field mandatory kosong',
                'missing' => $missing,
            ), 400);
        }

        // trxId optional — jangan kirim kalau kosong
        $dataReq = array(
            'partnerServiceId' => $partnerServiceId,
            'customerNo'       => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
        );
        if ($trxId !== '') {
            $dataReq['trxId'] = $trxId;
        }

        // Step 1: ambil token BRI
        $tokenResp = $this->fetchAccessToken();
        if (empty($tokenResp['accessToken'])) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'Gagal dapat access token dari BRI',
                'bri'     => $tokenResp,
            ), 500);
        }

        // Step 2: panggil delete-va
        $vaResp = $this->callVa($tokenResp['accessToken'], $dataReq, 'DELETE');

        $this->mymodel->insertid('bri_res', array(
            'text' => '[delete_va] req=' . json_encode($dataReq) . ' resp=' . json_encode($vaResp),
        ));

        return $this->json(array(
            'status'  => 1,
            'message' => 'Berhasil request delete VA ke BRI',
            'req'     => $dataReq,
            'token'   => $tokenResp,
            'bri'     => $vaResp,
        ), 200);
    }
}
