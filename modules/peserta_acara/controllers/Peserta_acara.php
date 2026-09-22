<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public page: Daftar Hadir & Tidak Hadir Peserta Acara
 * Tanpa login, bisa diakses semua orang.
 */
class Peserta_acara extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('peserta_acara/model_peserta_acara');
    }

    /**
     * Main page
     */
    public function index($id_acara = 0)
    {
        $id_acara = (int) $id_acara;
        if ($id_acara <= 0) {
            show_404();
            return;
        }

        $acara = $this->model_peserta_acara->get_acara($id_acara);
        if (!$acara) {
            show_404();
            return;
        }

        // Ambil semua undangan
        $semua_undangan = $this->model_peserta_acara->get_semua_undangan($acara->peserta_acara);

        // Ambil yang hadir
        $hadir = $this->model_peserta_acara->get_hadir($id_acara);

        // Hitung yang tidak hadir
        $tidak_hadir = $this->model_peserta_acara->get_tidak_hadir($semua_undangan, $hadir);

        $data = array(
            'acara'         => $acara,
            'total_undangan'=> count($semua_undangan),
            'total_hadir'   => count($hadir),
            'total_tidak'   => count($tidak_hadir),
            'list_hadir'    => array_values($hadir),
            'list_tidak'    => array_values($tidak_hadir),
            'roles'         => array_filter(array_map('trim', explode(',', $acara->peserta_acara))),
            'id_acara'      => $id_acara,
        );

        $this->load->view('peserta_acara/view', $data);
    }

    /**
     * AJAX endpoint: get data as JSON
     */
    public function data($id_acara = 0)
    {
        $id_acara = (int) $id_acara;
        if ($id_acara <= 0) {
            echo json_encode(array('status' => 0, 'message' => 'ID tidak valid'));
            return;
        }

        $acara = $this->model_peserta_acara->get_acara($id_acara);
        if (!$acara) {
            echo json_encode(array('status' => 0, 'message' => 'Acara tidak ditemukan'));
            return;
        }

        $semua_undangan = $this->model_peserta_acara->get_semua_undangan($acara->peserta_acara);
        $hadir = $this->model_peserta_acara->get_hadir($id_acara);
        $tidak_hadir = $this->model_peserta_acara->get_tidak_hadir($semua_undangan, $hadir);

        echo json_encode(array(
            'status' => 1,
            'data' => array(
                'acara'          => $acara,
                'total_undangan' => count($semua_undangan),
                'total_hadir'    => count($hadir),
                'total_tidak'    => count($tidak_hadir),
                'list_hadir'     => array_values($hadir),
                'list_tidak'     => array_values($tidak_hadir),
            )
        ));
    }
}
