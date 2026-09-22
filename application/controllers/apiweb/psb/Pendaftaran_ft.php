<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Pendaftaran_ft extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }

    public function cek_notelp($nohp){
        $nohp = trim($nohp);
        // hapus semua non-digit (spasi, dash, kurung, +)
        $nohp = preg_replace('/[^0-9]/', '', $nohp);
        if($nohp == '') return '';
        // jika mulai 0 → asumsi lokal Indonesia → ganti 0 dengan 62
        if(substr($nohp, 0, 1) == '0'){
            $nohp = '62' . substr($nohp, 1);
        }
        return $nohp;
    }
    
    public function index_post()
    {
      // === VALIDASI FIELD WAJIB ===
      $required_fields = array(
        'nama_lengkap' => 'Nama Lengkap',
        'email' => 'Email',
        'nisn' => 'NISN',
        'tempat_lahir' => 'Tempat Lahir',
        'tgl_lahir' => 'Tanggal Lahir',
        'jenis_kelamin' => 'Jenis Kelamin',
        'agama' => 'Agama',
        'nama_ibu' => 'Nama Ibu',
        'pekerjaan_ibu' => 'Pekerjaan Ibu',
        'notelp_ibu' => 'No. Telp Ibu',
        'nama_ayah' => 'Nama Ayah',
        'pekerjaan_ayah' => 'Pekerjaan Ayah',
        'notelp_ayah' => 'No. Telp Ayah',
        'alamat' => 'Alamat',
        'kode_pos' => 'Kode Pos',
        'sumber_informasi' => 'Sumber Informasi',
      );
      if ($this->post('ppsbb') !== '1') {
        $required_fields['alasan_tertarik'] = 'Alasan Tertarik';
        $required_fields['peminatan_ft'] = 'Peminatan FT';
      }
      $empty_fields = array();
      foreach ($required_fields as $key => $label) {
        if ($this->post($key) === '' || $this->post($key) === null) {
          $empty_fields[] = $label;
        }
      }

      // Validasi format tgl_lahir
      if (!empty($this->post('tgl_lahir'))) {
        $d = DateTime::createFromFormat('Y-m-d', $this->post('tgl_lahir'));
        if (!$d || $d->format('Y-m-d') !== $this->post('tgl_lahir')) {
          $empty_fields[] = 'Tanggal Lahir (format salah, gunakan YYYY-MM-DD)';
        }
      }

      // Validasi file wajib
      $required_files = array(
        'foto_peserta' => 'Foto Peserta',
        'akte_lahir' => 'Akte Lahir',
        'kartu_keluarga' => 'Kartu Keluarga'
      );
      foreach ($required_files as $key => $label) {
        if (empty($_FILES[$key]['name'])) {
          $empty_fields[] = $label . ' (file wajib diupload)';
        }
      }

      // Validasi file raport wajib
      for ($i = 1; $i <= 4; $i++) {
        $field = ($i == 1) ? 'file_raport' : 'file_raport' . $i;
        if (empty($_FILES[$field]['name'])) {
          $empty_fields[] = 'File Raport ' . $i . ' (file wajib diupload)';
        }
      }

      // Validasi nilai raport wajib
      $raport_fields = array(
        'b_inggris' => 'Nilai B.Inggris Sem 1',
        'b_inggris2' => 'Nilai B.Inggris Sem 2',
        'b_inggris3' => 'Nilai B.Inggris Sem 3',
        'b_inggris4' => 'Nilai B.Inggris Sem 4',
        'ipa' => 'Nilai IPA Sem 1',
        'ipa2' => 'Nilai IPA Sem 2',
        'ipa3' => 'Nilai IPA Sem 3',
        'ipa4' => 'Nilai IPA Sem 4',
        'matematika' => 'Nilai Matematika Sem 1',
        'matematika2' => 'Nilai Matematika Sem 2',
        'matematika3' => 'Nilai Matematika Sem 3',
        'matematika4' => 'Nilai Matematika Sem 4'
      );
      if( $this->post('ppsbb') !== '1') {
        $raport_fields = array_merge($raport_fields, array(
          'b_indonesia' => 'Nilai B.Indonesia Sem 1',
          'b_indonesia2' => 'Nilai B.Indonesia Sem 2',
          'b_indonesia3' => 'Nilai B.Indonesia Sem 3',
          'b_indonesia4' => 'Nilai B.Indonesia Sem 4',
          'ips' => 'Nilai IPS Sem 1',
          'ips2' => 'Nilai IPS Sem 2',
          'ips3' => 'Nilai IPS Sem 3',
          'ips4' => 'Nilai IPS Sem 4'
        ));
      }
      foreach ($raport_fields as $key => $label) {
        if ($this->post($key) === '' || $this->post($key) === null) {
          $empty_fields[] = $label;
        }
      }

      // Validasi PPSBB
      if ($this->post('ppsbb') == '1' && empty($this->post('jenis_ppsbb'))) {
        $empty_fields[] = 'Jenis PPSBB (wajib jika PPSBB dipilih)';
      }

      // Validasi kuesioner (step 5): field "kuesioner" = JSON string
      // [{"id_step_pertanyaan":1,"jawaban":[{"id_web_psb_item":1},{"id_web_psb_item":3}]}, ...]
      $kuesioner_rows = array();
      $kuesioner_list = json_decode((string) $this->post('kuesioner'), true);
      if (!is_array($kuesioner_list) || empty($kuesioner_list)) {
        $empty_fields[] = 'Kuesioner (format tidak valid)';
      }
      else {
        $step_pertanyaan = $this->mymodel->withquery("select id_step_pertanyaan, no_urut, min_input, max_input from web_psb_step_pertanyaan where step = '5'","result");
        $map_pertanyaan = array();
        foreach ($step_pertanyaan as $sp) {
          $map_pertanyaan[intval($sp->id_step_pertanyaan)] = $sp;
        }
        $seen_pertanyaan = array();
        foreach ($kuesioner_list as $k) {
          $id_pertanyaan = isset($k['id_step_pertanyaan']) ? intval($k['id_step_pertanyaan']) : 0;
          if (!isset($map_pertanyaan[$id_pertanyaan]) || isset($seen_pertanyaan[$id_pertanyaan])) {
            $empty_fields[] = 'Kuesioner (pertanyaan tidak valid atau duplikat: ' . $id_pertanyaan . ')';
            continue;
          }
          $seen_pertanyaan[$id_pertanyaan] = true;
          $sp = $map_pertanyaan[$id_pertanyaan];
          $jawaban = isset($k['jawaban']) && is_array($k['jawaban']) ? $k['jawaban'] : array();
          $min = intval($sp->min_input);
          $max = intval($sp->max_input);
          if (count($jawaban) < $min || count($jawaban) > $max) {
            $empty_fields[] = 'Kuesioner pertanyaan no.' . intval($sp->no_urut) . ' (jawaban harus ' . $min . '-' . $max . ' pilihan)';
            continue;
          }
          $seen_item = array();
          foreach ($jawaban as $j) {
            $id_item = isset($j['id_web_psb_item']) ? intval($j['id_web_psb_item']) : 0;
            if ($id_item <= 0 || isset($seen_item[$id_item])) {
              $empty_fields[] = 'Kuesioner pertanyaan no.' . intval($sp->no_urut) . ' (jawaban tidak valid atau duplikat)';
              continue;
            }
            $seen_item[$id_item] = true;
            $cek_item = $this->mymodel->withquery("select id_web_psb_item from web_psb_step_item where id_web_psb_item = '".$id_item."' and id_web_psb_pertanyaan = '".$id_pertanyaan."'","row");
            if (empty($cek_item)) {
              $empty_fields[] = 'Kuesioner pertanyaan no.' . intval($sp->no_urut) . ' (opsi ' . $id_item . ' tidak ditemukan)';
              continue;
            }
            $teks_lainnya = isset($j['teks_lainnya']) ? trim((string) $j['teks_lainnya']) : '';
            $kuesioner_rows[] = array(
              'id_step_pertanyaan' => $id_pertanyaan,
              'id_web_psb_item'    => $id_item,
              'teks_lainnya'       => ($teks_lainnya !== '' ? substr($teks_lainnya, 0, 255) : null)
            );
          }
        }
        if (count($seen_pertanyaan) < count($map_pertanyaan)) {
          $empty_fields[] = 'Kuesioner (belum semua pertanyaan dijawab)';
        }
      }

      // Validasi prestasi terstruktur (khusus ppsbb=1): field "prestasi" = JSON string
      // [{"id_jenis_prestasi":1,"id_jenis_jenjang":2,"id_keterangan_prestasi":1,"keterangan_lainnya":null,"nama_prestasi":"...","jenis_lomba":"...","tahun_prestasi":2025}, ...]
      $prestasi_rows = array();
      if ($this->post('ppsbb') == '1') {
        $prestasi_list = json_decode((string) $this->post('prestasi'), true);
        if (is_array($prestasi_list) && !empty($prestasi_list)) {
          $tahun_max = intval(date('Y'));
          $tahun_min = $tahun_max - 4;
          foreach ($prestasi_list as $p) {
            if (!is_array($p)) {
              $empty_fields[] = 'Prestasi (format tidak valid)';
              continue;
            }
            $id_jenis = isset($p['id_jenis_prestasi']) ? intval($p['id_jenis_prestasi']) : 0;
            $id_jenjang = isset($p['id_jenis_jenjang']) ? intval($p['id_jenis_jenjang']) : 0;
            $id_keterangan = isset($p['id_keterangan_prestasi']) ? intval($p['id_keterangan_prestasi']) : 0;
            $nama_prestasi = isset($p['nama_prestasi']) ? trim((string) $p['nama_prestasi']) : '';
            $jenis_lomba = isset($p['jenis_lomba']) ? trim((string) $p['jenis_lomba']) : '';
            $tahun = isset($p['tahun_prestasi']) ? intval($p['tahun_prestasi']) : 0;
            $keterangan_lainnya = isset($p['keterangan_lainnya']) ? trim((string) $p['keterangan_lainnya']) : '';

            if ($id_jenis <= 0 || empty($this->mymodel->withquery("select id_jenis_prestasi from jenis_prestasi where id_jenis_prestasi = '" . $id_jenis . "'", "row"))) {
              $empty_fields[] = 'Prestasi (tingkat prestasi tidak valid: ' . $id_jenis . ')';
              continue;
            }
            if ($id_jenjang <= 0 || empty($this->mymodel->withquery("select id_jenis_jenjang from jenis_jenjang where id_jenis_jenjang = '" . $id_jenjang . "'", "row"))) {
              $empty_fields[] = 'Prestasi (jenjang tidak valid: ' . $id_jenjang . ')';
              continue;
            }
            if ($id_keterangan <= 0 || empty($this->mymodel->withquery("select id_keterangan_prestasi from keterangan_prestasi where id_keterangan_prestasi = '" . $id_keterangan . "'", "row"))) {
              $empty_fields[] = 'Prestasi (keterangan tidak valid: ' . $id_keterangan . ')';
              continue;
            }
            if ($id_keterangan == 4 && $keterangan_lainnya === '') {
              $empty_fields[] = 'Prestasi (keterangan Lainnya wajib diisi teksnya)';
              continue;
            }
            if ($nama_prestasi === '') {
              $empty_fields[] = 'Prestasi (nama prestasi wajib diisi)';
              continue;
            }
            if ($tahun < $tahun_min || $tahun > $tahun_max) {
              $empty_fields[] = 'Prestasi (tahun harus ' . $tahun_min . ' s/d ' . $tahun_max . ')';
              continue;
            }
            $prestasi_rows[] = array(
              'id_jenis_prestasi'      => $id_jenis,
              'id_jenis_jenjang'       => $id_jenjang,
              'id_keterangan_prestasi' => $id_keterangan,
              'keterangan_lainnya'     => ($id_keterangan == 4) ? substr($keterangan_lainnya, 0, 255) : null,
              'nama_prestasi'          => substr($nama_prestasi, 0, 255),
              'jenis_lomba'            => ($jenis_lomba !== '') ? substr($jenis_lomba, 0, 255) : null,
              'tahun_prestasi'         => $tahun
            );
          }
        }
        if (!empty($prestasi_rows)) {
          $file_prestasi_count = isset($_FILES['file_prestasi']['name']) ? count($_FILES['file_prestasi']['name']) : 0;
          if ($file_prestasi_count < count($prestasi_rows)) {
            $empty_fields[] = 'File Prestasi (file wajib untuk setiap item prestasi)';
          }
        }
      }

      if (!empty($empty_fields)) {
        $msg = array(
          'success' => 0,
          'message' => 'Data berikut wajib diisi: ' . implode(', ', $empty_fields),
          'data' => array('missing_fields' => $empty_fields)
        );
        $this->response($msg, 200);
        return;
      }

      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $token_user = md5($this->post("email")." ".date("YmdHis"));
        $tahun_ajar_psb = $this->mymodel->getbywhere("tahun_ajaran_psb", "id",1,"row")->label;
        $kode_tahun = substr($tahun_ajar_psb,2,2);
        $kode_tahun = $kode_tahun.substr($tahun_ajar_psb,7,2);
        $sekolah_asal = "";
        //get tahun ajaran
        $tahun_ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran_psb where id = '1'","row")->label;
        $kelas_psb = ($this->post('ppsbb') == '1') ? 'France Track Jalur Prestasi' : 'France Track';
        $periode_row = $this->mymodel->withquery("select gelombang from web_periode_daftar where periode_pendaftaran_mulai <= '".date("Y-m-d H:i:s")."' and periode_pendaftaran_selesai >= '".date("Y-m-d H:i:s")."' and kelas = '".$kelas_psb."' ","row");
        $gelombang = !empty($periode_row) ? $periode_row->gelombang : null;
        if (empty($gelombang)) {
          $msg = array(
            'success' => 0,
            'message' => 'Tidak ada gelombang pendaftaran yang aktif untuk kelas '.ucfirst($kelas_psb).'. Silakan hubungi panitia.',
          );
          $this->response($msg, 200);
          return;
        }
          if ($this->post("sekolah_asal") > 0) {
            $sekolah_asal = $this->mymodel->getbywhere("list_sekolah_smp", "id_list_sekolah_smp",$this->post("sekolah_asal"),"row")->nama_sekolah;
            if(!empty($this->post("sekolah_asal_lainnya"))){
              $sekolah_asal = $this->post("sekolah_asal_lainnya");
            }
          }
          else if(!empty($this->post("sekolah_asal_lainnya"))){
            $sekolah_asal = $this->post("sekolah_asal_lainnya");
          }
          else if(empty($this->post("sekolah_asal"))){
            $sekolah_asal = null;
          }
          else{
            $sekolah_asal = $this->post("sekolah_asal");
          }
          $pekerjaan_ibu = "";
          $pekerjaan_ayah = "";
          if ($this->post("pekerjaan_ayah") > 0) {
            $pekerjaan_ayah = $this->mymodel->withquery("select * from pekerjaan_ortu where id_pekerjaan_ortu = '".$this->post("pekerjaan_ayah")."'","row")->nama_pekerjaan;
            if (!empty($this->post("pekerjaan_ayah_lainnya"))) {
              $pekerjaan_ayah = $this->post("pekerjaan_ayah_lainnya");
            }
          }
          else if(!empty($this->post("pekerjaan_ayah_lainnya"))){
            $pekerjaan_ayah = $this->post("pekerjaan_ayah_lainnya");
          }
          else{
            $pekerjaan_ayah = $this->post("pekerjaan_ayah");
          }
          if ($this->post("pekerjaan_ibu") > 0) {
            $pekerjaan_ibu = $this->mymodel->withquery("select * from pekerjaan_ortu where id_pekerjaan_ortu = '".$this->post("pekerjaan_ibu")."'","row")->nama_pekerjaan;
            if (!empty($this->post("pekerjaan_ibu_lainnya"))) {
              $pekerjaan_ibu = $this->post("pekerjaan_ibu_lainnya");
            }
          }
          else if(!empty($this->post("pekerjaan_ibu_lainnya"))){
            $pekerjaan_ibu = $this->post("pekerjaan_ibu_lainnya");
          }
          else{
            $pekerjaan_ibu = $this->post("pekerjaan_ibu");
          }
          $data = array(
            "nama_lengkap" => trim(ucwords(strtolower($this->post("nama_lengkap"))), " "),
            "email" => $this->post("email"),
            "email_ms_office" => "",
            "email_ms_office_ortu" => "",
            "token" => $token_user,
            "nik" => $this->post("nik"),
            //"nisn" => $this->post("nisn"),
            "tahun_ajaran" => $tahun_ajaran_aktif,
            "gelombang" => $gelombang,
            "tempat_lahir" => $this->post("tempat_lahir"),
            "tgl_lahir" => (($d = DateTime::createFromFormat('Y-m-d', $this->post("tgl_lahir"))) && $d->format('Y-m-d') === $this->post("tgl_lahir")) ? $this->post("tgl_lahir") : null,
            "jenis_kelamin" => $this->post("jenis_kelamin"),
            "agama" => $this->post("agama"),
            "nama_ibu" => $this->post("nama_ibu"),
            "pekerjaan_ibu" => $pekerjaan_ibu,
            "notelp_ibu" => $this->cek_notelp($this->post("notelp_ibu")),
            "nama_ayah" => $this->post("nama_ayah"),
            "pekerjaan_ayah" => $pekerjaan_ayah,
            "notelp_ayah" => $this->cek_notelp($this->post("notelp_ayah")),
            "alamat" => $this->post("alamat"),
            "kelurahan" => $this->post("kelurahan"),
            "kecamatan" => $this->post("kecamatan"),
            "kota" => $this->post("kota"),
            "provinsi" => $this->post("provinsi"),
            "kode_pos" => $this->post("kode_pos"),
            "sekolah_asal" => $sekolah_asal,
            "sumber_informasi" => $this->post("sumber_informasi"),
            "alasan_tertarik" => $this->post("alasan_tertarik"),
            "status_lulus" => 1,
            "no_transaksi" => 'PENDING',
            "va_number" => 'PENDING',
            "token_expired" => date("Y-m-d H:i:s", strtotime("+3 days")),
            "peminatan_ft" => !empty($this->post("peminatan_ft")) ? $this->post("peminatan_ft") : null,
            "ppsbb" => $this->post("ppsbb"),
            "jenis_ppsbb" => !empty($this->post("jenis_ppsbb")) ? $this->post("jenis_ppsbb") : null
          );
          //"is_ft" => 1,

          // NISN sudah divalidasi di awal
          $data['nisn'] = $this->post('nisn');
          if (!empty($this->post('npsn'))) {
            $data['npsn'] = $this->post("npsn");
          }

          //validasi tipe_ppsbb (jenis_ppsbb kosong sudah divalidasi di awal)
          if ($this->post("ppsbb") == "1") {
            $cek_tipe_ppsbb = $this->mymodel->withquery("select id_tipe_ppsbb from tipe_ppsbb where id_tipe_ppsbb = '".$this->post("jenis_ppsbb")."'", "row");
            if (empty($cek_tipe_ppsbb)) {
              $msg = array('success'=>0,'message'=>'Jenis PPSBB tidak valid','data'=>[]);
              $this->response($msg,'200');
              return;
            }
          }

          // pastikan folder upload tersedia (server bisa tidak punya folder ini)
          foreach (array('./uploads/siswa_ft/', './uploads/siswa_ft/raport/') as $dir) {
            if (!is_dir($dir)) {
              mkdir($dir, 0777, true);
            }
          }

          //foto peserta
          if (!empty($_FILES['foto_peserta']['name'])) {
            $uploaddir = './uploads/siswa_ft/';
            $img = explode('.', $_FILES['foto_peserta']['name']);
            $extension = end($img);
            $file_name =  md5(date('y-m-d h:i:s').$_FILES['foto_peserta']['name']).".".$extension;
            $uploadfile = $uploaddir.$file_name;
            $status = 0;
              if (move_uploaded_file($_FILES['foto_peserta']['tmp_name'], $uploadfile)) {
                $data['foto_peserta'] = $file_name;
                $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
              }else{
                $msg = array('success'=>0,'message'=>'Upload Foto Gagal','data'=>[]);
                $this->response($msg,'200');
              }
          }
          //foto akte lahir
          if (!empty($_FILES['akte_lahir']['name'])) {
            $uploaddir = './uploads/siswa_ft/';
            $img = explode('.', $_FILES['akte_lahir']['name']);
            $extension = end($img);
            $file_name =  md5(date('y-m-d h:i:s').$_FILES['akte_lahir']['name']).".".$extension;
            $uploadfile = $uploaddir.$file_name;
            $status = 0;
              if (move_uploaded_file($_FILES['akte_lahir']['tmp_name'], $uploadfile)) {
                $data['akte_lahir'] = $file_name;
                $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
              }else{
                $msg = array('success'=>0,'message'=>'Upload File Gagal','data'=>[]);
                $this->response($msg,'200');
              }
          }
          //foto kartu keluarga
          if (!empty($_FILES['kartu_keluarga']['name'])) {
            $uploaddir = './uploads/siswa_ft/';
            $img = explode('.', $_FILES['kartu_keluarga']['name']);
            $extension = end($img);
            $file_name =  md5(date('y-m-d h:i:s').$_FILES['kartu_keluarga']['name']).".".$extension;
            $uploadfile = $uploaddir.$file_name;
            $status = 0;
              if (move_uploaded_file($_FILES['kartu_keluarga']['tmp_name'], $uploadfile)) {
                $data['kartu_keluarga'] = $file_name;
                $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
              }else{
                $msg = array('success'=>0,'message'=>'Upload File Gagal','data'=>[]);
                $this->response($msg,'200');
              }
          }

            //foto Raport 1 - 4
            if (!empty($_FILES['file_raport']['name'])) {
              $uploaddir = './uploads/siswa_ft/raport/';
              $img = explode('.', $_FILES['file_raport']['name']);
              $extension = end($img);
              $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_raport']['name']).".".$extension;
              $uploadfile = $uploaddir.$file_name;
              $status = 0;
                if (move_uploaded_file($_FILES['file_raport']['tmp_name'], $uploadfile)) {
                  $file1 = $file_name;
                  $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
                }else{
                  $msg = array('success'=>0,'message'=>'File Raport 1 Gagal','data'=>[]);
                  $this->response($msg,'200');
                }
            }else{
              $msg = array('success'=>0,'message'=>'File Raport 1 kosong','data'=>[]);
              $this->response($msg,'200');
            }

            if (!empty($_FILES['file_raport2']['name'])) {
              $uploaddir = './uploads/siswa_ft/raport/';
              $img = explode('.', $_FILES['file_raport2']['name']);
              $extension = end($img);
              $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_raport2']['name']).".".$extension;
              $uploadfile = $uploaddir.$file_name;
              $status = 0;
                if (move_uploaded_file($_FILES['file_raport2']['tmp_name'], $uploadfile)) {
                  $file2 = $file_name;
                  $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
                }else{
                  $msg = array('success'=>0,'message'=>'File Raport 2 Gagal','data'=>[]);
                  $this->response($msg,'200');
                }
            }else{
              $msg = array('success'=>0,'message'=>'File Raport 2 kosong','data'=>[]);
              $this->response($msg,'200');
            }

            if (!empty($_FILES['file_raport3']['name'])) {
              $uploaddir = './uploads/siswa_ft/raport/';
              $img = explode('.', $_FILES['file_raport3']['name']);
              $extension = end($img);
              $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_raport3']['name']).".".$extension;
              $uploadfile = $uploaddir.$file_name;
              $status = 0;
                if (move_uploaded_file($_FILES['file_raport3']['tmp_name'], $uploadfile)) {
                  $file3 = $file_name;
                  $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
                }else{
                  $msg = array('success'=>0,'message'=>'File Raport 3 Gagal','data'=>[]);
                  $this->response($msg,'200');
                }
            }else{
              $msg = array('success'=>0,'message'=>'File Raport 3 kosong','data'=>[]);
              $this->response($msg,'200');
            }

            if (!empty($_FILES['file_raport4']['name'])) {
              $uploaddir = './uploads/siswa_ft/raport/';
              $img = explode('.', $_FILES['file_raport4']['name']);
              $extension = end($img);
              $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_raport4']['name']).".".$extension;
              $uploadfile = $uploaddir.$file_name;
              $status = 0;
                if (move_uploaded_file($_FILES['file_raport4']['tmp_name'], $uploadfile)) {
                  $file4 = $file_name;
                  $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
                }else{
                  $msg = array('success'=>0,'message'=>'File Raport 4 Gagal','data'=>[]);
                  $this->response($msg,'200');
                }
            }else{
              $msg = array('success'=>0,'message'=>'File Raport 4 kosong','data'=>[]);
              $this->response($msg,'200');
            }
        

          //cek email duplicate
          $cek_email = $this->mymodel->withquery("select * from siswa_ft where email = '".$this->post("email")."' and nama_lengkap like '%".$this->db->escape_like_str($this->post("nama_lengkap"))."%' and tgl_lahir = '".(($d = DateTime::createFromFormat('Y-m-d', $this->post("tgl_lahir"))) && $d->format('Y-m-d') === $this->post("tgl_lahir") ? $this->post("tgl_lahir") : '1970-01-01')."'", "row");
          if(!empty($cek_email)){
            $msg = array('status' => 0, 'message'=>'Email "'.$this->input->post('email').'" sudah digunakan, silahkan menggunakan email lain.' ,'data'=>$cek_email, 'data_raport' => array(), 'transaksi' => array());
            $status="200";
          }
          else if (!empty($data) && empty($cek_email)) {
            //insert siswa
            $this->db->trans_begin();
            $id_siswa = $this->mymodel->insertid("siswa_ft",$data);
            if (!empty($id_siswa)) {
              $data['id_siswa'] = $id_siswa;

              // Upload & simpan prestasi terstruktur (khusus ppsbb=1, opsional)
              if ((int)$this->post("ppsbb") === 1 && !empty($prestasi_rows)) {
                $prestasi_dir = './uploads/siswa_ft/' . $id_siswa . '/prestasi/';
                if (!is_dir($prestasi_dir)) {
                  mkdir($prestasi_dir, 0777, true);
                  chmod($prestasi_dir, 0777);
                }
                $allowed_prestasi_ext = array('pdf', 'png', 'jpg', 'jpeg');
                foreach ($prestasi_rows as $pi => $prow) {
                  if (empty($_FILES['file_prestasi']['name'][$pi])) {
                    $this->db->trans_rollback();
                    $msg = array('status' => 0, 'message' => 'File prestasi kosong untuk item ke-' . ($pi + 1), 'data' => array(), 'data_raport' => array(), 'transaksi' => array());
                    $status = "200";
                    $this->response($msg, $status);
                    return;
                  }
                  $pext = strtolower(pathinfo($_FILES['file_prestasi']['name'][$pi], PATHINFO_EXTENSION));
                  if (!in_array($pext, $allowed_prestasi_ext)) {
                    $this->db->trans_rollback();
                    $msg = array('status' => 0, 'message' => 'Format file prestasi tidak valid: ' . $_FILES['file_prestasi']['name'][$pi] . ' (pdf/png/jpg/jpeg saja)', 'data' => array(), 'data_raport' => array(), 'transaksi' => array());
                    $status = "200";
                    $this->response($msg, $status);
                    return;
                  }
                  $phash = md5(date('Y-m-d H:i:s') . $id_siswa . $_FILES['file_prestasi']['name'][$pi]) . '.' . $pext;
                  $ptarget = $prestasi_dir . $phash;
                  if (move_uploaded_file($_FILES['file_prestasi']['tmp_name'][$pi], $ptarget)) {
                    $this->mymodel->insertid('sertifikat_prestasi_ft', array(
                      'id_siswa_ft'            => $id_siswa,
                      'id_jenis_prestasi'      => $prow['id_jenis_prestasi'],
                      'id_jenis_jenjang'       => $prow['id_jenis_jenjang'],
                      'id_keterangan_prestasi' => $prow['id_keterangan_prestasi'],
                      'keterangan_lainnya'     => $prow['keterangan_lainnya'],
                      'nama_prestasi'          => $prow['nama_prestasi'],
                      'jenis_lomba'            => $prow['jenis_lomba'],
                      'tahun_prestasi'         => $prow['tahun_prestasi'],
                      'nama_file_asli'         => $_FILES['file_prestasi']['name'][$pi],
                      'nama_file_hash'         => $phash,
                    ));
                  } else {
                    $this->db->trans_rollback();
                    $msg = array('status' => 0, 'message' => 'Upload prestasi gagal: ' . $_FILES['file_prestasi']['name'][$pi], 'data' => array(), 'data_raport' => array(), 'transaksi' => array());
                    $status = "200";
                    $this->response($msg, $status);
                    return;
                  }
                }
              }

              //isi nilai raport
              $data_raport = array(
                "id_siswa" => $id_siswa,
                "b_inggris" => $this->post("b_inggris"),
                "b_inggris2" => $this->post("b_inggris2"),
                "b_inggris3" => $this->post("b_inggris3"),
                "b_inggris4" => $this->post("b_inggris4"),
                "b_indonesia" => ($this->post("b_indonesia") !== null) ? $this->post("b_indonesia") : 0,
                "b_indonesia2" => ($this->post("b_indonesia2") !== null) ? $this->post("b_indonesia2") : 0,
                "b_indonesia3" => ($this->post("b_indonesia3") !== null) ? $this->post("b_indonesia3") : 0,
                "b_indonesia4" => ($this->post("b_indonesia4") !== null) ? $this->post("b_indonesia4") : 0,
                "ipa" => $this->post("ipa"),
                "ipa2" => $this->post("ipa2"),
                "ipa3" => $this->post("ipa3"),
                "ipa4" => $this->post("ipa4"),
                "ips" => ($this->post("ips") !== null) ? $this->post("ips") : 0,
                "ips2" => ($this->post("ips2") !== null) ? $this->post("ips2") : 0,
                "ips3" => ($this->post("ips3") !== null) ? $this->post("ips3") : 0,
                "ips4" => ($this->post("ips4") !== null) ? $this->post("ips4") : 0,
                "matematika" => $this->post("matematika"),
                "matematika2" => $this->post("matematika2"),
                "matematika3" => $this->post("matematika3"),
                "matematika4" => $this->post("matematika4"),
                "file_raport" => $file1,
                "file_raport2" => $file2,
                "file_raport3" => $file3,
                "file_raport4" => $file4,
              );
              $id_raport = $this->mymodel->insertid("nilai_raport_ft",$data_raport);
              //isi jawaban kuesioner step 5
              foreach ($kuesioner_rows as $kr) {
                $this->mymodel->insertid("siswa_ft_kuesioner",array(
                  'id_siswa_ft'        => $id_siswa,
                  'id_step_pertanyaan' => $kr['id_step_pertanyaan'],
                  'id_web_psb_item'    => $kr['id_web_psb_item'],
                  'teks_lainnya'       => $kr['teks_lainnya'],
                  'created_at'         => date("Y-m-d H:i:s")
                ));
              }
              //insert transaksi
              $ppsbb = $this->post("ppsbb");
              if ($ppsbb == "1") {
                $no_transaksi = "LI-PPSBBFT-".date("Ymd")."-".$id_siswa;
                //get biaya pendaftaran
                $get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran","jenjang","PPSBB FRANCE TRACK","row");
                $total_biaya = $get_biaya->nominal_pendaftaran;
                $tipe_pendaftaran_label = "PPSBB FRANCE TRACK";
              }
              else{
                $no_transaksi = "LI-FT-".date("Ymd")."-".$id_siswa;
                //get biaya pendaftaran
                $get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran","jenjang","FRANCE TRACK","row");
                $total_biaya = $get_biaya->nominal_pendaftaran;
                $tipe_pendaftaran_label = "PSB FT";
              }
              $data_transaksi = array(
                "no_transaksi" => $no_transaksi,
                "nama_bank" => 'BNI',
                "user_email" => $this->post('email'),
                "user_name" => $this->post('nama_lengkap'),
                "user_phone" => "",
                "description" => "Tagihan Pendaftaran " . $tipe_pendaftaran_label . " a.n " . strtoupper($this->post('nama_lengkap')),
                "id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
                "total_biaya" => $total_biaya,
                "status_transaksi" => "0",
                "created_at" => date("Y-m-d H:i:s"),
                "expired_datetime" => date("Y-m-d H:i:s", strtotime("+3 days"))
              );
              if (!empty($data_transaksi)) {
                $data['expired_datetime'] = $data_transaksi['expired_datetime'];
                //create billing
                $get_setting = $this->mymodel->getall("pengaturan_akun");
                foreach ($get_setting as $key => $value) {
                  if ($value->name_setting == "bni_client_id_ft") {
                    $client_id = $value->value;
                  }
                  
                  if ($value->name_setting == "bni_prefix") {
                    $prefix = $value->value;
                  }
                }
                //digit jalur VA: prestasi (PPSBB) = 8, jalur tes = 9
                $digit_jalur = "9";
                if ($ppsbb == "1") {
                  $digit_jalur = "8";
                }
                $get_no_urut = $this->mymodel->withquery("select va_number, id_siswa_ft as id_siswa from siswa_ft where va_number like '".$prefix.$client_id.$kode_tahun.$digit_jalur."%' and va_number != '' order by id_siswa_ft DESC","row");
                if (empty($get_no_urut)) {
                  $no_urut = "001";
                }
                else{
                  $no_urut = (int)substr($get_no_urut->va_number, -3);
                  $no_urut = $no_urut+1;
                  $no_urut = sprintf("%03d", $no_urut);
                }
                //$va_number = $prefix.$client_id.date("y", strtotime('+1 years'))."09".$no_urut;
                $va_number = $prefix.$client_id.$kode_tahun.$digit_jalur.$no_urut;
                // $va_number='9881611399010025';
                $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $this->post('nama_lengkap'), "email" => $this->post('email'), "va_number" => $va_number ));
                // dd($payment_response);
                $increment = 2;
                /* var_dump($payment_response); */
                if(empty($payment_response['virtual_account'])){
                  for ($i=2; $i < 100; $i++) { 
                    $no_transaksi = (($ppsbb == "1") ? "LI-PPSBBFT-" : "LI-FT-").date("Ymd").$i."-".$id_siswa;
                    $data_transaksi['no_transaksi'] = $no_transaksi;
                    $no_urut = (int)substr($get_no_urut->va_number, -3);
                    $no_urut = $no_urut+$i;
                    $no_urut = sprintf("%03d", $no_urut);
                    //$va_number = $prefix.$client_id.date("y", strtotime('+1 years'))."09".$no_urut;
                    $va_number = $prefix.$client_id.$kode_tahun.$digit_jalur.$no_urut;
                    $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $this->post('nama_lengkap'), "email" => $this->post('email'), "va_number" => $va_number ));
                    if (!empty($payment_response['virtual_account'])) {
                      break;
                    }
                  }
                  $payment_response['generated_va'] = $va_number;
                  if (empty($payment_response['virtual_account'])) {
                    $this->db->trans_rollback();
                    $msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan VA' ,'data'=>$payment_response);
                    $status="200";
                    $this->response($msg,$status);
                    $this->mymodel->insertid("error_log_bni",array("status"=>$payment_response['status'],"message"=>$payment_response['message']." ".json_encode($payment_response),"va_number"=>$va_number));
                  }
                  // $msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan VA' ,'data'=>$payment_response);
                  // $status="200";
                  //$this->response($msg,$status);
                }

                $data_transaksi['va_number'] = $va_number;

                $id_transaksi = $this->mymodel->insertid("transaksi",$data_transaksi);
                if (empty($id_transaksi)) {
                  $this->db->trans_rollback();
                  $msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan Transaksi' ,'data'=>$payment_response);
                  $status="200";
                  $this->response($msg,$status);
                }
                $this->mymodel->update("siswa_ft", array("no_transaksi" => $no_transaksi, "va_number" => $va_number), "id_siswa_ft", $id_siswa);

                //commit jika valid
                if ($this->db->trans_status() === FALSE){
                  $err_log = $this->db->error();
                  $this->db->trans_rollback();
                  $msg = array('status' => 0, 'message'=>'Terjadi kesalahan saat menyimpan data pendaftaran', 'data'=>array(), 'data_raport' => array(), 'transaksi' => array(), 'err_log' => $err_log);
                  $status="200";
                  $this->response($msg,$status);
                  return;
                }
                else{
                  $this->db->trans_commit();
                }
                //$this->cetak_slip(array("id_siswa" => $id_siswa, "tipe_siswa" => "ft"));
                $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '".$id_transaksi."'","row");
                //kirim email slip pembayaran
                $data_email = array(
                  "email" => $this->post("email"),
                  "nama_lengkap" => $this->post("nama_lengkap"),
                  "tipe_pendaftaran" => $tipe_pendaftaran_label,
                  "jenjang" => "FT",
                  "transaksi" => $get_transaksi,
                  "nama_panitia" => "Panitia ".($ppsbb == "1" ? "PPSBB FT" : "PSB FT")." Labschool Cibubur ".date("Y", strtotime("+1 years"))."-".date("Y", strtotime("+2 years")),
                  "slip_pembayaran" => $no_transaksi.'-'.$this->post("nama_lengkap").'.pdf',
                );
                $this->send_email_file("",$data_email['email'],$data_email);
              }

              $msg = array('status' => 1, 'message'=>'Berhasil melakukan pendaftaran' ,'data'=>$data, 'data_raport' => $data_raport, 'transaksi' => $data_transaksi, 'prestasi' => count($prestasi_rows), 'kuesioner' => count($kuesioner_rows));
              $status="200";
            }
            else{
              $this->db->trans_rollback();
              $msg = array('status' => 0, 'message'=>'Form tidak diisi sesuai permintaan (Input siswa data gagal)' ,'data'=>array(), 'data_raport' => array(), 'transaksi' => array(), 'err_log' => $this->db->error(), 'last_query' => $this->db->last_query());
              $status="200";
            }
          }
          else{
            $this->db->trans_rollback();
            $msg = array('status' => 0, 'message'=>'Form tidak diisi sesuai permintaan' ,'data'=>array(), 'data_raport' => array(), 'transaksi' => array());
            $status="200";
          }

        $this->response($msg,$status);
    }

    function cetak_slip($get = '') {
      //$usecookie = __DIR__ . "/cookie.txt";
      $header[] = 'Content-Type: application/json';
      $header[] = "Accept-Encoding: gzip, deflate";
      $header[] = "Cache-Control: max-age=0";
      $header[] = "Connection: keep-alive";
      $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

      $ch = curl_init();
      //curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_VERBOSE, false);
      // curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_ENCODING, true);
      curl_setopt($ch, CURLOPT_AUTOREFERER, true);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

      if ($get)
      {
        $endpoint = site_url('/apiapp/siswa/export_pdf_slip_pembayaran');
        $params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
        $url = $endpoint . '?' . http_build_query($params);
        curl_setopt($ch, CURLOPT_URL, $url);
      }
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $rs = curl_exec($ch);

      if(empty($rs)){
        var_dump($rs, curl_error($ch));
        curl_close($ch);
        return false;
      }
      curl_close($ch);
      //return $rs;
    }

    function create_billing($production, $total, $no_transaksi, $data_user){
      $this->load->library('BniEnc');
      // FROM BNI
      $get_setting = $this->mymodel->getall("pengaturan_akun");
      foreach ($get_setting as $key => $value) {
        if ($value->name_setting == "bni_client_id_ft") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_ft") {
          $secret_key = $value->value;
        }
        if ($value->name_setting == "bni_prefix") {
          $prefix = $value->value;
        }
        if ($production == "production") {
          if ($value->name_setting == "bni_api_prod_url") {
            $url = $value->value;
          }
        }
        else if ($production == "development" || $production == "testing"){
          if ($value->name_setting == "bni_api_dev_url") {
            $url = $value->value;
          }
        }
      }

      $get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
      foreach ($get_pengaturan_masa_aktif as $key => $item) {
        if($item->label=='pendaftaran'){
          if($item->tipe_date=='day'){
            $date_va=($item->value*24) * 3600;
          }else{
            $date_va=($item->value) * 3600;
          }
        }
      }

      $data_asli = array(
        'type' => "createbilling",
        'client_id' => $client_id,
        'trx_id' => $no_transaksi,
        'trx_amount' => $total,
        'billing_type' => 'c',
        'datetime_expired' => date('c', time() + $date_va), // billing will be expired in 6 hours
        'virtual_account' => $data_user['va_number'],
        'customer_name' => $data_user['nama'],
        'customer_email' => $data_user['email'],
        //'customer_phone' => $data_user['notelp'],
      );

      $hashed_string = BniEnc::encrypt(
        $data_asli,
        $client_id,
        $secret_key
      );

      $data = array(
        'client_id' => $client_id,
        'data' => $hashed_string,
      );

      $response = $this->get_content($url, json_encode($data));
      $response_json = json_decode($response, true);

      if ($response_json['status'] !== '000') {
        // handling jika gagal
        return($response_json);
      }
      else {
        $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
        // $data_response will contains something like this: 
        // array(
        //  'virtual_account' => 'xxxxx',
        //  'trx_id' => 'xxx',
        // );
        //var_dump($data_response);
        return($data_response);
      }
    }

    function get_content($url, $post = '') {
      //$usecookie = __DIR__ . "/cookie.txt";
      $header[] = 'Content-Type: application/json';
      $header[] = "Accept-Encoding: gzip, deflate";
      $header[] = "Cache-Control: max-age=0";
      $header[] = "Connection: keep-alive";
      $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_VERBOSE, false);
      // curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_ENCODING, true);
      curl_setopt($ch, CURLOPT_AUTOREFERER, true);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

      if ($post)
      {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      }

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $rs = curl_exec($ch);

      if(empty($rs)){
        var_dump($rs, curl_error($ch));
        curl_close($ch);
        return false;
      }
      curl_close($ch);
      return $rs;
    }

    public function send_email_file($file="",$to='',$data)
    {
      $to = urldecode($to);
      $mail = new PHPMailer;
      // Konfigurasi SMTP
      $mail->isSMTP();
      $mail->SMTPDebug =0;
      // $mail->Host = 'mail.namagz.com';
      $mail->Host = 'smtp.office365.com';
      $mail->SMTPOptions = array(
         'ssl' => array(
           'verify_peer' => false,
           'verify_peer_name' => false,
           'allow_self_signed' => true
          )
      );
      $mail->SMTPAuth = true;
      $mail->Username = 'noreply@labschoolcibubur.sch.id';
      $mail->Password = ''; // [JACOS] TODO(manual): password SMTP Jacos — kredensial lama dihapus
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->addReplyTo('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
      $mail->setFrom('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');

      // Menambahkan penerima
      $mail->addAddress($to);

      // Menambahkan beberapa penerima


      // Subjek email
      $mail->Subject = '[No Reply] SLIP PEMBAYARAN PENDAFTARAN SISWA BARU';

      // Mengatur format email ke HTML
      $mail->isHTML(true);
      //$mail->AddEmbeddedImage('./assets/image/admin/bg_footer_mail_black.png', 'bg_footer_mail_black'); //ini yg dipakai utk
      //$mail->addStringAttachment(file_get_contents(base_url("assets/image/admin/")."bg_footer_mail"), "bg_footer_mail");
      if (!empty($data['slip_pembayaran'])) {
        $mail->AddAttachment('./uploads/slip_pembayaran/'.$data['slip_pembayaran']);
        //$mail->AddEmbeddedImage('./uploads/slip_pembayaran/'.$data->slip_pembayaran, 'slip_pembayaran');
      }
      // Konten/isi
       $data_['to'] = $to;
       $data_['nama_lengkap'] = $data['nama_lengkap'];
       $data_['jenjang'] = $data['jenjang'];
       $data_['nama_panitia'] = $data['nama_panitia'];
       $data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
       $data_['transaksi'] = $data['transaksi'];
       $mailContent = $this->load->view('template_email_pendaftaran',$data_,true);
       $mail->Body = $mailContent;
      // Menambahakn lampiran

      // Kirim email
      if(!$mail->send()){
          //echo 'Pesan tidak dapat dikirim.';
          //echo 'Mailer Error: ' . $mail->ErrorInfo;
      }else{
          //echo 'Pesan telah terkirim ';
      }
    }
}
