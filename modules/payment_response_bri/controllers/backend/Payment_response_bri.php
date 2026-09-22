<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Payment Response Bri Controller
*| --------------------------------------------------------------------------
*| Payment Response Bri site
*|
*/
class Payment_response_bri extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_payment_response_bri');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Payment Response Bris
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('payment_response_bri_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['payment_response_bris'] = $this->model_payment_response_bri->get($filter, $field, $this->limit_page, $offset);
		$this->data['payment_response_bri_counts'] = $this->model_payment_response_bri->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/payment_response_bri/index/',
			'total_rows'   => $this->model_payment_response_bri->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Payment Response Bri List');
		$this->render('backend/standart/administrator/payment_response_bri/payment_response_bri_list', $this->data);
	}
	
	
	
	/**
	* delete Payment Response Bris
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('payment_response_bri_delete');

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
            set_message(cclang('has_been_deleted', 'payment_response_bri'), 'success');
        } else {
            set_message(cclang('error_delete', 'payment_response_bri'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Payment Response Bris
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('payment_response_bri_view');

		$this->data['payment_response_bri'] = $this->model_payment_response_bri->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Payment Response Bri Detail');
		$this->render('backend/standart/administrator/payment_response_bri/payment_response_bri_view', $this->data);
	}
	
	/**
	* delete Payment Response Bris
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$payment_response_bri = $this->model_payment_response_bri->find($id);

		
		
		return $this->model_payment_response_bri->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('payment_response_bri_export');

		$this->model_payment_response_bri->export('payment_response_bri', 'payment_response_bri');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('payment_response_bri_export');

		$this->model_payment_response_bri->pdf('payment_response_bri', 'payment_response_bri');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('payment_response_bri_export');

		$table = $title = 'payment_response_bri';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_payment_response_bri->find($id);
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


/* End of file payment_response_bri.php */
/* Location: ./application/controllers/administrator/Payment Response Bri.php */