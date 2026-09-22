<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cek_ibadah_tipe extends REST_Controller {
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

        $ibadah = $this->mymodel->withquery("select * from ibadah_setting where start_ibadah <= '".$waktu."' and end_ibadah >= '".$waktu."'","result");
        $cek_presensi = $this->mymodel->withquery("select i.id, i.id_siswa_aktif, s.nama_lengkap, k.label as nama_kelas, i.jenjang, i.id_ibadah, p.ibadah, i.tanggal, i.waktu, i.jumlah_rakaat, i.keterangan, i.longitude, i.latitude from ibadah i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif and s.deleted_at is null left join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." join ibadah_setting p on i.id_ibadah = p.id where i.deleted_at is null and i.id_siswa_aktif = '".$id_siswa_aktif."' and tanggal = '".$tanggal."'","row");
        $data = null;
        
        if(!empty($ibadah)){
            foreach ($ibadah as $key => $value) {
                $radius = $value->radius;
                if (empty($radius)) {
                    $radius = 0.1;
                }
                $distance = $this->distance_haversine($latitude, $longitude, $value->latitude, $value->longitude);
                //jika diluar radius hapus dari data
                if ($distance > $radius && (!empty($get_ibadah->latitude) && !empty($get_ibadah->longitude)) && !empty($radius)) {
                    unset($ibadah[$key]);
                    $ibadah = array_values($ibadah);
                }
                $cek_presensi = $this->mymodel->withquery("select i.id, i.id_siswa_aktif, s.nama_lengkap, k.label as nama_kelas, i.jenjang, i.id_ibadah, p.ibadah, i.tanggal, i.waktu, i.jumlah_rakaat, i.keterangan, i.longitude, i.latitude from ibadah i 
                join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif and s.deleted_at is null 
                left join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." 
                join ibadah_setting p on i.id_ibadah = p.id 
                where i.deleted_at is null and i.id_siswa_aktif = '".$id_siswa_aktif."' and tanggal = '".$tanggal."' and i.id_ibadah = '".$value->id."'","row");
                if(!empty($cek_presensi)){
                    $value->id_ibadah = $cek_presensi->id_ibadah;
                    $value->is_ibadah = true;
                }
                else if(empty($cek_presensi)){
                    $value->id_ibadah = $value->id;
                    $value->is_ibadah = false;
                }
            }

            $data = $ibadah;
        }

        $msg = array('status' => 1, 'message'=>'Success loading data' ,'data'=>$data);
        $status="200";
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
