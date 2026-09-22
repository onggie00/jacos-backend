<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Tanggal Lahir Controller
*| --------------------------------------------------------------------------
*| Pengaturan Tanggal Lahir site
*|
*/
class Pengaturan_tanggal_lahir extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_tanggal_lahir');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Tanggal Lahirs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_tanggal_lahir_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_tanggal_lahirs'] = $this->model_pengaturan_tanggal_lahir->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_tanggal_lahir_counts'] = $this->model_pengaturan_tanggal_lahir->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_tanggal_lahir/index/',
			'total_rows'   => $this->model_pengaturan_tanggal_lahir->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Tanggal Lahir List');
		$this->render('backend/standart/administrator/pengaturan_tanggal_lahir/pengaturan_tanggal_lahir_list', $this->data);
	}
	
	
		/**
	* Update view Pengaturan Tanggal Lahirs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_tanggal_lahir_update');

		$this->data['pengaturan_tanggal_lahir'] = $this->model_pengaturan_tanggal_lahir->find($id);

		$this->template->title('Pengaturan Tanggal Lahir Update');
		$this->render('backend/standart/administrator/pengaturan_tanggal_lahir/pengaturan_tanggal_lahir_update', $this->data);
	}

	/**
	* Update Pengaturan Tanggal Lahirs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_tanggal_lahir_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'date' => $this->input->post('date'),
			];

			
			$save_pengaturan_tanggal_lahir = $this->model_pengaturan_tanggal_lahir->change($id, $save_data);

			if ($save_pengaturan_tanggal_lahir) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_tanggal_lahir', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_tanggal_lahir');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_tanggal_lahir');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Pengaturan Tanggal Lahirs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_tanggal_lahir_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_tanggal_lahir'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_tanggal_lahir'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Tanggal Lahirs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_tanggal_lahir_view');

		$this->data['pengaturan_tanggal_lahir'] = $this->model_pengaturan_tanggal_lahir->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Tanggal Lahir Detail');
		$this->render('backend/standart/administrator/pengaturan_tanggal_lahir/pengaturan_tanggal_lahir_view', $this->data);
	}
	
	/**
	* delete Pengaturan Tanggal Lahirs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_tanggal_lahir = $this->model_pengaturan_tanggal_lahir->find($id);

		
		
		return $this->model_pengaturan_tanggal_lahir->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_tanggal_lahir_export');

		$this->model_pengaturan_tanggal_lahir->export('pengaturan_tanggal_lahir', 'pengaturan_tanggal_lahir');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_tanggal_lahir_export');

		$this->model_pengaturan_tanggal_lahir->pdf('pengaturan_tanggal_lahir', 'pengaturan_tanggal_lahir');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_tanggal_lahir_export');

		$table = $title = 'pengaturan_tanggal_lahir';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_tanggal_lahir->find($id);
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


/* End of file pengaturan_tanggal_lahir.php */
/* Location: ./application/controllers/administrator/Pengaturan Tanggal Lahir.php */