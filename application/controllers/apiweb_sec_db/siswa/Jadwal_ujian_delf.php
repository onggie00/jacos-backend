<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Jadwal_ujian_delf extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  public function index_post()
  {
    $status = "";
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    $date=date('Y-m');
    $where_and = ""; $where="";
    $hari = $this->post('hari');
    $id_siswa_aktif = $this->post('id_siswa_aktif');

    $get_siswa=$this->second_db->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_ft_aktif sa join siswa_ft s on s.id_siswa_ft = sa.id_siswa_ft join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_ft k on k.id_kelas_ft = sa.id_kelas where id_siswa_ft_aktif = $id_siswa_aktif","row");
    if(!$get_siswa){
      $msg = array('status' => 0, 'message' => 'Siswa tidak ditemukan', 'data' => array());
      $status = "200";
      $this->response($msg,$status);
    }
    // dd($get_siswa);
    $where="where tanggal like '%$date%' and id_tingkatan = $get_siswa->id_tingkatan";

    if ($hari) {
      $where_and = "and hari = '$hari'";
    }
    $data = $this->second_db->withquery(
      "select u.*,m.nama_mapel as mata_pelajaran_ft_nama_mapel,j.nama_ujian as jenis_ujian_nama_ujian from jadwal_ujian_delf u join mata_pelajaran_ft m on m.id_mapel = u.id_mapel join jenis_ujian j on j.id_jenis_ujian = u.id_jenis_ujian $where $where_and",
      "result"
    );

    if (!empty($data)) {
      $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $data);
      $status = "200";
    } else {
      $msg = array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array());
      $status = "200";
    }

    $this->response($msg, $status);
  }
}
