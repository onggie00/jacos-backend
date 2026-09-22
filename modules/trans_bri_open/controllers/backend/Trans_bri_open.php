<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Trans Bri Controller
*| --------------------------------------------------------------------------
*| Trans Bri site
*|
*/
class Trans_bri_open extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_trans_bri_open');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Trans Bris
	*
	* @var $offset String
	*/
	public function index($date = null)
	{
		$this->is_allowed('trans_bri_open_list');
		$this->load->library('BriApi');
		//get config bri key
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $item) {
			if(ENVIRONMENT == "development" || ENVIRONMENT == "testing"){
				if($item->name_setting=='bri_dev_url'){
					$url = $item->value;
				}
				if ($item->name_setting == "bri_no_briva_dev") {
					$brivaNo = $item->value;
				}
			}else if(ENVIRONMENT == 'production'){
				if($item->name_setting=='bri_prod_url'){
					$url = $item->value;
				}
				if ($item->name_setting == "bri_no_briva_prod") {
					$brivaNo = $item->value;
				}
			}
			if($item->name_setting=='bri_client_id'){
				$clientID = $item->value;
			}
			if($item->name_setting=='bri_client_secret'){
				$clientSecret = $item->value;
			}
			if($item->name_setting=='bri_institution_code'){
				$institutionCode = $item->value;
			}
		}

		$endpoint     = $url."oauth/client_credential/accesstoken?grant_type=client_credentials";
		if($this->input->get('end_date')){
			$start_date=$this->input->get('start_date');
			$end_date=$this->input->get('end_date');
			
		}else{
			$start_date=date('Y').date('m').date('d');
			$end_date=date('Y').date('m').date('d');
		}

		$date=$this->echoDate(strtotime($start_date),strtotime($end_date));

		$res=[];
		foreach($date as $key => $d){
			$formated=str_replace('-','',$d);
			$datas=array(
				'brivaNo'=>$brivaNo,
				'startDate'=>$formated,
				'endDate'=>$formated
			);
	
			$arr=BriApi::getReportDate($clientID,$clientSecret,$endpoint,$institutionCode,$datas,$url);
			if($arr['responseCode']=='00'){
				foreach($arr['data'] as $item){
					$res[]=$item;
				}
			}
			
		}

		

		$this->data['trans_bris'] = $res;
		$this->data['trans_bri_open_counts'] = count($res);

		$config = [
			'base_url'     => 'administrator/trans_bri_open/index/',
			'total_rows'   => count($res),
			'per_page'     => 200,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);
		$this->template->title('Trans Bri List');
		$this->render('backend/standart/administrator/trans_bri_open/trans_bri_open_list', $this->data);
	}
	
	function echoDate( $start, $end ){

		$current = $start;
  
		$ret = array();
  
		while( $current<=$end ){
			
			
			$format=date('Y-m-d',$current);
			$ret[] = $format;
			$current = @date('Y-m-d', $current) . "+1 days";
			$current = @strtotime($current);
		}
  
		return $ret;
	}
	
	/**
	* delete Trans Bris
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('trans_bri_open_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'trans_bri_open'), 'success');
        } else {
            set_message(cclang('error_delete', 'trans_bri_open'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Trans Bris
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('trans_bri_open_view');

		$this->data['trans_bri_open'] = $this->model_trans_bri_open->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Trans Bri Detail');
		$this->render('backend/standart/administrator/trans_bri_open/trans_bri_open_view', $this->data);
	}
	
	/**
	* delete Trans Bris
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$trans_bri_open = $this->model_trans_bri_open->find($id);

		
		
		return $this->model_trans_bri_open->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->load->library('BriApi');
		//get config bri key
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $item) {
			if(ENVIRONMENT == "development" || ENVIRONMENT == "testing"){
				if($item->name_setting=='bri_dev_url'){
					$url = $item->value;
				}
				if ($item->name_setting == "bri_no_briva_dev") {
					$brivaNo = $item->value;
				}
			}else if(ENVIRONMENT == 'production'){
				if($item->name_setting=='bri_prod_url'){
					$url = $item->value;
				}
				if ($item->name_setting == "bri_no_briva_prod") {
					$brivaNo = $item->value;
				}
			}
			if($item->name_setting=='bri_client_id'){
				$clientID = $item->value;
			}
			if($item->name_setting=='bri_client_secret'){
				$clientSecret = $item->value;
			}
			if($item->name_setting=='bri_institution_code'){
				$institutionCode = $item->value;

			}
		}

		$endpoint     = $url."oauth/client_credential/accesstoken?grant_type=client_credentials";
		if($this->input->get('end_date')){
			$start_date=$this->input->get('start_date');
			$end_date=$this->input->get('end_date');
			
		}else{
			$start_date=date('Y').date('m').date('d');
			$end_date=date('Y').date('m').date('d');
		}

		$date=$this->echoDate(strtotime($start_date),strtotime($end_date));

		$res=[];
		foreach($date as $key => $d){
			$formated=str_replace('-','',$d);
			$datas=array(
				'brivaNo'=>$brivaNo,
				'startDate'=>$formated,
				'endDate'=>$formated
			);
	
			$arr=BriApi::getReportDate($clientID,$clientSecret,$endpoint,$institutionCode,$datas,$url);
			if($arr['responseCode']=='00'){
				foreach($arr['data'] as $item){
					$res[]=$item;
				}
			}
			
		}
		// dd($res);
		$this->model_trans_bri_open->export_bri($res, 'trans_bri_open');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('trans_bri_open_export');

		$this->model_trans_bri_open->pdf('trans_bri_open', 'trans_bri_open');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('trans_bri_open_export');

		$table = $title = 'trans_bri_open';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_trans_bri_open->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	
}


/* End of file trans_bri.php */
/* Location: ./application/controllers/administrator/Trans Bri.php */