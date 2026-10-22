<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');


class Get_diskon_spp extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    /**
     * GET/POST apiapp/spp/get_diskon_spp
     * Parameter: jenjang (kb/tk/sd/...), id_tingkatan (opsional, int)
     * Return: setting diskon aktif utk jenjang+tingkatan (persen, min bulan, status)
     * FE pakai utk visual badge "hemat 3%" dsb — angka final tetap dari server saat create VA.
     */
    public function index_get()
    {
        $this->index_post();
    }

    public function index_post()
    {
        $jenjang = strtolower($this->post('jenjang') ? $this->post('jenjang') : $this->get('jenjang'));
        $id_tingkatan = $this->post('id_tingkatan') !== null ? (int)$this->post('id_tingkatan') : $this->get('id_tingkatan');

        if (empty($jenjang)) {
            $msg = array('status' => 0, 'message' => 'jenjang wajib diisi', 'data' => null);
            $this->response($msg, "200");
        }

        $setting = spp_diskon_setting($jenjang, $id_tingkatan);

        if (empty($setting) || (int)$setting->aktif !== 1) {
            $msg = array('status' => 1, 'message' => 'Tidak ada diskon berlaku', 'data' => array(
                'diskon_aktif' => false,
                'persen_diskon' => 0,
                'min_pembayaran' => null,
            ));
            $this->response($msg, "200");
        }

        // row yang terpakai: spesifik tingkatan dulu, fallback default jenjang (id_tingkatan NULL)
        $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => array(
            'diskon_aktif' => true,
            'persen_diskon' => (string)((float)$setting->persen_diskon),
            'min_pembayaran' => (string)((int)$setting->min_pembayaran),
            'id_setting' => (int)$setting->id_setting,
            'id_tingkatan' => $setting->id_tingkatan !== null ? (int)$setting->id_tingkatan : null,
        ));
        $this->response($msg, "200");
    }
}
