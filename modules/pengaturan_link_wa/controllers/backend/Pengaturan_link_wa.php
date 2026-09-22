<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Link Wa Controller
*| --------------------------------------------------------------------------
*| Pengaturan Link Wa site
*|
*/
class Pengaturan_link_wa extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_link_wa');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Link Was
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_link_wa_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_link_was'] = $this->model_pengaturan_link_wa->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_link_wa_counts'] = $this->model_pengaturan_link_wa->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_link_wa/index/',
			'total_rows'   => $this->model_pengaturan_link_wa->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Link Wa List');
		$this->render('backend/standart/administrator/pengaturan_link_wa/pengaturan_link_wa_list', $this->data);
	}
	
	
		/**
	* Update view Pengaturan Link Was
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_link_wa_update');

		$this->data['pengaturan_link_wa'] = $this->model_pengaturan_link_wa->find($id);

		$this->template->title('Pengaturan Link Wa Update');
		$this->render('backend/standart/administrator/pengaturan_link_wa/pengaturan_link_wa_update', $this->data);
	}

	/**
	* Update Pengaturan Link Was
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_link_wa_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('link', 'Link', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'link' => $this->input->post('link'),
			];

			
			$save_pengaturan_link_wa = $this->model_pengaturan_link_wa->change($id, $save_data);

			if ($save_pengaturan_link_wa) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_link_wa', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_link_wa');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_link_wa');
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
	* delete Pengaturan Link Was
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_link_wa_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_link_wa'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_link_wa'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Link Was
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_link_wa_view');

		$this->data['pengaturan_link_wa'] = $this->model_pengaturan_link_wa->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Link Wa Detail');
		$this->render('backend/standart/administrator/pengaturan_link_wa/pengaturan_link_wa_view', $this->data);
	}
	
	/**
	* delete Pengaturan Link Was
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_link_wa = $this->model_pengaturan_link_wa->find($id);

		
		
		return $this->model_pengaturan_link_wa->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_link_wa_export');

		$this->model_pengaturan_link_wa->export('pengaturan_link_wa', 'pengaturan_link_wa');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_link_wa_export');

		$this->model_pengaturan_link_wa->pdf('pengaturan_link_wa', 'pengaturan_link_wa');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_link_wa_export');

		$table = $title = 'pengaturan_link_wa';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_link_wa->find($id);
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


/* End of file pengaturan_link_wa.php */
/* Location: ./application/controllers/administrator/Pengaturan Link Wa.php */