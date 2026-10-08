<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, content-type');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Setting bahasa user (role siswa).
 * GET/POST /apiapp/siswa/setting_bahasa/get_setting_bahasa
 * POST     /apiapp/siswa/setting_bahasa/update_bahasa  (.kode_bahasa)
 */
class Setting_bahasa extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->helper('bahasa_token');
        $this->load->model('Setting_bahasa_model');
    }

    private function _authenticate()
    {
        $token = '';
        if (!empty($_SERVER['HTTP_X_TOKEN'])) {
            $token = $_SERVER['HTTP_X_TOKEN'];
        }
        return validate_akun_bahasa_token($token, 'siswa');
    }

    public function get_setting_bahasa_get()
    {
        $auth = $this->_authenticate();
        if (!$auth['valid']) {
            $this->response(array('status' => 0, 'message' => $auth['message'], 'data' => array()), 401);
            return;
        }

        $row = $this->Setting_bahasa_model->get_or_create_default($auth['email'], 'siswa', $auth['jenjang']);
        if ($row === false) {
            $this->response(array('status' => 0, 'message' => 'Gagal membaca setting bahasa (database error)', 'data' => array()), 500);
            return;
        }
        if (empty($row)) {
            $this->response(array('status' => 0, 'message' => 'Email akun kosong, tidak bisa membaca setting bahasa', 'data' => array()), 400);
            return;
        }

        $data = array(
            'kode_bahasa'   => $row->kode_bahasa,
            'bahasa'        => $row->bahasa,
            'updated_at'    => $row->updated_at,
            'daftar_bahasa' => $this->Setting_bahasa_model->daftar_bahasa(),
        );
        $this->response(array('status' => 1, 'message' => 'Berhasil ambil setting bahasa', 'data' => array($data)), 200);
    }

    public function get_setting_bahasa_post()
    {
        $this->get_setting_bahasa_get();
    }

    public function update_bahasa_post()
    {
        $auth = $this->_authenticate();
        if (!$auth['valid']) {
            $this->response(array('status' => 0, 'message' => $auth['message'], 'data' => array()), 401);
            return;
        }

        $hasil = $this->Setting_bahasa_model->update_bahasa($auth['email'], 'siswa', $this->post('kode_bahasa'), $auth['jenjang']);
        if (!$hasil['success']) {
            $code = !empty($hasil['db_error']) ? 500 : 400;
            $this->response(array('status' => 0, 'message' => $hasil['message'], 'data' => array()), $code);
            return;
        }

        $row = $hasil['data'];
        $data = array(
            'kode_bahasa'   => $row->kode_bahasa,
            'bahasa'        => $row->bahasa,
            'updated_at'    => $row->updated_at,
            'daftar_bahasa' => $this->Setting_bahasa_model->daftar_bahasa(),
        );
        $this->response(array('status' => 1, 'message' => $hasil['message'], 'data' => array($data)), 200);
    }
}
