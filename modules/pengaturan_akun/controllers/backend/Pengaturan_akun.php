<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Akun Controller
*| --------------------------------------------------------------------------
*| Pengaturan Akun site
*|
*/
class Pengaturan_akun extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_akun');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Akuns
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_akun_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_akuns'] = $this->model_pengaturan_akun->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_akun_counts'] = $this->model_pengaturan_akun->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_akun/index/',
			'total_rows'   => $this->model_pengaturan_akun->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Akun List');
		$this->render('backend/standart/administrator/pengaturan_akun/pengaturan_akun_list', $this->data);
	}
	
	
		/**
	* Update view Pengaturan Akuns
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_akun_update');

		$this->data['pengaturan_akun'] = $this->model_pengaturan_akun->find($id);

		$this->template->title('Pengaturan Akun Update');
		$this->render('backend/standart/administrator/pengaturan_akun/pengaturan_akun_update', $this->data);
	}

	/**
	* Update Pengaturan Akuns
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_akun_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('value', 'Value', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'value' => $this->input->post('value'),
			];

			
			$save_pengaturan_akun = $this->model_pengaturan_akun->change($id, $save_data);

			if ($save_pengaturan_akun) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_akun', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_akun');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_akun');
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
	* delete Pengaturan Akuns
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_akun_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_akun'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_akun'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Akuns
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_akun_view');

		$this->data['pengaturan_akun'] = $this->model_pengaturan_akun->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Akun Detail');
		$this->render('backend/standart/administrator/pengaturan_akun/pengaturan_akun_view', $this->data);
	}
	
	/**
	* delete Pengaturan Akuns
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_akun = $this->model_pengaturan_akun->find($id);

		
		
		return $this->model_pengaturan_akun->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_akun_export');

		$this->model_pengaturan_akun->export('pengaturan_akun', 'pengaturan_akun');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_akun_export');

		$this->model_pengaturan_akun->pdf('pengaturan_akun', 'pengaturan_akun');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_akun_export');

		$table = $title = 'pengaturan_akun';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_akun->find($id);
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


/* End of file pengaturan_akun.php */
/* Location: ./application/controllers/administrator/Pengaturan Akun.php */