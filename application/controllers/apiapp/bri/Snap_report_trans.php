<?php
/**
 * BRI SNAP BI - Client: Report Transaksi VA (transfer-va/report)
 *
 * Endpoint : https://{domain}/snap/v1.0/transfer-va/report
 * Method   : POST
 * Default  : sandbox.partner.api.bri.co.id
 *
 * Sesuai spec BRI:
 *   partnerServiceId        M  8 char left-padded space, numeric
 *   startDate               M  yyyy-MM-dd (10 char)
 *   startTime               M  HH:mm:ss+07:00 (BRI filter sampai menit, HH:mm juga OK)
 *   endTime                 M  HH:mm:ss+07:00
 *   additionalInfo          O  object {customerCode?, uniqueCode?}
 *
 * BRI rule: range maksimal 24 jam, startDate dalam 60 hari terakhir.
 * BRI hanya filter field time sampai menit.
 *
 * Token BRI di-fetch otomatis via MY_Snap_bri::fetchAccessToken().
 */

require_once APPPATH . 'core/MY_Snap_bri.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');

class Snap_report_trans extends MY_Snap_bri
{
    protected $vaEndpoint = '/snap/v1.0/transfer-va/report';

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
                'message' => 'BRI SNAP BI - Report Transaksi VA endpoint aktif',
                'method'  => 'POST',
                'target'  => 'https://' . $this->baseDomain . $this->vaEndpoint,
                'note'    => 'POST body berisi partnerServiceId, startDate, startTime, endTime. additionalInfo opsional. Token BRI di-fetch otomatis.',
                'required_fields' => array('partnerServiceId', 'startDate', 'startTime', 'endTime'),
                'optional_fields' => array('additionalInfo.customerCode', 'additionalInfo.uniqueCode'),
                'format' => array(
                    'startDate'  => 'yyyy-MM-dd',
                    'startTime'  => 'HH:mm:ss+07:00 atau HH:mm',
                    'endTime'    => 'HH:mm:ss+07:00 atau HH:mm',
                ),
                'bri_rules' => array(
                    'range_max_24h' => true,
                    'startDate_max_60d_ago' => true,
                    'time_filter_minute_precision' => true,
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
        $partnerServiceId   = isset($input['partnerServiceId']) ? $input['partnerServiceId'] : '';
        $startDate          = isset($input['startDate'])        ? $input['startDate']        : '';
        $startTime          = isset($input['startTime'])        ? $input['startTime']        : '';
        $endTime            = isset($input['endTime'])          ? $input['endTime']          : '';
        $additionalInfo     = isset($input['additionalInfo'])   ? $input['additionalInfo']   : array();

        if ($partnerServiceId === '') { $missing[] = 'partnerServiceId'; }
        if ($startDate        === '') { $missing[] = 'startDate'; }
        if ($startTime        === '') { $missing[] = 'startTime'; }
        if ($endTime          === '') { $missing[] = 'endTime'; }

        if (!empty($missing)) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'Field mandatory kosong',
                'missing' => $missing,
            ), 400);
        }

        // Validasi format startDate: yyyy-MM-dd
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'startDate harus berformat yyyy-MM-dd',
                'received'=> $startDate,
            ), 400);
        }

        // Validasi format startTime / endTime minimal HH:mm
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?(\+\d{2}:\d{2})?$/', $startTime)) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'startTime harus berformat HH:mm atau HH:mm:ss+07:00',
                'received'=> $startTime,
            ), 400);
        }
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?(\+\d{2}:\d{2})?$/', $endTime)) {
            return $this->json(array(
                'status'  => 0,
                'message' => 'endTime harus berformat HH:mm atau HH:mm:ss+07:00',
                'received'=> $endTime,
            ), 400);
        }

        // additionalInfo optional — kirim object kalau ada isinya
        $hasAdditional = is_array($additionalInfo) && (!empty($additionalInfo['customerCode']) || !empty($additionalInfo['uniqueCode']));

        $dataReq = array(
            'partnerServiceId' => $partnerServiceId,
            'startDate'        => $startDate,
            'startTime'        => $startTime,
            'endTime'          => $endTime,
        );
        if ($hasAdditional) {
            $dataReq['additionalInfo'] = array();
            if (!empty($additionalInfo['customerCode'])) {
                $dataReq['additionalInfo']['customerCode'] = $additionalInfo['customerCode'];
            }
            if (!empty($additionalInfo['uniqueCode'])) {
                $dataReq['additionalInfo']['uniqueCode'] = $additionalInfo['uniqueCode'];
            }
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

        // Step 2: panggil report
        $vaResp = $this->callVa($tokenResp['accessToken'], $dataReq, 'POST');

        $this->mymodel->insertid('bri_res', array(
            'text' => '[report_trans] req=' . json_encode($dataReq) . ' resp=' . json_encode($vaResp),
        ));

        return $this->json(array(
            'status'  => 1,
            'message' => 'Berhasil request report transaksi ke BRI',
            'req'     => $dataReq,
            'token'   => $tokenResp,
            'bri'     => $vaResp,
        ), 200);
    }
}
