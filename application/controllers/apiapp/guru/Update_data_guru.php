<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Update_data_guru extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }

  function index_post()
  {
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[strtolower($name)] = $value;
    }
    if (isset($headers['x-token'])) $token =  $headers['x-token'];
    $id = $this->post('id_guru');
    $jenjang = $this->post('jenjang');

    if($jenjang=='sd'){
      $guru="guru_sd";
      $guru="guru_sd";
    }elseif($jenjang=='smp'){
      $guru="guru_smp";
      $guru="guru_smp";
    }elseif($jenjang=='sma'){
      $guru="guru_sma";
      $guru="guru_sma";
    }else{
      $guru="guru_ft";
      $guru="guru_ft";
    }

    $data = array(
        'nama_lengkap' => $this->post('nama_lengkap'),
				'nik' => $this->post('nik'),
				'nuptk' => $this->post('nuptk'),
				'npp' => $this->post('npp'),
				'npwp' => $this->post('npwp'),
				'alamat' => $this->post('alamat'),
				'agama' => $this->post('agama'),
				'jenis_kelamin' => $this->post('jenis_kelamin'),
				'status_menikah' => $this->post('status_menikah'),
				'jumlah_anak' => $this->post('jumlah_anak'),
				'no_telp' => $this->post('no_telp'),
				'email' => $this->post('email'),
				'id_posisi' => $this->post('id_posisi'),
				'unit' => $this->post('unit'),
				'id_mapel' => $this->post('id_mapel'),
				'status_kepegawaian' => $this->post('status_kepegawaian'),
				'informasi_kepala_pimpinan' => $this->post('informasi_kepala_pimpinan'),
        'keterangan_jabatan' => $this->post('keterangan_jabatan'),
        'jam_ajar' => $this->post('jam_ajar'),
        'pendidikan_terakhir' => $this->post('pendidikan_terakhir'),
        'universitas' => $this->post('universitas'),
        'jurusan' => $this->post('jurusan'),
        'tahun_lulus' => $this->post('tahun_lulus'),
    );

    //Foto profil
    if (!empty($_FILES['foto_profil']['name'])) {
      $uploaddir = './uploads/'.$guru.'/';
      $img = explode('.', $_FILES['foto_profil']['name']);
      $extension = end($img);
      $file_name =  md5(date('y-m-d h:i:s').$_FILES['foto_profil']['name']).".".$extension;
      $uploadfile = $uploaddir.$file_name;
      $status = 0;
      if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $uploadfile)) {
        $data['foto_profil'] = $file_name;
        $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
      }
    }

    if (!empty($data)) {
      $update_guru = $this->mymodel->update($guru, $data, "id_guru", $id);
    }

    if ($update_guru == -1) {
      $msg = array('status' => 0, 'message' => 'Gagal update data guru ', 'data' => array());
    } else {
      $msg = array('status' => 1, 'message' => 'Berhasil update data', 'data' => array());
    }
    $this->response($msg);
  }
}
