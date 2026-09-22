<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Employe Controller
*| --------------------------------------------------------------------------
*| Employe site
*|
*/
class Employe extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_employe');
	}

	/**
	* show all Employes
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('employe_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['employes'] = $this->model_employe->get($filter, $field, $this->limit_page, $offset);
		$this->data['employe_counts'] = $this->model_employe->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/employe/index/',
			'total_rows'   => $this->model_employe->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Employe List');
		$this->render('backend/standart/administrator/employe/employe_list', $this->data);
	}
	
	/**
	* Add new employes
	*
	*/
	public function add()
	{
		$this->is_allowed('employe_add');

		$this->template->title('Employe New');
		$this->render('backend/standart/administrator/employe/employe_add', $this->data);
	}

	/**
	* Add New Employes
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('employe_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('company', 'Company', 'trim|required');
		$this->form_validation->set_rules('position[]', 'Position', 'trim|required');
		$this->form_validation->set_rules('head_id', 'Head Id', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'company' => $this->input->post('company'),
				'position' => implode(',', (array) $this->input->post('position')),
				'head_id' => $this->input->post('head_id'),
				'sub_id' => $this->input->post('sub_id'),
				'created_at' => date('Y-m-d H:i:s'),
			];

			
			$save_employe = $this->model_employe->store($save_data);

			if ($save_employe) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_employe;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/employe/edit/' . $save_employe, 'Edit Employe'),
						anchor('administrator/employe', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/employe/edit/' . $save_employe, 'Edit Employe')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/employe');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/employe');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Employes
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('employe_update');

		$this->data['employe'] = $this->model_employe->find($id);

		$this->template->title('Employe Update');
		$this->render('backend/standart/administrator/employe/employe_update', $this->data);
	}

	/**
	* Update Employes
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('employe_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('company', 'Company', 'trim|required');
		$this->form_validation->set_rules('position[]', 'Position', 'trim|required');
		$this->form_validation->set_rules('head_id', 'Head Id', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'company' => $this->input->post('company'),
				'position' => implode(',', (array) $this->input->post('position')),
				'head_id' => $this->input->post('head_id'),
				'sub_id' => $this->input->post('sub_id'),
				'created_at' => date('Y-m-d H:i:s'),
			];

			
			$save_employe = $this->model_employe->change($id, $save_data);

			if ($save_employe) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/employe', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/employe');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/employe');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Employes
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('employe_delete');

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
            set_message(cclang('has_been_deleted', 'employe'), 'success');
        } else {
            set_message(cclang('error_delete', 'employe'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Employes
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('employe_view');

		$this->data['employe'] = $this->model_employe->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Employe Detail');
		$this->render('backend/standart/administrator/employe/employe_view', $this->data);
	}
	
	/**
	* delete Employes
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$employe = $this->model_employe->find($id);

		
		
		return $this->model_employe->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('employe_export');

		$this->model_employe->export('employe', 'employe');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('employe_export');

		$this->model_employe->pdf('employe', 'employe');
	}
}


/* End of file employe.php */
/* Location: ./application/controllers/administrator/Employe.php */