<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Submission Type Controller
*| --------------------------------------------------------------------------
*| Presensi Submission Type site
*|
*/
class Presensi_submission_type extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_submission_type');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Submission Types
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_submission_type_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_submission_types'] = $this->model_presensi_submission_type->get($filter, $field, $this->limit_page, $offset);
		$this->data['presensi_submission_type_counts'] = $this->model_presensi_submission_type->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/presensi_submission_type/index/',
			'total_rows'   => $this->model_presensi_submission_type->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Submission Type List');
		$this->render('backend/standart/administrator/presensi_submission_type/presensi_submission_type_list', $this->data);
	}
	
	/**
	* Add new presensi_submission_types
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_submission_type_add');

		$this->template->title('Presensi Submission Type New');
		$this->render('backend/standart/administrator/presensi_submission_type/presensi_submission_type_add', $this->data);
	}

	/**
	* Add New Presensi Submission Types
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_submission_type_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Jenis', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('code', 'Kode', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('file_required', 'File Wajib?', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
				'file_required' => $this->input->post('file_required'),
			];

			
			$save_presensi_submission_type = $this->model_presensi_submission_type->store($save_data);
            

			if ($save_presensi_submission_type) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_submission_type;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_submission_type/edit/' . $save_presensi_submission_type, 'Edit Presensi Submission Type'),
						anchor('administrator/presensi_submission_type', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_submission_type/edit/' . $save_presensi_submission_type, 'Edit Presensi Submission Type')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_submission_type');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_submission_type');
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
	* Update view Presensi Submission Types
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_submission_type_update');

		$this->data['presensi_submission_type'] = $this->model_presensi_submission_type->find($id);

		$this->template->title('Presensi Submission Type Update');
		$this->render('backend/standart/administrator/presensi_submission_type/presensi_submission_type_update', $this->data);
	}

	/**
	* Update Presensi Submission Types
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_submission_type_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Jenis', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('code', 'Kode', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('file_required', 'File Wajib?', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
				'file_required' => $this->input->post('file_required'),
			];

			
			$save_presensi_submission_type = $this->model_presensi_submission_type->change($id, $save_data);

			if ($save_presensi_submission_type) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_submission_type', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_submission_type');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_submission_type');
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
	* delete Presensi Submission Types
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_submission_type_delete');

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
            set_message(cclang('has_been_deleted', 'presensi_submission_type'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_submission_type'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Submission Types
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_submission_type_view');

		$this->data['presensi_submission_type'] = $this->model_presensi_submission_type->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi Submission Type Detail');
		$this->render('backend/standart/administrator/presensi_submission_type/presensi_submission_type_view', $this->data);
	}
	
	/**
	* delete Presensi Submission Types
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_submission_type = $this->model_presensi_submission_type->find($id);

		
		
		return $this->model_presensi_submission_type->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('presensi_submission_type_export');

		$this->model_presensi_submission_type->export('presensi_submission_type', 'presensi_submission_type');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_submission_type_export');

		$this->model_presensi_submission_type->pdf('presensi_submission_type', 'presensi_submission_type');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_submission_type_export');

		$table = $title = 'presensi_submission_type';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_submission_type->find($id);
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


/* End of file presensi_submission_type.php */
/* Location: ./application/controllers/administrator/Presensi Submission Type.php */