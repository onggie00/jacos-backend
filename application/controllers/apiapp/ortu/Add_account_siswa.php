<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Add_account_siswa extends REST_Controller {
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

        $jenjang=$this->post('jenjang');
        $email_ms_office_ortu = $this->post('email_ms_office_ortu');
        $nisn = $this->post('nisn');

        if (!empty($jenjang)) {
            if($jenjang=='sd' || $jenjang=='SD'){
                $check_if_email_exist = $this->mymodel->withquery("select * from siswa_sd where email_ms_office_ortu like '%" . $email_ms_office_ortu . "%' and nisn = '$nisn'","row"); //before: result, then changed to row
                $get_id_siswa_sd_aktif = $this->mymodel->withquery("select * from siswa_sd_aktif "."where nama_lengkap like '%" . $check_if_email_exist->nama_lengkap . "%' ","row");
                if (!$check_if_email_exist){
                    $msg = array('status' => 401, 'message'=>'Data tidak Valid!');
                }else{
                    $get_siswa=$this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_sd_aktif sa join siswa_sd s on s.id_siswa_sd = sa.id_siswa_sd join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_sd k on k.id_kelas_sd = sa.id_kelas where id_siswa_sd_aktif = $get_id_siswa_sd_aktif->id_siswa_sd_aktif","row");
                    $data = array(
                        "email_ms_office_ortu" => $check_if_email_exist->email_ms_office_ortu,
                        "id_siswa_aktif" => $get_id_siswa_sd_aktif->id_siswa_sd_aktif,
                        "nama_lengkap" => $check_if_email_exist->nama_lengkap,
                        "jenjang" => $jenjang,
                        "nisn" => $check_if_email_exist->nisn,
                        "email_ms_office" => $check_if_email_exist->email_ms_office,
                        "notelp_ayah" => $check_if_email_exist->notelp_ayah,
                        "notelp_ibu" => $check_if_email_exist->notelp_ibu,
                        "kelas_siswa" => $get_siswa->id_tingkatan.' - '.$get_siswa->nama_kelas
                    );
                }
            }elseif($jenjang=='smp' || $jenjang=='SMP'){
                $check_if_email_exist = $this->mymodel->withquery("select * from siswa_smp where email_ms_office_ortu like '%" . $email_ms_office_ortu . "%' and nisn = '$nisn'","row");
                $get_id_siswa_smp_aktif = $this->mymodel->withquery("select * from siswa_smp_aktif "."where nama_lengkap like '%" . $check_if_email_exist->nama_lengkap . "%' ","row");
                if (!$check_if_email_exist){
                    $msg = array('status' => 401, 'message'=>'Data tidak Valid!');
                }else{
                    $get_siswa=$this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_smp_aktif sa join siswa_smp s on s.id_siswa_smp = sa.id_siswa_smp join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_smp k on k.id_kelas_smp = sa.id_kelas where id_siswa_smp_aktif = $get_id_siswa_smp_aktif->id_siswa_smp_aktif","row");
                    $data = array(
                        "email_ms_office_ortu" => $check_if_email_exist->email_ms_office_ortu,
                        "id_siswa_aktif" => $get_id_siswa_smp_aktif->id_siswa_smp_aktif,
                        "nama_lengkap" => $check_if_email_exist->nama_lengkap,
                        "jenjang" => $jenjang,
                        "nisn" => $check_if_email_exist->nisn,
                        "email_ms_office" => $check_if_email_exist->email_ms_office,
                        "notelp_ayah" => $check_if_email_exist->notelp_ayah,
                        "notelp_ibu" => $check_if_email_exist->notelp_ibu,
                        "kelas_siswa" => $get_siswa->id_tingkatan.' - '.$get_siswa->nama_kelas
                    );
                }
            }elseif($jenjang=='sma' || $jenjang=='SMA'){
                $check_if_email_exist = $this->mymodel->withquery("select * from siswa_sma where email_ms_office_ortu like '%" . $email_ms_office_ortu . "%' and nisn = '$nisn'","row");
                $get_id_siswa_sma_aktif = $this->mymodel->withquery("select * from siswa_sma_aktif "."where nama_lengkap like '%" . $check_if_email_exist->nama_lengkap . "%' ","row");
                if (!$check_if_email_exist){
                    $msg = array('status' => 401, 'message'=>'Data tidak Valid!');
                }else{
                    $get_siswa=$this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_sma_aktif sa join siswa_sma s on s.id_siswa_sma = sa.id_siswa_sma join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_sma k on k.id_kelas_sma = sa.id_kelas where id_siswa_sma_aktif = $get_id_siswa_sma_aktif->id_siswa_sma_aktif","row");
                    $data = array(
                        "email_ms_office_ortu" => $check_if_email_exist->email_ms_office_ortu,
                        "id_siswa_aktif" => $get_id_siswa_sma_aktif->id_siswa_sma_aktif,
                        "nama_lengkap" => $check_if_email_exist->nama_lengkap,
                        "jenjang" => $jenjang,
                        "nisn" => $check_if_email_exist->nisn,
                        "email_ms_office" => $check_if_email_exist->email_ms_office,
                        "notelp_ayah" => $check_if_email_exist->notelp_ayah,
                        "notelp_ibu" => $check_if_email_exist->notelp_ibu,
                        "kelas_siswa" => $get_siswa->id_tingkatan.' - '.$get_siswa->nama_kelas
                    );
                }
            }else{ //FT
                $check_if_email_exist = $this->mymodel->withquery("select * from siswa_ft where email_ms_office_ortu like '%" . $email_ms_office_ortu . "%' and nisn = '$nisn'","row");
                $get_id_siswa_ft_aktif = $this->mymodel->withquery("select * from siswa_ft_aktif "."where nama_lengkap like '%" . $check_if_email_exist->nama_lengkap . "%' ","row");
                if (!$check_if_email_exist){
                    $msg = array('status' => 401, 'message'=>'Data tidak Valid!');
                }else{
                    $get_siswa=$this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_ft_aktif sa join siswa_ft s on s.id_siswa_ft = sa.id_siswa_ft join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_ft k on k.id_kelas_ft = sa.id_kelas where id_siswa_ft_aktif = $get_id_siswa_ft_aktif->id_siswa_ft_aktif","row");
                    $data = array(
                        "email_ms_office_ortu" => $check_if_email_exist->email_ms_office_ortu,
                        "id_siswa_aktif" => $get_id_siswa_ft_aktif->id_siswa_ft_aktif,
                        "nama_lengkap" => $check_if_email_exist->nama_lengkap,
                        "jenjang" => $jenjang,
                        "nisn" => $check_if_email_exist->nisn,
                        "email_ms_office" => $check_if_email_exist->email_ms_office,
                        "notelp_ayah" => $check_if_email_exist->notelp_ayah,
                        "notelp_ibu" => $check_if_email_exist->notelp_ibu,
                        "kelas_siswa" => $get_siswa->id_tingkatan.' - '.$get_siswa->nama_kelas
                    );
                }
            }
        }

        if (!empty($data)) {
            $where_email="where email_ms_office_ortu like '%" . $email_ms_office_ortu . "%'";
            $where="and nisn = $nisn and jenjang = '$jenjang'";
            // $where_email_utama="and email_ortu_utama like '%" . $email_ms_office_ortu . "%'";

            $check_account_if_exist = $this->mymodel->withquery("select * from switch_account $where_email order by id desc","row");

            // $check_account_if_exist = $this->mymodel->withquery("select * from switch_account where email_ms_office_ortu like '%" . $email_ms_office_ortu . "%' and jenjang = '$jenjang' and nisn = '$nisn'","row");
            if($check_account_if_exist){
                $msg = array('status' => 401, 'message'=>'Data account siswa sudah ditambahkan!');
            }else{
                $insert=$this->mymodel->insertid("switch_account",$data);
                if($insert){
                    $msg = array('status' => 200, 'message'=>'Berhasil menambahkan akun siswa!', 'data' => $data);
                }else{
                    $msg = array('status' => 401, 'message'=>'Gagal menambahkan akun siswa!', 'data' => $data);
                }
            }
        }
        $this->response($msg);
    }
}
