<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_today_presensi_non_siswa extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $status = "200";
        $data = [];
        $dataIndex = [];

        // ================= HEADER TOKEN =================
        $headers = getallheaders();
        $token = $headers['x-token'] ?? '';

        // ================= SETTING =================
        $setting = $this->get_setting();
        $host = $setting->host;
        $port = $setting->port;

        $token = $token ?: $this->get_token();

        if (empty($token)) {
            $tokenReq = $this->request_token(
                $host,
                $port,
                $setting->username,
                $setting->password
            );
            if (!empty($tokenReq->token)) {
                $token = $tokenReq->token;
                $this->mymodel->update(
                    "presensi_setting",
                    ['value' => $token, 'updated_at' => date('Y-m-d H:i:s')],
                    ['name_setting' => 'token_mesin']
                );
            }
        }

        if (empty($token)) {
            return $this->response([
                'status' => 0,
                'message' => 'Request Token Failed',
                'token' => '',
                'data' => []
            ], $status);
        }

        // ================= PARAM =================
        $start_time = $this->input->get('start_time') ?: date('Y-m-d')." 00:00:00.000";
        $end_time   = $this->input->get('end_time') ?: date('Y-m-d')." 23:59:59.000";
        $hari       = formatHari($start_time);
        $emp_code   = $this->input->get('emp_code') ?: "";
        $page_size  = 10000 ?: 10000;

        // ================= ROLE SHIFT (MAP) =================
        $hariShift = strtolower(formatHari($start_time));
        $get_roleShift = $this->mymodel->withquery("
            SELECT r.nama_role, r.nama_tabel, s.start_checkin, s.limit_checkin, s.start_checkout, s.limit_checkout, s.hari
            FROM presensi_setting_role r
            JOIN presensi_setting_shift s ON s.id_role = r.id
        ", "result");

        $roleShiftMap = [];
        foreach ($get_roleShift as $rs) {
            // ponytail: pakai baris shift pertama yang harinya match (dedup role duplikat & multi-baris shift)
            if (!isset($roleShiftMap[$rs->nama_role]) && strpos(','.strtolower($rs->hari).',', ','.$hariShift.',') !== false) {
                $roleShiftMap[$rs->nama_role] = $rs;
            }
        }

        // ================= PRESENSI EXISTING (MAP) =================
        $get_recorded = $this->mymodel->withquery("
            SELECT 
                id_presensi,
                npp,
                presensi_date,
                check_in,
                check_out,
                file_report,
                report_status,
                report_status_end,
                status_presensi,
                status_presensi_selesai,
                keterangan
            FROM presensi_office
            WHERE presensi_date BETWEEN '".date('Y-m-d', strtotime($start_time))."'
            AND '".date('Y-m-d', strtotime($end_time))."'
        ", "result");

        $recordedMap = [];
        foreach ($get_recorded as $r) {
            $recordedMap[$r->npp.'|'.$r->presensi_date] = $r;
        }

        // ================= DEVICE =================
        $devices = $this->get_all_device();
        if (empty($devices)) {
            return $this->response([
                'status' => 0,
                'message' => 'Device not found',
                'token' => $token,
                'data' => []
            ], $status);
        }

        // ================= FETCH API =================
        foreach ($devices as $device) {

            $result = $this->get_transaction_by_time(
                $host,
                $port,
                $token,
                $emp_code,
                $start_time,
                $end_time,
                $page_size,
                $device->sn_mesin,
                $device->nama_mesin
            );

            if (empty($result->data)) continue;

            foreach ($result->data as $row) {

                if (!empty($row->last_name)){
                    $keyUniq = $row->last_name.'|'.$row->punch_time;
                    if (isset($dataIndex[$keyUniq])) continue;

                    $dataIndex[$keyUniq] = true;

                    $data[] = [
                        'npp' => $row->last_name,
                        'first_name' => $row->first_name,
                        'hari' => formatHari($row->punch_time),
                        'punch_time' => $row->punch_time,
                        'presensi_role' => $row->department,
                        'presensi_device' => $row->terminal_alias,
                        'file_report' => $row->file_report ?? null,
                        'report_status' => $row->report_status ?? null
                    ];
                }
                else if(!empty($row->emp_code)){
                    $keyUniq = $row->emp_code.'|'.$row->punch_time;
                    if (isset($dataIndex[$keyUniq])) continue;

                    $dataIndex[$keyUniq] = true;

                    $data[] = [
                        'npp' => $row->emp_code,
                        'first_name' => $row->first_name,
                        'hari' => formatHari($row->punch_time),
                        'punch_time' => $row->punch_time,
                        'presensi_role' => $row->department,
                        'presensi_device' => $row->terminal_alias,
                        'file_report' => $row->file_report ?? null,
                        'report_status' => $row->report_status ?? null
                    ];
                }
            }
        }

        // ================= SORT =================
        usort($data, fn($a, $b) => strtotime($a['punch_time']) <=> strtotime($b['punch_time']));

        // ================= FILTER TOLERANSI (< 5 MENIT) =================
        $filtered = [];
        $lastPunch = [];

        foreach ($data as $row) {
            $key = $row['npp'];
            $time = strtotime($row['punch_time']);

            if (isset($lastPunch[$key]) && abs($time - $lastPunch[$key]) < 300) {
                continue; // skip duplikat
            }

            $lastPunch[$key] = $time;
            $filtered[] = $row;
        }

        $data = $filtered;

        // ================= AGREGASI PER NPP PER HARI =================
        $daily = []; // key: npp|tanggal

        foreach ($data as $row) {
            $tanggal = date('Y-m-d', strtotime($row['punch_time']));
            $key = $row['npp'].'|'.$tanggal;
            $time = date('H:i:s', strtotime($row['punch_time']));

            if (!isset($daily[$key])) {
                $daily[$key] = [
                    'npp' => $row['npp'],
                    'nama' => $row['first_name'],
                    'tanggal' => $tanggal,
                    'hari' => $row['hari'],
                    'role' => $row['presensi_role'],
                    'device' => $row['presensi_device'],
                    'earliest' => $time,  // paling awal
                    'latest' => $time,    // paling akhir
                    'count' => 1
                ];
            } else {
                if ($time < $daily[$key]['earliest']) $daily[$key]['earliest'] = $time;
                if ($time > $daily[$key]['latest']) $daily[$key]['latest'] = $time;
                $daily[$key]['count']++;
            }
        }

        // ================= INSERT / UPDATE =================
        foreach ($daily as $key => $row) {

            $shift = $roleShiftMap[$row['role']] ?? null;

            $check_in = null;
            $check_out = null;
            $status_masuk = null;
            $status_pulang = null;

            if ($shift) {
                $has_checkin = false;
                $has_checkout = false;

                // Cek apakah pegawai sudah terdaftar di tabel role
                $check_table = $this->mymodel->withquery(
                    "select nama_lengkap, npp, emp_code, presensi_role 
                    from ".strtolower($shift->nama_tabel)." 
                    where (npp = ? or emp_code = ?)",
                    "row",
                    [$row['npp'], str_replace('.', '', $row['npp'])]
                );

                if (empty($check_table)) {
                    $this->mymodel->insert($shift->nama_tabel, [
                        "npp" => $row['npp'],
                        "emp_code" => $row['npp'],
                        "nama_lengkap" => $row['nama'],
                        "presensi_role" => $row['role'],
                    ]);
                }

                // ===== CHECK_IN (earliest punch) =====
                if ($row['earliest'] >= $shift->start_checkin && $row['earliest'] <= $shift->limit_checkin) {
                    // Dalam window checkin → Hadir
                    $check_in = $row['earliest'];
                    $status_masuk = 'H';
                    $has_checkin = true;
                } elseif ($row['earliest'] > $shift->limit_checkin && $row['earliest'] < $shift->start_checkout) {
                    // Setelah limit checkin, sebelum window checkout → Terlambat
                    $check_in = $row['earliest'];
                    $status_masuk = 'T';
                    $has_checkin = true;
                }

                // ===== CHECK_OUT (latest punch) =====
                if ($row['latest'] >= $shift->start_checkout && $row['latest'] <= $shift->limit_checkout) {
                    // Dalam window checkout → End
                    $check_out = $row['latest'];
                    $status_pulang = 'E';
                    $has_checkout = true;
                } elseif ($row['latest'] > $shift->limit_checkout) {
                    // Setelah limit checkout → Selesai
                    $check_out = $row['latest'];
                    $status_pulang = 'SH';
                    $has_checkout = true;
                }

                // Skip jika tidak ada yang valid
                if (!$has_checkin && !$has_checkout) continue;

            } else {
                // Tidak ada shift → fallback threshold 12:00
                if ($row['earliest'] < '12:00:00') {
                    $check_in = $row['earliest'];
                    // ponytail: tanpa shift tidak ada limit_checkin, jadi apa pun jam < 12:00 dianggap terlambat (T), bukan H
                    $status_masuk = 'T';
                }
                if ($row['latest'] >= '12:00:00') {
                    $check_out = $row['latest'];
                    $status_pulang = 'E';
                }

                // Skip jika tidak ada yang valid
                if (!$check_in && !$check_out) continue;
            }

            // ================= INSERT / UPDATE =================
            $keyDB = $row['npp'].'|'.$row['tanggal'];

            $insert = [
                'presensi_date' => $row['tanggal'],
                'npp' => $row['npp'],
                'nama_lengkap' => $row['nama'],
                'presensi_hari' => $row['hari'],
                'role' => $row['role'],
                'presensi_device' => $row['device'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($check_in) {
                $insert['check_in'] = $check_in;
                $insert['status_presensi'] = $status_masuk;
            }
            if ($check_out) {
                $insert['check_out'] = $check_out;
                $insert['status_presensi_selesai'] = $status_pulang;
            }

            if (isset($recordedMap[$keyDB])) {
                // ===== UPDATE =====
                $existing = $recordedMap[$keyDB];
                $needUpdate = false;

                // CHECK_IN: update jika lebih awal, skip jika laporan check-in masih pending (jangan ditimpa mesin)
                if (isset($insert['check_in'])) {
                    if (!empty($existing->check_in)) {
                        if (strtotime($existing->check_in) > strtotime($insert['check_in']) && $existing->report_status != '1' && $existing->report_status != '0') {
                            $needUpdate = true;
                        } else {
                            unset($insert['check_in'], $insert['status_presensi']);
                        }
                    } else {
                        $needUpdate = true;
                    }
                }

                // CHECK_OUT: update jika lebih akhir, skip jika laporan pulang masih pending
                if (isset($insert['check_out'])) {
                    if (!empty($existing->check_out)) {
                        if (strtotime($existing->check_out) < strtotime($insert['check_out']) && $existing->report_status_end != '1' && $existing->report_status_end != '0') {
                            $needUpdate = true;
                        } else {
                            unset($insert['check_out'], $insert['status_presensi_selesai']);
                        }
                    } else {
                        $needUpdate = true;
                    }
                }

                if ($needUpdate && count($insert) > 1) {
                    $this->mymodel->update('presensi_office', $insert, [
                        'presensi_date' => $existing->presensi_date,
                        'npp' => $existing->npp
                    ]);
                }

            } else {
                // ===== INSERT BARU (upsert: cron overlap tidak bisa bikin duplikat, unik_presensi melindungi) =====
                $cols = array();
                $vals = array();
                foreach ($insert as $k => $v) {
                    $cols[] = '`'.$k.'`';
                    $vals[] = $this->db->escape($v);
                }
                $this->mymodel->withquery("insert into presensi_office (".implode(',', $cols).")
                    values (".implode(',', $vals).")
                    on duplicate key update id_presensi = id_presensi");

                $id = $this->db->insert_id();
                if (empty($id)) {
                    // sudah ada (race overlap) → ambil id baris existing
                    $ex = $this->mymodel->withquery("select id_presensi from presensi_office where npp = '".$row['npp']."' and presensi_date = '".$row['tanggal']."'", "row");
                    $id = $ex->id_presensi;
                }

                // Update map agar punch berikutnya UPDATE
                $recordedMap[$keyDB] = (object)[
                    'id_presensi' => $id,
                    'npp' => $row['npp'],
                    'presensi_date' => $row['tanggal'],
                    'check_in' => $insert['check_in'] ?? null,
                    'check_out' => $insert['check_out'] ?? null,
                    'report_status' => null,
                    'report_status_end' => null
                ];
            }
        }

        return $this->response([
            'status' => 1,
            'message' => 'Success',
            'token' => $token,
            'data' => $data
        ], $status);
    }

    private function get_setting(){
        $get_setting = $this->mymodel->withquery("select * from presensi_setting","result");

        $data = new stdClass();
        if (!empty($get_setting)) {
            foreach ($get_setting as $key => $value) {
                $data->{$value->name_setting} = $value->value;
            }
        }
        
        return $data;
    }

    private function get_token(){
        $data = $this->mymodel->withquery("select * from presensi_setting where name_setting = 'token_mesin'","row")->value;
        if(!empty($data) && date("Y-m-d", strtotime($data)) != date("Y-m-d") ){
            $data = null;
        }

        return $data;
    }

    private function request_token($host, $port, $username, $password){
        $data = new stdClass();
        $data->username = (!empty($username)) ? $username : "admin";
        $data->password = (!empty($password)) ? $password : "labschool123";

        $url = $host . ":" . $port . "/jwt-api-token-auth/";
        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    private function get_transaction_by_time($host, $port, $token, $emp_code, $start_time, $end_time, $page_size, $terminal_sn, $terminal_alias){
        $url = $host . ":" . $port . "/iclock/api/transactions/"."?emp_code=" . $emp_code . "&page_size=" . $page_size . "&start_time=" . $start_time . "&end_time=" . $end_time."&terminal_sn=" . $terminal_sn."&terminal_alias=" . $terminal_alias;
        $url = str_replace(" ", "%20", $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: JWT ' . $token,
            'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        if (curl_errno($ch)) { 
            print curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result);
    }

    private function get_all_device(){
        $data = $this->mymodel->withquery("select * from presensi_device","result");
        return $data;
    }

}