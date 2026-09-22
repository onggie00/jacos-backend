<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Presensi_ibadah extends REST_Controller {
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
        $id_ibadah = $this->post("id_ibadah");
        $rakaat = $this->post("rakaat");
        $keterangan = $this->post("keterangan");
        $longitude = $this->post("longitude");
        $latitude = $this->post("latitude");
        
        //cek jarak antara lokasi user dan destinasi
        $get_ibadah = $this->mymodel->withquery("select * from ibadah_setting where id = '".$id_ibadah."'","row");
        $radius = $get_ibadah->radius;
        if (empty($radius)) {
          $radius = 0.1;
        }
        if (!empty($longitude) && !empty($latitude)) {
          $distance = $this->distance_haversine($latitude, $longitude, $get_ibadah->latitude, $get_ibadah->longitude);
          if ($distance > $radius && (!empty($get_ibadah->latitude) && !empty($get_ibadah->longitude)) && !empty($radius)) {
            $msg = array('status' => 0, 'message'=>'Anda berada diluar radius lokasi (Jarak ke lokasi: '.number_format($distance,2,'.',',').' km)' ,'data'=>null);
            $status="200";
          }
          else{
            $data_insert = array(
              "id_siswa_aktif" => $id_siswa_aktif,
              "jenjang" => $jenjang,
              "tanggal" => $tanggal,
              "waktu" => $waktu,
              "id_ibadah" => $id_ibadah,
              "jumlah_rakaat" => $rakaat,
              "keterangan" => $keterangan,
              "longitude" => (!empty($longitude)) ? $longitude : null,
              "latitude" => (!empty($latitude)) ? $latitude : null
            );
            $cek_presensi = $this->mymodel->withquery("select i.id, i.id_siswa_aktif, s.nama_lengkap, k.label as nama_kelas, i.jenjang, i.id_ibadah, p.ibadah, i.tanggal, i.waktu, i.jumlah_rakaat, i.keterangan from ibadah i join siswa_".$jenjang."_aktif s on i.id_siswa_aktif = s.id_siswa_".$jenjang."_aktif and s.deleted_at is null join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." join ibadah_setting p on i.id_ibadah = p.id 
            where i.tanggal = '".$tanggal."' and i.id_ibadah = '".$id_ibadah."' and i.deleted_at is null and i.id_siswa_aktif = '".$id_siswa_aktif."'","row");
    
            if (!empty($cek_presensi)) {
              $data_insert["id"] = $cek_presensi->id;
              $data_insert['tanggal'] = $cek_presensi->tanggal;
              $data_insert["waktu"] = $cek_presensi->waktu;
              $data_insert["keterangan"] = $cek_presensi->keterangan;
              $data_insert["jumlah_rakaat"] = $cek_presensi->jumlah_rakaat;
              $msg = array('status' => 1, 'message'=>'Sudah melakukan Presensi ibadah terkait' ,'data'=>$data_insert);
              $status="200";
            }
            else if(empty($cek_presensi)){
              $insert = $this->mymodel->insertid("ibadah", $data_insert);
              $data_insert["id"] = $insert;
              $msg = array('status' => 1, 'message'=>'Berhasil melakukan presensi ibadah (Jarak : '.number_format($distance,2,'.',',').' km)' ,'data'=>$data_insert);
              $status="200";
            }
            else{
              $msg = array('status' => 0, 'message'=>'Data Input tidak valid' ,'data'=>array());
              $status="200";
            }
          }
        }
        else{
          $msg = array('status' => 0, 'message'=>'Longitude & Latitude Kosong, Pastikan mengaktifkan Geolokasi' ,'data'=>null);
          $status="200";
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

    public static function distance_vincent(
      $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
      //miles : 3959.0
      //kilometers : 6371
      //meters : 6371000
      // convert from degrees to radians
      $latFrom = deg2rad($latitudeFrom);
      $lonFrom = deg2rad($longitudeFrom);
      $latTo = deg2rad($latitudeTo);
      $lonTo = deg2rad($longitudeTo);
    
      $lonDelta = $lonTo - $lonFrom;
      $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
      $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
    
      $angle = atan2(sqrt($a), $b);
      return $angle * $earthRadius;
    }

}
