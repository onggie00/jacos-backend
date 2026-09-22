<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jenis Jenjang Controller
*| --------------------------------------------------------------------------
*| Jenis Jenjang site
*|
*/
class Jenis_jenjang extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jenis_jenjang');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jenis Jenjangs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jenis_jenjang_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jenis_jenjangs'] = $this->model_jenis_jenjang->get($filter, $field, $this->limit_page, $offset);
		$this->data['jenis_jenjang_counts'] = $this->model_jenis_jenjang->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jenis_jenjang/index/',
			'total_rows'   => $this->model_jenis_jenjang->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jenis Jenjang List');
		$this->render('backend/standart/administrator/jenis_jenjang/jenis_jenjang_list', $this->data);
	}
	
	/**
	* Add new jenis_jenjangs
	*
	*/
	public function add()
	{
		$this->is_allowed('jenis_jenjang_add');

		$this->template->title('Jenis Jenjang New');
		$this->render('backend/standart/administrator/jenis_jenjang/jenis_jenjang_add', $this->data);
	}

	/**
	* Add New Jenis Jenjangs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jenis_jenjang_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenis_jenjang', 'Jenis Jenjang', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenis_jenjang' => $this->input->post('jenis_jenjang'),
			];

			
			$save_jenis_jenjang = $this->model_jenis_jenjang->store($save_data);
            

			if ($save_jenis_jenjang) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jenis_jenjang;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jenis_jenjang/edit/' . $save_jenis_jenjang, 'Edit Jenis Jenjang'),
						anchor('administrator/jenis_jenjang', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jenis_jenjang/edit/' . $save_jenis_jenjang, 'Edit Jenis Jenjang')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_jenjang');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_jenjang');
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
	* Update view Jenis Jenjangs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jenis_jenjang_update');

		$this->data['jenis_jenjang'] = $this->model_jenis_jenjang->find($id);

		$this->template->title('Jenis Jenjang Update');
		$this->render('backend/standart/administrator/jenis_jenjang/jenis_jenjang_update', $this->data);
	}

	/**
	* Update Jenis Jenjangs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jenis_jenjang_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenis_jenjang', 'Jenis Jenjang', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenis_jenjang' => $this->input->post('jenis_jenjang'),
			];

			
			$save_jenis_jenjang = $this->model_jenis_jenjang->change($id, $save_data);

			if ($save_jenis_jenjang) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jenis_jenjang', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_jenjang');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_jenjang');
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
	* delete Jenis Jenjangs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jenis_jenjang_delete');

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
            set_message(cclang('has_been_deleted', 'jenis_jenjang'), 'success');
        } else {
            set_message(cclang('error_delete', 'jenis_jenjang'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jenis Jenjangs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jenis_jenjang_view');

		$this->data['jenis_jenjang'] = $this->model_jenis_jenjang->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jenis Jenjang Detail');
		$this->render('backend/standart/administrator/jenis_jenjang/jenis_jenjang_view', $this->data);
	}
	
	/**
	* delete Jenis Jenjangs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jenis_jenjang = $this->model_jenis_jenjang->find($id);

		
		
		return $this->model_jenis_jenjang->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jenis_jenjang_export');

		$this->model_jenis_jenjang->export('jenis_jenjang', 'jenis_jenjang');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jenis_jenjang_export');

		$this->model_jenis_jenjang->pdf('jenis_jenjang', 'jenis_jenjang');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jenis_jenjang_export');

		$table = $title = 'jenis_jenjang';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jenis_jenjang->find($id);
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


/* End of file jenis_jenjang.php */
/* Location: ./application/controllers/administrator/Jenis Jenjang.php */