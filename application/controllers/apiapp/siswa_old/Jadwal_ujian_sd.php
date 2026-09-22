<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Jadwal_ujian_sd extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  public function index_get()
  {
    $status = "";
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    $date=date('Y-m');
    $where_and = ""; $where="";
    $hari = $this->get('hari');
    $id_siswa = $this->get('id_siswa_aktif');

    $data = $this->mymodel->withquery("select * from siswa_sd_aktif where id_siswa_sd_aktif = '".$id_siswa."'","row");

    /*$get_siswa=$this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_sd_aktif sa join siswa_sd s on s.id_siswa_sd = sa.id_siswa_sd join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_sd k on k.id_kelas_sd = sa.id_kelas where id_siswa_sd_aktif = $id_siswa_aktif","row");
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
    $data = $this->mymodel->withquery(
      "select u.*,m.nama_mapel as mata_pelajaran_sd_nama_mapel,j.nama_ujian as jenis_ujian_nama_ujian from jadwal_ujian_sd u join mata_pelajaran_sd m on m.id_mapel = u.id_mapel join jenis_ujian j on j.id_jenis_ujian = u.id_jenis_ujian $where $where_and",
      "result"
    );*/
//echo $this->db->last_query();
    if (!empty($data)) {
      $get_siswa = $this->mymodel->withquery("select d.*, k.kelas, k.id_tingkatan, k.id_ruang_kelas, s.nis from ujian_ruang_detail d left join ujian_ruang_kelas k on d.id_ruang_kelas = k.id_ruang_kelas left join siswa_sd_aktif s on d.nomor_peserta_ujian = s.nomor_peserta_ujian where d.nomor_peserta_ujian = '".$data->nomor_peserta_ujian."' order by d.urutan_kursi ASC limit 0,1","result");
      if (!empty($get_siswa)) {
        $get_ujian = $this->mymodel->withquery("select j.id_jadwal, j.id_jenis_ujian, j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, j.id_tahun_ajaran, m.nama_mapel, t.label as tahun_ajaran from jadwal_ujian_sd j join ujian_mapel_sd m on j.id_mapel = m.id_mapel join tahun_ajaran t on j.id_tahun_ajaran = t.id_tahun_ajaran where j.id_tingkatan = '".$get_siswa[0]->id_tingkatan."' order by tanggal ASC, jam_mulai ASC","result");
            
        if (!empty($get_ujian)) {
          $arr_ujian = array();
          foreach ($get_ujian as $key => $value) {
            $value->tanggal = formatTanggal($value->tanggal);
            $value->jam_mulai = date("H:i", strtotime($value->jam_mulai));
            $value->jam_selesai = date("H:i", strtotime($value->jam_selesai));
            $value->tahun_ajaran = str_replace("/", "-", $value->tahun_ajaran);
          }
          $arr_ujian = array_values($arr_ujian);
          foreach ($get_siswa as $key => $value) {
            $value->judul_ujian = $this->mymodel->withquery("select ju.nama_ujian from jenis_ujian ju join jadwal_ujian_sd j on ju.id_jenis_ujian = j.id_jenis_ujian join ujian_ruang_kelas k on j.id_tingkatan = k.id_tingkatan where k.id_tingkatan = '".$get_siswa[0]->id_tingkatan."'","row")->nama_ujian;
          }
          $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $get_ujian);
          $status = "200";
        }
        else{
          $msg = array('status' => 0, 'message' => 'Belum ada jadwal ujian', 'data' => array());
          $status = "200";
        }
      }
      else{
        $msg = array('status' => 0, 'message' => 'Nomor Peserta Ujian tidak valid sebagai peserta ujian', 'data' => array());
      $status = "200";
      }
    } else {
      $msg = array('status' => 0, 'message' => 'Siswa belum punya nomor ujian', 'data' => array());
      $status = "200";
    }

    $this->response($msg, $status);
  }
}
