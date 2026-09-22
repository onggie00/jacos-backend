<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cek_ibadah extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];
        $id_siswa_aktif = $this->post("id_siswa_aktif");
        $jenjang = $this->post("jenjang");
        $tanggal = $this->post("tanggal");
        $waktu = $this->post("waktu");
        $longitude = $this->post("longitude");
        $latitude = $this->post("latitude");
        $id_ibadah = $this->post("id_ibadah");

        $ibadah = $this->mymodel->withquery("select * from ibadah_setting where start_ibadah <= '".$waktu."' and end_ibadah >= '".$waktu."'","row");
        $cek_presensi = $this->mymodel->withquery("select i.id, i.id_siswa_aktif, s.nama_lengkap, k.label as nama_kelas, i.jenjang, i.id_ibadah, p.ibadah, i.tanggal, i.waktu, i.jumlah_rakaat, i.keterangan, i.longitude, i.latitude from ibadah i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif and s.deleted_at is null left join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." join ibadah_setting p on i.id_ibadah = p.id where i.deleted_at is null and i.id_siswa_aktif = '".$id_siswa_aktif."' and tanggal = '".$tanggal."'","row");
        $data = null;
        $radius = $ibadah->radius;
        if (empty($radius)) {
          // $radius = 0.1;
        }

        $distance = $this->distance_haversine($latitude, $longitude, $ibadah->latitude, $ibadah->longitude);
        
        if ($distance > $radius && (!empty($get_ibadah->latitude) && !empty($get_ibadah->longitude)) && !empty($radius)){
          $msg = array('status' => 0, 'message'=>'Anda berada diluar radius lokasi (Jarak ke lokasi : '.number_format($distance,2,'.',',').' km)' ,'data'=>null);
          $status="200";
        }
        else{
          if (!empty($ibadah)) {
            $data = $ibadah;
          }
          if (!empty($cek_presensi) && !empty($data)) {
            $data->id_ibadah = $cek_presensi->id;
            $data->is_ibadah = true;
            $msg = array('status' => 1, 'message'=>'Sudah melakukan Presensi ibadah terkait' ,'data'=>$data);
            $status="200";
          }
          else if(!empty($data)){
            $data->id_ibadah = null;
            $data->is_ibadah = false;
            $msg = array('status' => 1, 'message'=>'Belum melakukan Presensi ibadah terkait' ,'data'=>$data);
            $status="200";
          }
          else{
            $msg = array('status' => 0, 'message'=>'Belum memasuki waktu ibadah' ,'data'=>null);
            $status="200";
          }
        }

        $this->response($msg,$status);
    }

    function distance_haversine(
      $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
      //miles : 3959.0
      //kilometers : 6371
      //meters : 6371000
      // convert from degrees to radians
      $latFrom = deg2rad($latitudeFrom);
      $lonFrom = deg2rad($longitudeFrom);
      $latTo = deg2rad($latitudeTo);
      $lonTo = deg2rad($longitudeTo);
    
      $latDelta = $latTo - $latFrom;
      $lonDelta = $lonTo - $lonFrom;
    
      $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
      return $angle * $earthRadius;
    }
}
