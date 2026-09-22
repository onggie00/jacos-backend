<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

/**
 * Daftar pertanyaan + opsi per step (PSB web).
 * GET apiweb/Get_content_per_step?jenjang=ft&is_ppsbb=1            -> seluruh step, urut step & no_urut ASC
 * GET apiweb/Get_content_per_step?step=5&jenjang=ft&is_ppsbb=1     -> hanya step 5
 * Param wajib: jenjang (sd/smp/sma/ft), is_ppsbb (0/1). Invalid -> status 0.
 * Response: data[ {...pertanyaan, step, item_details:[{...item}]} ]
 */
class Get_content_per_step extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_get()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

      $step = intval($this->get('step'));

      $jenjang = trim($this->get('jenjang'));
      $is_ppsbb = trim($this->get('is_ppsbb'));
      if(empty($jenjang)) {
        $jenjang = "ft";
      }
      if(empty($is_ppsbb)) {
        $is_ppsbb = "1";
      }

      $allowed_jenjang = array('sd','smp','sma','ft');
      if ($jenjang === '' || !in_array($jenjang, $allowed_jenjang)) {
        $msg = array('status' => 0, 'message'=>'Parameter jenjang wajib diisi (sd/smp/sma/ft)' ,'data'=>array());
        $this->response($msg,200);
      }
      if ($is_ppsbb !== '0' && $is_ppsbb !== '1') {
        $msg = array('status' => 0, 'message'=>'Parameter is_ppsbb wajib diisi (0/1)' ,'data'=>array());
        $this->response($msg,200);
      }

      if (!empty($step)) {
        $pertanyaan = $this->mymodel->withquery("select * from web_psb_step_pertanyaan where step = '".$step."' and jenjang = '".$jenjang."' and is_ppsbb = ".intval($is_ppsbb)." order by no_urut asc","result");
      }
      else{
        $pertanyaan = $this->mymodel->withquery("select * from web_psb_step_pertanyaan where jenjang = '".$jenjang."' and is_ppsbb = ".intval($is_ppsbb)." order by CAST(step AS UNSIGNED) asc, no_urut asc","result");
      }

      $data = array();
      foreach ($pertanyaan as $p) {
        $items = $this->mymodel->withquery("select * from web_psb_step_item where id_web_psb_pertanyaan = '".intval($p->id_step_pertanyaan)."' order by id_web_psb_item asc","result");
        $data[] = array(
          'id_step_pertanyaan' => intval($p->id_step_pertanyaan),
          'step'               => $p->step,
          'pertanyaan'         => $p->pertanyaan,
          'no_urut'            => intval($p->no_urut),
          'tipe_input'         => $p->tipe_input,
          'min_input'          => $p->min_input,
          'max_input'          => $p->max_input,
          'item_details'       => $items
        );
      }

      if (!empty($data)) {
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
