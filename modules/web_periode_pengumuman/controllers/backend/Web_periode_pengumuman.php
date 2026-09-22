<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Web Periode Pengumuman Controller
*| --------------------------------------------------------------------------
*| Web Periode Pengumuman site
*|
*/
class Web_periode_pengumuman extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_web_periode_pengumuman');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Web Periode Pengumumans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('web_periode_pengumuman_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['web_periode_pengumumans'] = $this->model_web_periode_pengumuman->get($filter, $field, $this->limit_page, $offset);
		$this->data['web_periode_pengumuman_counts'] = $this->model_web_periode_pengumuman->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/web_periode_pengumuman/index/',
			'total_rows'   => $this->model_web_periode_pengumuman->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Web Periode Pengumuman List');
		$this->render('backend/standart/administrator/web_periode_pengumuman/web_periode_pengumuman_list', $this->data);
	}
	
	
		/**
	* Update view Web Periode Pengumumans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('web_periode_pengumuman_update');

		$this->data['web_periode_pengumuman'] = $this->model_web_periode_pengumuman->find($id);

		$this->template->title('Web Periode Pengumuman Update');
		$this->render('backend/standart/administrator/web_periode_pengumuman/web_periode_pengumuman_update', $this->data);
	}

	/**
	* Update Web Periode Pengumumans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('web_periode_pengumuman_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('periode_pengumuman_mulai', 'Periode Pengumuman Mulai', 'trim|required');
		$this->form_validation->set_rules('periode_pengumuman_selesai', 'Periode Pengumuman Selesai', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'periode_pengumuman_mulai' => $this->input->post('periode_pengumuman_mulai'),
				'periode_pengumuman_selesai' => $this->input->post('periode_pengumuman_selesai'),
			];

			
			$save_web_periode_pengumuman = $this->model_web_periode_pengumuman->change($id, $save_data);

			if ($save_web_periode_pengumuman) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/web_periode_pengumuman', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_periode_pengumuman');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/web_periode_pengumuman');
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
	* delete Web Periode Pengumumans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('web_periode_pengumuman_delete');

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
            set_message(cclang('has_been_deleted', 'web_periode_pengumuman'), 'success');
        } else {
            set_message(cclang('error_delete', 'web_periode_pengumuman'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Web Periode Pengumumans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('web_periode_pengumuman_view');

		$this->data['web_periode_pengumuman'] = $this->model_web_periode_pengumuman->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Web Periode Pengumuman Detail');
		$this->render('backend/standart/administrator/web_periode_pengumuman/web_periode_pengumuman_view', $this->data);
	}
	
	/**
	* delete Web Periode Pengumumans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$web_periode_pengumuman = $this->model_web_periode_pengumuman->find($id);

		
		
		return $this->model_web_periode_pengumuman->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('web_periode_pengumuman_export');

		$this->model_web_periode_pengumuman->export('web_periode_pengumuman', 'web_periode_pengumuman');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('web_periode_pengumuman_export');

		$this->model_web_periode_pengumuman->pdf('web_periode_pengumuman', 'web_periode_pengumuman');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('web_periode_pengumuman_export');

		$table = $title = 'web_periode_pengumuman';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_web_periode_pengumuman->find($id);
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


/* End of file web_periode_pengumuman.php */
/* Location: ./application/controllers/administrator/Web Periode Pengumuman.php */