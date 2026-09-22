<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Web Link Daftar Lain Controller
*| --------------------------------------------------------------------------
*| Web Link Daftar Lain site
*|
*/
class Web_link_daftar_lain extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_web_link_daftar_lain');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Web Link Daftar Lains
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('web_link_daftar_lain_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['web_link_daftar_lains'] = $this->model_web_link_daftar_lain->get($filter, $field, $this->limit_page, $offset);
		$this->data['web_link_daftar_lain_counts'] = $this->model_web_link_daftar_lain->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/web_link_daftar_lain/index/',
			'total_rows'   => $this->model_web_link_daftar_lain->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Link Daftar Lain List');
		$this->render('backend/standart/administrator/web_link_daftar_lain/web_link_daftar_lain_list', $this->data);
	}
	
	
		/**
	* Update view Web Link Daftar Lains
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('web_link_daftar_lain_update');

		$this->data['web_link_daftar_lain'] = $this->model_web_link_daftar_lain->find($id);

		$this->template->title('Link Daftar Lain Update');
		$this->render('backend/standart/administrator/web_link_daftar_lain/web_link_daftar_lain_update', $this->data);
	}

	/**
	* Update Web Link Daftar Lains
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('web_link_daftar_lain_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('link_daftar_lain', 'Link Lain Pendaftaran', 'trim|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'link_daftar_lain' => $this->input->post('link_daftar_lain'),
			];

			
			$save_web_link_daftar_lain = $this->model_web_link_daftar_lain->change($id, $save_data);

			if ($save_web_link_daftar_lain) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/web_link_daftar_lain', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_link_daftar_lain');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/web_link_daftar_lain');
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
	* delete Web Link Daftar Lains
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('web_link_daftar_lain_delete');

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
            set_message(cclang('has_been_deleted', 'web_link_daftar_lain'), 'success');
        } else {
            set_message(cclang('error_delete', 'web_link_daftar_lain'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Web Link Daftar Lains
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('web_link_daftar_lain_view');

		$this->data['web_link_daftar_lain'] = $this->model_web_link_daftar_lain->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Link Daftar Lain Detail');
		$this->render('backend/standart/administrator/web_link_daftar_lain/web_link_daftar_lain_view', $this->data);
	}
	
	/**
	* delete Web Link Daftar Lains
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$web_link_daftar_lain = $this->model_web_link_daftar_lain->find($id);

		
		
		return $this->model_web_link_daftar_lain->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('web_link_daftar_lain_export');

		$this->model_web_link_daftar_lain->export('web_link_daftar_lain', 'web_link_daftar_lain');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('web_link_daftar_lain_export');

		$this->model_web_link_daftar_lain->pdf('web_link_daftar_lain', 'web_link_daftar_lain');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('web_link_daftar_lain_export');

		$table = $title = 'web_link_daftar_lain';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_web_link_daftar_lain->find($id);
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


/* End of file web_link_daftar_lain.php */
/* Location: ./application/controllers/administrator/Web Link Daftar Lain.php */