<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Agenda extends REST_Controller {
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
        $jenjang=explode(',',$this->post('jenjang'));
        $id_agenda=$this->post('id_agenda');
        $where="";
        $date=date('Y-m-d H:i:s');
        if (empty($id_agenda)) {
          foreach($jenjang as $key => $item){
            if($key==0){
              $where.="'$item'";
            }else{
              $where.=",'$item'";
            }
          }
          $data = $this->mymodel->withquery("select a.*,k.* from agenda a left join kategori_agenda k on k.id=a.id_kategori where jenjang in ($where) and created_at <= '$date' order by id_agenda desc","result");
        }
        else{
          $data = $this->mymodel->withquery("select a.*,k.* from agenda a left join kategori_agenda k on k.id=a.id_kategori where id_agenda = '".$id_agenda."' order by id_agenda desc","result");
        }
        // dd($where);

        if (!empty($data)) {
          foreach ($data as $key => $value) {
            if (!empty($value->img_thumbnail)) {
              $value->img_thumbnail = base_url("uploads/agenda/").$value->img_thumbnail;
            }
            if (!empty($value->img_agenda)) {
              $value->img_agenda = base_url("uploads/agenda/").$value->img_agenda;
            }
          }
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
