<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Activity User Controller
*| --------------------------------------------------------------------------
*| Activity User site
*|
*/
class Activity_user extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_activity_user');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Activity Users
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('activity_user_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['activity_users'] = $this->model_activity_user->get($filter, $field, $this->limit_page, $offset);
		$this->data['activity_user_counts'] = $this->model_activity_user->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/activity_user/index/',
			'total_rows'   => $this->model_activity_user->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		// Recent activities for notification bar
		$this->data['recent_activities'] = $this->model_activity_user->get_recent(3, 10);

		$this->template->title('Activity User List');
		$this->render('backend/standart/administrator/activity_user/activity_user_list', $this->data);
	}
	
	/**
	* Add new activity_users
	*
	*/
	public function add()
	{
		$this->is_allowed('activity_user_add');

		$this->template->title('Activity User New');
		$this->render('backend/standart/administrator/activity_user/activity_user_add', $this->data);
	}

	/**
	* Add New Activity Users
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('activity_user_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('value', 'Isian', 'trim|required');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|required|max_length[150]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'keterangan' => $this->input->post('keterangan'),
				'value' => $this->input->post('value'),
				'created_at' => $this->input->post('created_at'),
				'updated_by' => $this->input->post('updated_by'),
			];

			
			$save_activity_user = $this->model_activity_user->store($save_data);
            

			if ($save_activity_user) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_activity_user;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/activity_user/edit/' . $save_activity_user, 'Edit Activity User'),
						anchor('administrator/activity_user', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/activity_user/edit/' . $save_activity_user, 'Edit Activity User')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/activity_user');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/activity_user');
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
	* Update view Activity Users
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('activity_user_update');

		$this->data['activity_user'] = $this->model_activity_user->find($id);

		$this->template->title('Activity User Update');
		$this->render('backend/standart/administrator/activity_user/activity_user_update', $this->data);
	}

	/**
	* Update Activity Users
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('activity_user_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('value', 'Isian', 'trim|required');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|required|max_length[150]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'keterangan' => $this->input->post('keterangan'),
				'value' => $this->input->post('value'),
				'created_at' => $this->input->post('created_at'),
				'updated_by' => $this->input->post('updated_by'),
			];

			
			$save_activity_user = $this->model_activity_user->change($id, $save_data);

			if ($save_activity_user) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/activity_user', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/activity_user');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/activity_user');
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
	* delete Activity Users
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('activity_user_delete');

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
            set_message(cclang('has_been_deleted', 'activity_user'), 'success');
        } else {
            set_message(cclang('error_delete', 'activity_user'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Activity Users
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('activity_user_view');

		$this->data['activity_user'] = $this->model_activity_user->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Activity User Detail');
		$this->render('backend/standart/administrator/activity_user/activity_user_view', $this->data);
	}
	
	/**
	* delete Activity Users
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$activity_user = $this->model_activity_user->find($id);

		
		
		return $this->model_activity_user->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('activity_user_export');

		$this->model_activity_user->export('activity_user', 'activity_user');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('activity_user_export');

		$this->model_activity_user->pdf('activity_user', 'activity_user');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('activity_user_export');

		$table = $title = 'activity_user';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_activity_user->find($id);
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


/* End of file activity_user.php */
/* Location: ./application/controllers/administrator/Activity User.php */