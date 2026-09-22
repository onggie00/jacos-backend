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
        $jenjang = $this->post("jenjang");
        

        $resGrade = $this->moodle_rest($func,$params,$jenjang);

        $grades=$resGrade['grades'];

        $func='core_course_get_courses';
        foreach($grades as $key => $item){
          if($key==0){
            $params=array('options[ids]['.$key.']' => $item['courseid']);
          }else{
            $p=$params['options[ids]['.$key.']']=$item['courseid'];
          }

        }
        
        $courses = $this->moodle_rest($func,$params,$jenjang);
        // dd($courses);
        foreach ($grades as $key => $g){
          foreach ($courses as $c){
            if($g['courseid'] == $c['id']){
              $grades[$key]['detail_course']=$c;
            }
          }
        }

        $res=[
          "grade"=>$grades,
          // "moodle_data" => $resGrade,
          // "courses" => $courses
        ];

        $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$res);
          $status="200";

        $this->response($msg,$status);
    }

    function moodle_rest($func,$params,$jenjang){

      $this->load->library('MoodleRest');

      if (strtoupper($jenjang) == "SD") {
        $url = "https://cbtsd.labschoolcibubur.sch.id/webservice/rest/server.php";
        $token = ""; // [JACOS] TODO(manual): token webservice Moodle Jacos
      }
      else if (strtoupper($jenjang) == "SMP") {
        $url="https://cbtsmp.labschoolcibubur.sch.id/webservice/rest/server.php";
        $token="acd372a5007b3b3a7467b9a62ebe11ba";
      }
      else if(strtoupper($jenjang) == "SMA"){
        $url="https://cbtsma.labschoolcibubur.sch.id/webservice/rest/server.php";
        $token="a4930db5a31b430bc5fe6475b5aac46c";
      }
      else{
        //arahkan ke smp
        $url="https://cbtsmp.labschoolcibubur.sch.id/webservice/rest/server.php";
        $token="acd372a5007b3b3a7467b9a62ebe11ba";
      }


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
