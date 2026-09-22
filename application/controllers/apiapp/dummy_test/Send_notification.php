<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
define('PRIVATE_FIREBASE_KEY', FCPATH . 'labscib-app-c0ca345e64d9.json');
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

/**
 * API kirim bubble notifikasi FCM ke device user tertentu.
 * Hanya untuk eksekusi manual (admin / postman), tidak ada integrasi frontend/mobile.
 *
 * Input POST:
 * - email     : email_ms_office user
 * - judul     : title notifikasi
 * - deskripsi : body notifikasi
 * - user_role : nama_tabel user (guru_sd, guru_smp, guru_sma, guru_ft, pegawai,
 *               pimpinan_sd, pimpinan_smp, pimpinan_sma, siswa_sd, siswa_smp,
 *               siswa_sma, siswa_ft, pramubhakti, security)
 * Header: x-api-key (cek tabel `keys`)
 *
 * Catatan log tiap pengiriman ke tabel list_notifikasi.
 */
class Send_notification extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }

    public function index_post()
    {
        // ===== 1. Validasi x-api-key =====
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            $headers[strtolower($name)] = $value;
        }
        $api_key = isset($headers['x-api-key']) ? $headers['x-api-key'] : '';
        $cek_key = $this->mymodel->withquery("select `key` from `keys` where `key` = '" . $this->db->escape_str($api_key) . "'", "row");
        if (empty($cek_key)) {
            $this->response(array('status' => 0, 'message' => 'API key tidak valid'), 401);
        }

        // ===== 2. Ambil & validasi input =====
        $email     = $this->post('email');
        $judul     = $this->post('judul');
        $deskripsi = $this->post('deskripsi');
        $user_role = $this->post('user_role');

        if (empty($email) || empty($judul) || empty($deskripsi) || empty($user_role)) {
            $this->response(array('status' => 0, 'message' => 'Parameter email, judul, deskripsi, user_role wajib diisi'), 400);
        }

        // ===== 3. Resolve user => device token =====
        $resolve = $this->resolve_user($email, $user_role);
        if ($resolve === false) {
            $this->response(array('status' => 0, 'message' => 'User / device_id tidak ditemukan untuk email ' . $email . ' di role ' . $user_role), 404);
        }

        // ===== 4. Kirim FCM =====
        $fcm_result = $this->firebase_send_notif($resolve['device_id'], $judul, $deskripsi);

        // ===== 5. Catat log ke list_notifikasi =====
        $log = array(
            'title'        => $judul,
            'content'      => $deskripsi,
            'role'         => $resolve['table_used'],
            'user_id'      => $resolve['user_id'],
            'email'        => $email,
            'nama_lengkap' => $resolve['nama_lengkap'],
            'action'       => json_encode(array(
                'fcm_response' => $fcm_result,
                'route'        => 'apiapp/dummy_test/Send_notification'
            ))
        );
        $this->mymodel->insert("list_notifikasi", $log);

        // ===== 6. Response =====
        $this->response(array(
            'status'  => 1,
            'message' => 'Notifikasi terkirim',
            'data'    => array(
                'device_id'  => $resolve['device_id'],
                'table_used' => $resolve['table_used'],
                'user_id'    => $resolve['user_id'],
                'fcm_result' => $fcm_result
            )
        ), 200);
    }

    /**
     * Cari device_id user berdasarkan email_ms_office + nama_tabel.
     * Return array(table_used, user_id, device_id) atau false.
     *
     * Fallback untuk pimpinan: jika tidak ketemu di tabel pimpinan yang diminta,
     * turun ke pimpinan lainnya lalu guru_sma -> guru_smp -> guru_sd -> pegawai.
     */
    function resolve_user($email, $user_role)
    {
        $allowed = array('guru_sd', 'guru_smp', 'guru_sma', 'guru_ft', 'pegawai',
                         'pimpinan_sd', 'pimpinan_smp', 'pimpinan_sma',
                         'siswa_sd', 'siswa_smp', 'siswa_sma', 'siswa_ft',
                         'pramubhakti', 'security');
        if (!in_array($user_role, $allowed)) {
            return false;
        }

        // rantai fallback (hanya dipakai bila role = pimpinan_*)
        if ($user_role == 'pimpinan_sma') {
            $chain = array('pimpinan_sma', 'pimpinan_smp', 'pimpinan_sd', 'guru_sma', 'guru_smp', 'guru_sd', 'pegawai');
        } else if ($user_role == 'pimpinan_smp') {
            $chain = array('pimpinan_smp', 'pimpinan_sd', 'guru_sma', 'guru_smp', 'guru_sd', 'pegawai');
        } else if ($user_role == 'pimpinan_sd') {
            $chain = array('pimpinan_sd', 'guru_sma', 'guru_smp', 'guru_sd', 'pegawai');
        } else {
            $chain = array($user_role);
        }

        $email = $this->db->escape_str($email);
        foreach ($chain as $table) {
            if (substr($table, 0, 5) == 'siswa') {
                // device ada di tabel siswa_xx_aktif (join via id_siswa_xx)
                $query = $this->mymodel->withquery(
                    "select a.id_siswa_" . $table . " as id_user, a.device_id_siswa, s.nama_lengkap
                     from " . $table . " s
                     join " . $table . "_aktif a on a.id_siswa_" . substr($table, 6) . " = s.id_siswa_" . substr($table, 6) . "
                     where s.email_ms_office = '" . $email . "'
                       and a.deleted_at is null
                       and a.device_id_siswa is not null and a.device_id_siswa != ''",
                    "row"
                );
            } else {
                // nama kolom id beda tiap tabel -> alias id_user
                $id_cols = array(
                    'guru_sd' => 'id_guru', 'guru_smp' => 'id_guru', 'guru_sma' => 'id_guru', 'guru_ft' => 'id_guru',
                    'pegawai' => 'id_pegawai',
                    'pimpinan_sd' => 'id_pimpinan', 'pimpinan_smp' => 'id_pimpinan', 'pimpinan_sma' => 'id_pimpinan',
                    'pramubhakti' => 'id_pramubhakti', 'security' => 'id_security'
                );
                $query = $this->mymodel->withquery(
                    "select " . $id_cols[$table] . " as id_user, device_id, nama_lengkap from " . $table . "
                     where email_ms_office = '" . $email . "'
                       and deleted_at is null
                       and device_id is not null and device_id != ''",
                    "row"
                );
            }
            if (!empty($query) && !empty($query->device_id)) {
                return array(
                    'table_used'   => $table,
                    'user_id'      => $query->id_user,
                    'device_id'    => $query->device_id,
                    'nama_lengkap' => $query->nama_lengkap
                );
            }
        }
        return false;
    }

    // ===== FCM (pattern dari Acc_izin::send_notif) =====

    function getAccessToken()
    {
        $client = new \Google_Client();
        $client->setAuthConfig(PRIVATE_FIREBASE_KEY);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return $token['access_token'];
    }

    function firebase_send_notif($fcm_id, $title, $body)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json'
        );

        $fields = array(
            'message' => array(
                'token' => $fcm_id,
                'notification' => array(
                    'title' => $title,
                    'body'  => $body
                )
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/labscib-app/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
