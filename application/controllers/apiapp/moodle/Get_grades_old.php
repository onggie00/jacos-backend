<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_grades extends REST_Controller {
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

        $func='gradereport_overview_get_course_grades';
        $params=array('userid' => $this->post('userid'));
        

        $resGrade = $this->moodle_rest($func,$params);

        $grades=$resGrade['grades'];

        $func='core_course_get_courses';
        foreach($grades as $key => $item){
          if($key==0){
            $params=array('options[ids]['.$key.']' => $item['courseid']);
          }else{
            $p=$params['options[ids]['.$key.']']=$item['courseid'];
          }

        }
        
        $courses = $this->moodle_rest($func,$params);
        // dd($courses);
        foreach ($grades as $key => $g){
          foreach ($courses as $c){
            if($g['courseid'] == $c['id']){
              $grades[$key]['detail_course']=$c;
            }
          }
        }

        $res=[
          "grade"=>$grades
        ];

        $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$res);
          $status="200";

        $this->response($msg,$status);
    }

    function moodle_rest($func,$params){

      $this->load->library('MoodleRest');

      $url='https://lms.labschoolcibubur.sch.id/webservice/rest/server.php';
      $token='24eeaadd9663e4bf17e5cf0d7196986e';

      $MoodleRest = new MoodleRest($url , $token);
  
      $res = $MoodleRest->request($func,$params);
  
      return $res;
    }

    function like($str, $searchTerm) {
      $searchTerm = strtolower($searchTerm);
      $str = strtolower($str);
      $pos = strpos($str, $searchTerm);
      if ($pos === false)
          return false;
      else
          return true;
  }
}
