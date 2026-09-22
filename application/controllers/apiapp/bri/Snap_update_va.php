<?php
/**
 * BRI SNAP BI - Client: Update VA (transfer-va/update-va)
 *
 * Endpoint : https://{domain}/snap/v1.0/transfer-va/update-va
 * Method   : PUT
 * Default  : sandbox.partner.api.bri.co.id
 *
 * Sesuai spec BRI: semua field Mandatory (M).
 *
 * Input POST body:
 *   partnerServiceId    string  M  default "   22216"
 *   customerNo          string  M  (sampai 20 digit numeric)
 *   virtualAccountNo    string  M  partnerServiceId + customerNo
 *   virtualAccountName  string  M
 *   totalAmount.value   string  M  numeric
 *   totalAmount.currency string M  default "IDR"
 *   trxId               string  M
 *   expiredDate         string  M  ISO-8601, default +1 hari
 *   additionalInfo.description string M
 *
 * Token BRI di-fetch otomatis via MY_Snap_bri::fetchAccessToken().
 */

require_once APPPATH . 'core/MY_Snap_bri.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');

class Snap_update_va extends MY_Snap_bri
{
    protected $vaEndpoint = '/snap/v1.0/transfer-va/update-va';

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
                'message' => 'BRI SNAP BI - Update VA endpoint aktif',
                'method'  => 'PUT',
                'target'  => 'https://' . $this->baseDomain . $this->vaEndpoint,
                'note'    => 'PUT body berisi parameter VA lengkap. Token BRI di-fetch otomatis.',
                'required_fields' => array(
                    'partnerServiceId', 'customerNo', 'virtualAccountNo',
                    'virtualAccountName', 'totalAmount', 'trxId',
                    'expiredDate', 'additionalInfo',
                ),
            ), 200);
        }

        // Accept PUT atau POST (beberapa client/proxy strip PUT)
        if ($method !== 'put' && $method !== 'post') {
            return $this->json(array('status' => 0, 'message' => 'Method not allowed'), 405);
        }

        // Baca raw JSON kalau content-type JSON (CI default php://input parsing cuma untuk form-urlencoded)
        $rawBody = file_get_contents('php://input');
        $input   = array();
        if ($rawBody !== '' && $rawBody !== NULL) {
            $decoded = json_decode($rawBody, true);
            if (is_array($decoded)) {
                $input = $decoded;
            }
        }
        if (empty($input)) {
            // fallback ke $_POST (form-urlencoded)
            $input = $this->input->post(NULL, true);
            if (!is_array($input)) {
                $input = array();
            }
        }

        // Validasi field mandatory
        // ponytail: JANGAN trim() value BRI — partnerServiceId pakai left-padding space,
        // customerNo/virtualAccountNo numeric exact. trim() akan korup leading spaces.
        $missing = array();
        $partnerServiceId   = isset($input['partnerServiceId'])   ? $input['partnerServiceId']   : '';
        $customerNo         = isset($input['customerNo'])         ? $input['customerNo']         : '';
        $virtualAccountNo   = isset($input['virtualAccountNo'])   ? $input['virtualAccountNo']   : '';
        $virtualAccountName = isset($input['virtualAccountName']) ? $input['virtualAccountName'] : '';
        $trxId              = isset($input['trxId'])              ? $input['trxId']              : '';
        $expiredDate        = isset($input['expiredDate'])        ? $input['expiredDate']        : '';
        $additionalInfo     = isset($input['additionalInfo'])     ? $input['additionalInfo']     : array();
        $totalAmount        = isset($input['totalAmount'])        ? $input['totalAmount']        : array();

        if ($partnerServiceId   === '') { $missing[] = 'partnerServiceId'; }
        if ($customerNo         === '') { $missing[] = 'customerNo'; }
        if ($virtualAccountNo   === '') { $missing[] = 'virtualAccountNo'; }
        if ($virtualAccountName === '') { $missing[] = 'virtualAccountName'; }
        if ($trxId              === '') { $missing[] = 'trxId'; }
        if ($expiredDate        === '') { $missing[] = 'expiredDate'; }

        if (!is_array($additionalInfo) || !isset($additionalInfo['description']) || $additionalInfo['description'] === '') {
            $missing[] = 'additionalInfo.description';
        }

        if (!is_array($totalAmount) || !isset($totalAmount['value']) || $totalAmount['value'] === '') {
            $missing[] = 'totalAmount.value';
        } else {
            $currency = isset($totalAmount['currency']) ? $totalAmount['currency'] : '';
            if ($currency === '') { $missing[] = 'totalAmount.currency'; }
        }

        if (!empty($missing)) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'Field mandatory kosong',
                'missing' => $missing,
            ), 400);
        }

        // Default partnerServiceId kalau kosong (sandbox dummy)
        if ($partnerServiceId === '') {
            $partnerServiceId = $this->partnerServiceIdDefault;
        }
        // Default expiredDate +1 hari kalau caller kirim string kosong (tapi sudah divalidasi, fallback safety)
        if ($expiredDate === '') {
            $expiredDate = date('Y-m-d\TH:i:s', strtotime('+1 day')) . '+07:00';
        }

        // Normalisasi totalAmount.currency default IDR
        if (empty($totalAmount['currency'])) {
            $totalAmount['currency'] = 'IDR';
        }

        $dataReq = array(
            'partnerServiceId'   => $partnerServiceId,
            'customerNo'         => $customerNo,
            'virtualAccountNo'   => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'trxId'              => $trxId,
            'totalAmount'        => array(
                'value'    => (string) $totalAmount['value'],
                'currency' => $totalAmount['currency'],
            ),
            'expiredDate'        => $expiredDate,
            'additionalInfo'     => array(
                'description' => $additionalInfo['description'],
            ),
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

        // Step 2: panggil update-va
        $vaResp = $this->callVa($tokenResp['accessToken'], $dataReq, 'PUT');

        $this->mymodel->insertid('bri_res', array(
            'text' => '[update_va] req=' . json_encode($dataReq) . ' resp=' . json_encode($vaResp),
        ));

        return $this->json(array(
            'status'  => 1,
            'message' => 'Berhasil request update VA ke BRI',
            'req'     => $dataReq,
            'token'   => $tokenResp,
            'bri'     => $vaResp,
        ), 200);
    }
}
