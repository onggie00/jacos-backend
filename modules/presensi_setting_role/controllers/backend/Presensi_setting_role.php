<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Setting Role Controller
*| --------------------------------------------------------------------------
*| Presensi Setting Role site
*|
*/
class Presensi_setting_role extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_setting_role');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Setting Roles
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_setting_role_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_setting_roles'] = $this->model_presensi_setting_role->get($filter, $field, $this->limit_page, $offset);
		$this->data['presensi_setting_role_counts'] = $this->model_presensi_setting_role->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/presensi_setting_role/index/',
			'total_rows'   => $this->model_presensi_setting_role->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Setting Role List');
		$this->render('backend/standart/administrator/presensi_setting_role/presensi_setting_role_list', $this->data);
	}
	
	/**
	* Add new presensi_setting_roles
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_setting_role_add');

		$this->template->title('Presensi Setting Role New');
		$this->render('backend/standart/administrator/presensi_setting_role/presensi_setting_role_add', $this->data);
	}

	/**
	* Add New Presensi Setting Roles
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_setting_role_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_role', 'Role', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('head_role', 'Kepala Role', 'trim|max_length[50]');
		$this->form_validation->set_rules('nama_tabel', 'Nama Tabel', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_role' => $this->input->post('nama_role'),
				'head_role' => $this->input->post('head_role'),
				'nama_tabel' => $this->input->post('nama_tabel'),
				'updated_by' => $this->input->post('updated_by'),
				'updated_at' => $this->input->post('updated_at'),
			];

			
			$save_presensi_setting_role = $this->model_presensi_setting_role->store($save_data);
            

			if ($save_presensi_setting_role) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_setting_role;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_setting_role/edit/' . $save_presensi_setting_role, 'Edit Presensi Setting Role'),
						anchor('administrator/presensi_setting_role', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_setting_role/edit/' . $save_presensi_setting_role, 'Edit Presensi Setting Role')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_setting_role');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_setting_role');
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
	* Update view Presensi Setting Roles
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_setting_role_update');

		$this->data['presensi_setting_role'] = $this->model_presensi_setting_role->find($id);

		$this->template->title('Presensi Setting Role Update');
		$this->render('backend/standart/administrator/presensi_setting_role/presensi_setting_role_update', $this->data);
	}

	/**
	* Update Presensi Setting Roles
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_setting_role_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_role', 'Role', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('head_role', 'Kepala Role', 'trim|max_length[50]');
		$this->form_validation->set_rules('nama_tabel', 'Nama Tabel', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_role' => $this->input->post('nama_role'),
				'head_role' => $this->input->post('head_role'),
				'nama_tabel' => $this->input->post('nama_tabel'),
				'updated_by' => $this->input->post('updated_by'),
				'updated_at' => $this->input->post('updated_at'),
			];

			
			$save_presensi_setting_role = $this->model_presensi_setting_role->change($id, $save_data);

			if ($save_presensi_setting_role) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_setting_role', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_setting_role');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_setting_role');
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
	* delete Presensi Setting Roles
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_setting_role_delete');

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
            set_message(cclang('has_been_deleted', 'presensi_setting_role'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_setting_role'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Setting Roles
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_setting_role_view');

		$this->data['presensi_setting_role'] = $this->model_presensi_setting_role->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi Setting Role Detail');
		$this->render('backend/standart/administrator/presensi_setting_role/presensi_setting_role_view', $this->data);
	}
	
	/**
	* delete Presensi Setting Roles
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_setting_role = $this->model_presensi_setting_role->find($id);

		
		
		return $this->model_presensi_setting_role->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('presensi_setting_role_export');

		$this->model_presensi_setting_role->export('presensi_setting_role', 'presensi_setting_role');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_setting_role_export');

		$this->model_presensi_setting_role->pdf('presensi_setting_role', 'presensi_setting_role');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_setting_role_export');

		$table = $title = 'presensi_setting_role';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_setting_role->find($id);
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


/* End of file presensi_setting_role.php */
/* Location: ./application/controllers/administrator/Presensi Setting Role.php */