<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| List Notifikasi Controller
*| --------------------------------------------------------------------------
*| List Notifikasi site
*|
*/
class List_notifikasi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_list_notifikasi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all List Notifikasis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('list_notifikasi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['list_notifikasis'] = $this->model_list_notifikasi->get($filter, $field, $this->limit_page, $offset);
		$this->data['list_notifikasi_counts'] = $this->model_list_notifikasi->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/list_notifikasi/index/',
			'total_rows'   => $this->model_list_notifikasi->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('List Notifikasi List');
		$this->render('backend/standart/administrator/list_notifikasi/list_notifikasi_list', $this->data);
	}
	
	
		/**
	* Update view List Notifikasis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('list_notifikasi_update');

		$this->data['list_notifikasi'] = $this->model_list_notifikasi->find($id);

		$this->template->title('List Notifikasi Update');
		$this->render('backend/standart/administrator/list_notifikasi/list_notifikasi_update', $this->data);
	}

	/**
	* Update List Notifikasis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('list_notifikasi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('content', 'Content', 'trim|required');
		$this->form_validation->set_rules('role', 'Role', 'trim|required|max_length[25]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('user_id', 'User Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'title' => $this->input->post('title'),
				'content' => $this->input->post('content'),
				'role' => $this->input->post('role'),
				'jenjang' => $this->input->post('jenjang'),
				'user_id' => $this->input->post('user_id'),
				'created_at' => $this->input->post('created_at'),
				'action' => $this->input->post('action'),
			];

			
			$save_list_notifikasi = $this->model_list_notifikasi->change($id, $save_data);

			if ($save_list_notifikasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/list_notifikasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/list_notifikasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/list_notifikasi');
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
	* delete List Notifikasis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('list_notifikasi_delete');

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
            set_message(cclang('has_been_deleted', 'list_notifikasi'), 'success');
        } else {
            set_message(cclang('error_delete', 'list_notifikasi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view List Notifikasis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('list_notifikasi_view');

		$this->data['list_notifikasi'] = $this->model_list_notifikasi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('List Notifikasi Detail');
		$this->render('backend/standart/administrator/list_notifikasi/list_notifikasi_view', $this->data);
	}
	
	/**
	* delete List Notifikasis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$list_notifikasi = $this->model_list_notifikasi->find($id);

		
		
		return $this->model_list_notifikasi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('list_notifikasi_export');

		$this->model_list_notifikasi->export('list_notifikasi', 'list_notifikasi');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('list_notifikasi_export');

		$this->model_list_notifikasi->pdf('list_notifikasi', 'list_notifikasi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('list_notifikasi_export');

		$table = $title = 'list_notifikasi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_list_notifikasi->find($id);
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


/* End of file list_notifikasi.php */
/* Location: ./application/controllers/administrator/List Notifikasi.php */